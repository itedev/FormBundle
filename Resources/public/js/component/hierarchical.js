(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  $.fn.hierarchical = function(option) {
    var value;
    this.each(function() {
      var $this = $(this);

      if ('active' === option) {
        value = 'undefined' !== typeof $this.data('hierarchicalJqxhr');
      } else {
        $.error('Method with name "' +  option + '" does not exist in jQuery.hierarchical');
      }
    });

    return ('undefined' === typeof value) ? this : value;
  };

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

    getFullName: function($element, element) {
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

      var elementFullName = SF.util.getFullName($element, element);

      var eventData = {
        originator: elementFullName,
        children: {}
      };
      var $childrenElements = {};
      var childrenCount = 0;
      $.each(element.getHierarchicalChildren(), function(i, childSelector) {
        var $childElement = SF.elements.getJQueryElement(childSelector, context, replacementTokens);
        $childrenElements[childSelector] = $childElement;

        if ($childElement.length) {
          eventData.children['#' + $childElement.attr('id')] = $childElement.get(0);
          childrenCount++;
        }
      });

      if (!childrenCount) {
        return;
      }

      var event = $.Event('ite-before-submit.hierarchical', eventData);
      $element.trigger(event);
      if (false === event.result) {
        return;
      }
//
//      // clear children value
//      $.each(SF.elements.getHierarchicalChildrenRecursive(selector), function(index, childSelector) {
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

      var $form = $element.closest('form');
      var jqxhr = $form.data('hierarchicalJqxhr');
      if (jqxhr) {
        jqxhr.abort('hierarchicalAbort');
      }
      jqxhr = $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: $form.serialize(),
        dataType: 'html',
        headers: {
          'X-SF-Hierarchical': '1',
          'X-SF-Hierarchical-Originator': elementFullName
        },
        success: function(response) {
          $form.removeData('hierarchicalJqxhr');

          var newContext = $(response);
          event = $.Event('ite-after-submit.hierarchical', eventData);
          $element.trigger(event, [newContext]);
          if (false === event.result) {
            return;
          }

          $.each(element.getHierarchicalChildren(), function(i, childSelector) {
            var childElement = SF.elements.get(childSelector);
            var $childElement = $childrenElements[childSelector];
            var $newChildElement = SF.elements.getJQueryElement(childSelector, newContext, replacementTokens);

            if (!$childElement.length) {
              return;
            }

            // set element value
            var childEventData = {
              relatedTarget: $newChildElement.get(0)
            };
            event = $.Event('ite-before-change.hierarchical', childEventData);
            $childElement.trigger(event, [newContext]);
            if (false === event.result) {
              return;
            }

            SF.elements.setElementValue($childElement, $newChildElement, childElement);

            event = $.Event('ite-after-change.hierarchical', childEventData);
            $element.trigger(event, [newContext]);
          });

          event = $.Event('ite-after-children-change.hierarchical', eventData);
          $element.trigger(event, [newContext]);
        }
      });
      jqxhr.fail(function() {
        if (0 !== jqxhr.readyState || 'hierarchicalAbort' !== jqxhr.statusText) {
          $form.removeData('hierarchicalJqxhr');
        }

        event = $.Event('ite-after-submit.hierarchical', eventData);
        $element.trigger(event);
      });
      $form.data('hierarchicalJqxhr', jqxhr);
    }
  });

  SF.fn.classes.Element.prototype = $.extend(SF.fn.classes.Element.prototype, {
    getHierarchicalParents: function() {
      return this.getOption('hierarchical_parents', []);
    },

    hasHierarchicalParents: function() {
      return this.getHierarchicalParents().length > 0;
    },

    getHierarchicalChildren: function() {
      return this.getOption('hierarchical_children', []);
    },

    hasHierarchicalChildren: function() {
      return this.getHierarchicalChildren().length > 0;
    },

    hasHierarchicalChild: function(child) {
      return this.hasOption('hierarchical_children') && -1 !== $.inArray(child, this.getHierarchicalChildren());
    },

    addHierarchicalChild: function(child) {
      if (!this.hasHierarchicalChild(child)) {
        if (!this.hasOption('hierarchical_children')) {
          this.options['hierarchical_children'] = [];
        }
        this.options['hierarchical_children'].push(child);
      }
    },

    hierarchicalChildrenCount: function() {
      return this.getHierarchicalChildren().length;
    },

    isCompound: function() {
      return this.getOption('compound', false);
    },

    hasDelegateSelector: function() {
      return this.hasOption('delegate_selector');
    },

    getDelegateSelector: function() {
      return this.getOption('delegate_selector');
    },

    hasHierarchicalAutoInitialize: function() {
      return this.hasOption('hierarchical_auto_initialize');
    },

    isHierarchicalOriginator: function() {
      return this.getOption('hierarchical_originator', false);
    },

    hasHierarchicalTriggerEvent: function() {
      return this.hasOption('hierarchical_trigger_event');
    },

    getHierarchicalTriggerEvent: function() {
      return this.getOption('hierarchical_trigger_event');
    }

  });

  var baseApply = SF.fn.classes.ElementBag.prototype.apply;
  var baseBeforeAdd = SF.fn.classes.ElementBag.prototype.beforeAdd;
  SF.fn.classes.ElementBag.prototype = $.extend(SF.fn.classes.ElementBag.prototype, {

    beforeAdd: function(selector, options) {
      baseBeforeAdd.apply(this, [selector, options]);

      var self = this;
      if (options.hasOwnProperty('hierarchical_parents')) {
        $.each(options['hierarchical_parents'], function(index, parentSelector) {
          self.get(parentSelector).addHierarchicalChild(selector);
        });
      }
    },

    getHierarchicalParentsRecursive: function(selector) {
      var self = this;

      var parents = this.get(selector).getHierarchicalParents();
      $.each(parents, function(i, parent) {
        parents = parents.concat(self.getHierarchicalParentsRecursive(parent));
      });

      return SF.util.arrayUnique(parents);
    },

    getHierarchicalChildrenRecursive: function(selector) {
      var self = this;

      var children = this.get(selector).getHierarchicalChildren();
      $.each(children, function(i, child) {
        children = children.concat(self.getHierarchicalChildrenRecursive(child));
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

    setElementValue: function($element, $newElement, element) {
      if (!element.hasPlugins()) {
        if (!element.isCompound()) {
          var node = $element.get(0);
          if (rxText.test(node.nodeName)) {
            $element.val($newElement.val());
          } else if (rxSelect.test(node.nodeName)) {
            $element.html($newElement.html());
            $element.val($newElement.val());
          }
          $element.html($newElement.html());
        } else {
          $element.html($newElement.html());
        }
      } else {
        $.each(element.getPlugins(), function(i, plugin) {
          if ('undefined' !== typeof SF.plugins[plugin].setValue) {
            SF.plugins[plugin].setValue($element, $newElement);

            return false; // break
          }
        });
      }

      if (element.hasHierarchicalTriggerEvent()) {
        $element.trigger(element.getHierarchicalTriggerEvent());
      }
    },

    apply: function(context, replacementTokens) {
      baseApply.apply(this, [context, replacementTokens]);

      var self = this;
//      var $parentsToChange = [];
      $.each(this.elements, function(selector, element) {
        if (!element.hasHierarchicalChildren() && !element.isHierarchicalOriginator()) {
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
        } else {
          $element.on('change.hierarchical', data, SF.callbacks.hierarchicalChange);
        }

        $element.data('hierarchical', true);
      });

//        $.each(element.getHierarchicalParents(), function(i, parentSelector) {
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

//          $.each(parentElement.getHierarchicalChildren(), function(j, childSelector) {
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