(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  SF.util = $.extend(SF.util, {
    getSimpleName: function(element, $element) {
      var name;
      if (element.hasChildrenSelector()) {
        $element = $element.find(element.getChildrenSelector());
      }
      name = $element.attr('name');
      var re = /\[([^\]]+)\](?:|\[\])$/i;
      var matches = name.match(re);

      if (null === matches) {
        return null;
      }

      return matches[1];
    }
  });

  SF.callbacks = $.extend(SF.callbacks, {
    hierarchicalChange: function(e) {
      var selector = e.data.selector;
      var context = e.data.context;
      var replacementTokens = e.data.replacementTokens;

      // get parents data
      var data = {};
      $.each(SF.elements.getAllParents(selector), function(index, parentSelector) {
        var $parent = SF.elements.getJQueryElement(parentSelector, context, replacementTokens);
        if (!$parent.length) {
          return;
        }

        var parent = SF.elements.get(parentSelector);
        var name = SF.util.getSimpleName(parent, $parent);
        if (null === name) {
          return;
        }

        data[name] = SF.elements.getElementValue(parent, $parent);
      });

      // clear children value
      $.each(SF.elements.getAllChildren(selector), function(index, childSelector) {
        var $child = SF.elements.getJQueryElement(childSelector, context, replacementTokens);
        if (!$child.length) {
          return;
        }

        SF.elements.clearElementValue(SF.elements.get(childSelector), $child);
      });

      // clear element value
      var element = SF.elements.get(selector);
      var $element = SF.elements.getJQueryElement(selector, context, replacementTokens);
      SF.elements.clearElementValue(element, $element);

      if (element.hasHierarchicalUrl()) {
        // has url
        var jqxhr = $element.data('hierarchicalJqxhr');
        if (jqxhr) {
          jqxhr.abort();
        }
        jqxhr = $.ajax({
          type: 'post',
          url: element.getHierarchicalUrl(),
          data: data,
          dataType: 'html',
          success: function(value) {
            // set element value
            SF.elements.setElementValue(element, $element, value);
          }
        });
        $element.data('hierarchicalJqxhr', jqxhr);
      } else {
        // has callback
        var callback = element.getHierarchicalCallback();
        if ($.isFunction(window[callback])) {
          var value = window[callback].apply($element, [$element, data]);
          SF.elements.setElementValue(element, $element, value);
        }
      }
    }
  });

  SF.classes.Element.prototype = $.extend(SF.classes.Element.prototype, {

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

    hasChildrenSelector: function() {
      return this.hasOption('children_selector');
    },

    getChildrenSelector: function() {
      return this.getOption('children_selector');
    },

    hasHierarchicalUrl: function() {
      return this.hasOption('hierarchical_url');
    },

    getHierarchicalUrl: function() {
      return this.getOption('hierarchical_url');
    },

    hasHierarchicalCallback: function() {
      return this.hasOption('hierarchical_callback');
    },

    getHierarchicalCallback: function() {
      return this.getOption('hierarchical_callback');
    }
  });

  var baseApply = SF.classes.ElementBag.prototype.apply;
  var baseBeforeAdd = SF.classes.ElementBag.prototype.beforeAdd;
  SF.classes.ElementBag.prototype = $.extend(SF.classes.ElementBag.prototype, {

    beforeAdd: function(selector, options) {
      baseBeforeAdd.apply(this, [selector, options]);

      var self = this;
      if (options.hasOwnProperty('parents')) {
        $.each(options['parents'], function(index, parentSelector) {
          self.get(parentSelector).addChild(selector);
        });
      }
    },

    getAllParents: function(selector) {
      var self = this;

      var parents = this.get(selector).getParents();
      $.each(parents, function(i, parent) {
        parents = parents.concat(self.getAllParents(parent));
      });

      // @todo: add unique constraint

      return parents;
    },

    getAllChildren: function(selector) {
      var self = this;

      var children = this.get(selector).getChildren();
      $.each(children, function(i, child) {
        children = children.concat(self.getAllChildren(child));
      });

      // @todo: add unique constraint

      return children;
    },

    clearElementValue: function(element, $element) {
      var event = $.Event('ite-before-clear.hierarchical');
      $element.trigger(event);
      if (false !== event.result) {
        return;
      }

      if (element.hasChildrenSelector()) {
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
      if (element.hasChildrenSelector()) {
        var childrenSelector = element.getChildrenSelector();

        var values = [];
        $element.find(childrenSelector).filter(function() {
          return this.checked;
        }).each(function() {
            values.push($(this).val());
          });
        if ('input[type="radio"]' === childrenSelector) {
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

    setElementValue: function(element, $element, value) {
      var node;
      if (element.hasChildrenSelector()) {
        $element.html(value);
      } else {
        node = $element.get(0);
        if (rxText.test(node.nodeName)) {
          $element.val(value);
        } else if (rxSelect.test(node.nodeName)) {
          $element.html(value);
          var firstOption = $element.children('option:first');
          if (firstOption.length) {
            $element.val(firstOption.attr('value'));
          }
        }
      }

      if (element.hasPlugins()) {
        $.each(element.getPlugins(), function(i, plugin) {
          if ('undefined' !== typeof SF.plugins[plugin].setValue) {
            SF.plugins[plugin].setValue($element);
          }
        });
      }

      $element.trigger('change.hierarchical');
    },

    apply: function(context, replacementTokens) {
      baseApply.apply(this, [context, replacementTokens]);

      var self = this;
      $.each(this.elements, function(selector, elementObject) {
        if (!elementObject.hasParents()) {
          return;
        }

        $.each(elementObject.getParents(), function(i, parentSelector) {
          var $parent = self.getJQueryElement(parentSelector, context, replacementTokens);
          if (!$parent.length || SF.util.hasEvent($parent, 'change.hierarchical')) {
            return;
          }

          var parentElement = self.get(parentSelector);
          var data = {
            selector: selector,
            context: context,
            replacementTokens: replacementTokens
          };

          if (parentElement.hasChildrenSelector()) {
            $parent.on('change.hierarchical', parentElement.getChildrenSelector(), data, SF.callbacks.hierarchicalChange);
          } else {
            $parent.on('change.hierarchical', data, SF.callbacks.hierarchicalChange);
          }
        });
      });
    }
  });

})(jQuery);