(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  SF.fn.util = $.extend(SF.fn.util, {
    arrayUnique: function(arr) {
      return $.grep(arr, function(v, k) {
        return $.inArray(v ,arr) === k;
      });
    },

    getSimpleName: function(element, $element) {
      var name;
      if (element.hasDelegateSelector()) {
        $element = $element.find(element.getDelegateSelector());
      }
      name = $element.attr('name');
      var re = /\[([^\]]+)\](?:|\[\])$/i;
      var matches = name.match(re);

      if (null === matches) {
        return null;
      }

      return matches[1];
    },

    getFullName: function(element, $element) {
      if (element.hasDelegateSelector()) {
        $element = $element.find(element.getDelegateSelector());
      }
      return $element.attr('name');
    }
  });

  SF.fn.callbacks = $.extend(SF.fn.callbacks, {
    hierarchicalChange: function(e) {
      var selector = e.data.selector;
      var context = e.data.context;
      var replacementTokens = e.data.replacementTokens;

      var element = SF.elements.get(selector);
      var $element = SF.elements.getJQueryElement(selector, context, replacementTokens);

//      // get parents data
//      var data = {};
//      $.each(SF.elements.getParentsRecursive(selector), function(index, parentSelector) {
//        var $parent = SF.elements.getJQueryElement(parentSelector, context, replacementTokens);
//        if (!$parent.length) {
//          return;
//        }
//
//        var parent = SF.elements.get(parentSelector);
//        var name = SF.util.getSimpleName(parent, $parent);
//        if (null === name) {
//          return;
//        }
//
//        data[name] = SF.elements.getElementValue(parent, $parent);
//      });
//
//      // clear children value
//      $.each(SF.elements.getChildrenRecursive(selector), function(index, childSelector) {
//        var $child = SF.elements.getJQueryElement(childSelector, context, replacementTokens);
//        if (!$child.length) {
//          return;
//        }
//
//        SF.elements.clearElementValue(SF.elements.get(childSelector), $child);
//      });
//
//      // clear element value
//      SF.elements.clearElementValue(element, $element);

      var jqxhr = $element.data('hierarchicalJqxhr');
      if (jqxhr) {
        jqxhr.abort();
      }
      var $form = $element.closest('form');
      jqxhr = $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: $form.serialize(),
        dataType: 'html',
        headers: {
            'X-SF-Hierarchical': '1'
        },
        success: function(response) {
          var $content = $(response);

          $.each(element.getChildren(), function(i, childSelector) {
            var childElement = SF.elements.get(childSelector);
            var $childElement = SF.elements.getJQueryElement(childSelector, context, replacementTokens);
            var $newChildElement = SF.elements.getJQueryElement(childSelector, $content, replacementTokens);

            // set element value
            SF.elements.setElementValue($childElement, $newChildElement, childElement, $content);
          });
        }
      });
      $element.data('hierarchicalJqxhr', jqxhr);
    }
  });

  SF.fn.classes.Element.prototype = $.extend(SF.fn.classes.Element.prototype, {
    getParents: function() {
      return this.getOption('parents', []);
    },

    hasParents: function() {
      return this.getParents().length > 0;
    },

    getChildren: function() {
      return this.getOption('children', []);
    },

    hasChildren: function() {
      return this.getChildren().length > 0;
    },

    hasChild: function(child) {
      return this.hasOption('children') && -1 !== $.inArray(child, this.getChildren());
    },

    addChild: function(child) {
      if (!this.hasChild(child)) {
        if (!this.hasOption('children')) {
          this.options['children'] = [];
        }
        this.options['children'].push(child);
      }
    },

    childrenCount: function() {
      return this.getChildren().length;
    },

    hasDelegateSelector: function() {
      return this.hasOption('delegate_selector');
    },

    getDelegateSelector: function() {
      return this.getOption('delegate_selector');
    },

    hasHierarchicalAutoInitialize: function() {
      return this.hasOption('hierarchical_auto_initialize');
    }
  });

  var baseApply = SF.fn.classes.ElementBag.prototype.apply;
//  var baseBeforeAdd = SF.fn.classes.ElementBag.prototype.beforeAdd;
  SF.fn.classes.ElementBag.prototype = $.extend(SF.fn.classes.ElementBag.prototype, {

//    beforeAdd: function(selector, options) {
//      baseBeforeAdd.apply(this, [selector, options]);
//
//      var self = this;
//      if (options.hasOwnProperty('parents')) {
//        $.each(options['parents'], function(index, parentSelector) {
//          self.get(parentSelector).addChild(selector);
//        });
//      }
//    },

    getParentsRecursive: function(selector) {
      var self = this;

      var parents = this.get(selector).getParents();
      $.each(parents, function(i, parent) {
        parents = parents.concat(self.getParentsRecursive(parent));
      });

      return SF.util.arrayUnique(parents);
    },

    getChildrenRecursive: function(selector) {
      var self = this;

      var children = this.get(selector).getChildren();
      $.each(children, function(i, child) {
        children = children.concat(self.getChildrenRecursive(child));
      });

      return SF.util.arrayUnique(children);
    },

    clearElementValue: function(element, $element) {
      var event = $.Event('ite-before-clear.hierarchical');
      $element.trigger(event);
      if (false === event.result) {
        return;
      }

      if (element.hasDelegateSelector()) {
        $element.html('');
      } else {
        var node = $element.get(0);
        if (rxText.test(node.nodeName) && !rxCheckable.test(node.type)) {
          $element.val('');
        } else if (rxSelect.test(node.nodeName)) {
          $element.html('');
        }
      }

      if (element.hasPlugins()) {
        $.each(element.getPlugins(), function(i, plugin) {
          if ('undefined' !== typeof SF.plugins[plugin].clearValue) {
            SF.plugins[plugin].clearValue($element);
          }
        });
      }

      $element.trigger('ite-clear.hierarchical');
    },

    getElementValue: function(element, $element) {
      var node;
      if (element.hasDelegateSelector()) {
        var delegateSelector = element.getDelegateSelector();

        var values = [];
        $element.find(delegateSelector).filter(function() {
          return this.checked;
        }).each(function() {
          values.push($(this).val());
        });
        if ('input[type="radio"]' === delegateSelector) {
          return values.length ? values[0] : null;
        }
        return values;
      } else {
        node = $element.get(0);
        if (rxText.test(node.nodeName) || rxSelect.test(node.nodeName)) {
          return $element.val();
        }
      }

      return null;
    },

    setElementValue: function($element, $newElement, element, $content) {
      if (element.hasDelegateSelector()) {
        $element.html($newElement.html());
      } else {
        var node = $element.get(0);
        if (rxText.test(node.nodeName)) {
          $element.val($newElement.val());
        } else if (rxSelect.test(node.nodeName)) {
          $element.html($newElement.html());
          var firstOption = $element.children('option:first');
          if (firstOption.length) {
            $element.val(firstOption.attr('value'));
          }
        }
      }

      if (element.hasPlugins()) {
        $.each(element.getPlugins(), function(i, plugin) {
          if ('undefined' !== typeof SF.plugins[plugin].setValue) {
            SF.plugins[plugin].setValue($element, $newElement, $content);
          }
        });
      }

      $element.trigger('change.hierarchical');
    },

    apply: function(context, replacementTokens) {
      baseApply.apply(this, [context, replacementTokens]);

      var self = this;
//      var $parentsToChange = [];
      $.each(this.elements, function(selector, element) {
        if (!element.hasChildren()) {
          return;
        }

        var $element = self.getJQueryElement(selector, context, replacementTokens);
        if (!$element.length || 'undefined' !== typeof $element.data('hierarchical')) {
          return;
        }

        var data = {
          selector: selector,
          context: context,
          replacementTokens: replacementTokens
        };

        if (element.hasDelegateSelector()) {
          $element.on('change.hierarchical', element.getDelegateSelector(), data, SF.callbacks.hierarchicalChange);
        }
        else {
          $element.on('change.hierarchical', data, SF.callbacks.hierarchicalChange);
        }

        $element.data('hierarchical', true);
      });

//        $.each(element.getParents(), function(i, parentSelector) {
//          var $parent = self.getJQueryElement(parentSelector, context, replacementTokens);
//          if (!$parent.length || 'undefined' !== typeof $parent.data('hierarchical')) {
//            return;
//          }
//
//          var parentElement = self.get(parentSelector);
//          var data = {
//            selector: selector,
//            context: context,
//            replacementTokens: replacementTokens
//          };
//
//          if (parentElement.hasDelegateSelector()) {
//            $parent.on('change.hierarchical', parentElement.getDelegateSelector(), data, SF.callbacks.hierarchicalChange);
//          } else {
//            $parent.on('change.hierarchical', data, SF.callbacks.hierarchicalChange);
//          }

//          $.each(parentElement.getChildren(), function(j, childSelector) {
//            var childElement = self.get(childSelector);
//            if (childElement.hasHierarchicalAutoInitialize()) {
//              if (-1 === $.inArray($parent, $parentsToChange)) {
//                $parentsToChange.push($parent);
//              }
//            }
//          });
//
//          $parent.data('hierarchical', true);
//        });
//      });
//      $.each($parentsToChange, function(i, $parentToChange) {
//        $parentToChange.trigger('change.hierarchical');
//      });
    }
  });

})(jQuery);