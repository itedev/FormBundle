(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  SF.plugins = {};

  SF.util = $.extend(SF.util, {
    addGetParameter: function(url, paramName, paramValue) {
      var urlParts = url.split('?', 2);
      var baseURL = urlParts[0];
      var queryString = [];
      if (urlParts.length > 1) {
        var parameters = urlParts[1].split('&');
        for (var i = 0; i < parameters.length; ++i) {
          if (parameters[i].split('=')[0] != paramName) {
            queryString.push(parameters[i]);
          }
        }
      }
      queryString.push(paramName + '=' + encodeURIComponent(paramValue));

      return baseURL + '?' + queryString.join('&');
    },

    strtr: function(str, replacementTokens) {
      if ('object' === typeof replacementTokens) {
        $.each(replacementTokens, function(from, to) {
          str = str.replace(new RegExp(from, 'g'), to);
        });
      }
      return str;
    },

    mapRecursive: function(array, callback) {
      $.each(array, function(key, value) {
        if ('object' === typeof value || 'array' === typeof value) {
          array[key] = SF.util.mapRecursive(value, callback);
        } else {
          array[key] = callback.call(null, value);
        }
      });

      return array;
    },

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
    },

    hasEvent: function(element, event) {
      var eventName, eventNamespace, i;
      if (-1 === event.indexOf('.')){
        eventName = event;
        eventNamespace = '';
      } else {
        eventName = event.substring(0, event.indexOf('.'));
        eventNamespace = event.substring(event.indexOf('.') + 1);
      }

      var events = $._data(element[0], 'events');
      if ('undefined' === typeof events || 0 === events.length || !events.hasOwnProperty(eventName)) {
        return false;
      }
      if (!eventNamespace.length) {
        return true;
      }
      for (i in events[eventName]) {
        var eventOptions = events[eventName][i];
        if (eventOptions['namespace'] === eventNamespace) {
          return true;
        }
      }
      return false;
    },

    objectLength: function(obj) {
      var size = 0;

      for (var key in obj) {
        if (obj.hasOwnProperty(key)) {
          size++;
        }
      }

      return size;
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
        $.ajax({
          type: 'post',
          url: element.getHierarchicalUrl(),
          data: data,
          dataType: 'html',
          success: function(value) {
            // set element value
            SF.elements.setElementValue(element, $element, value);
          }
        });
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

  // Element
  var Element = function(selector, plugins, parents, options) {
    this.selector = selector;
    this.plugins = plugins || {};
    this.parents = parents || [];
    this.children = [];
    this.options = options || {};
  };
  Element.prototype = {
    getSelector: function() {
      return this.selector;
    },

    getPlugins: function() {
      var plugins = [];

      $.each(this.plugins, function(plugin, pluginData) {
        plugins.push(plugin);
      });

      return plugins;
    },

    hasPlugins: function() {
      return SF.util.objectLength(this.plugins) > 0;
    },

    hasPlugin: function(plugin) {
      return this.plugins.hasOwnProperty(plugin);
    },

    getPluginData: function(plugin) {
      if (!this.hasPlugin(plugin)) {
        return null;
      }

      return this.plugins[plugin];
    },

    getParents: function() {
      return this.parents;
    },

    hasParents: function() {
      return this.parents.length > 0;
    },

    getChildren: function() {
      return this.children;
    },

    hasChildren: function() {
      return this.children.length > 0;
    },

    getOptions: function() {
      return this.options;
    },

    hasOption: function(option) {
      return this.options.hasOwnProperty(option);
    },

    getOption: function(option, defaultValue) {
      defaultValue = defaultValue || null;
      return this.hasOption(option) ? this.options[option] : defaultValue;
    },

    hasChild: function(child) {
      return -1 !== $.inArray(child, this.children);
    },

    addChild: function(child) {
      if (!this.hasChild(child)) {
        this.children.push(child);
      }
    },

    hasChildrenSelector: function() {
      return this.hasOption('children_selector');
    },

    getChildrenSelector: function() {
      return this.getOption('children_selector');
    },

    getFullSelector: function() {
      if (this.hasChildrenSelector()) {
        return this.selector + ' ' + this.getChildrenSelector();
      }

      return this.selector;
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
  };

  // ElementBag
  var ElementBag = function() {
    this.plugins = {};
    this.elements = {};
  };
  ElementBag.prototype = {
    hasPlugin: function(plugin) {
      return this.plugins.hasOwnProperty(plugin);
    },

    getPlugins: function() {
      var plugins = [];

      $.each(this.plugins, function(i, plugin) {
        plugins.push(plugin);
      });

      return plugins;
    },

    getPluginElements: function(plugin) {
      return this.hasPlugin(plugin) ? this.plugins[plugin] : [];
    },

    getElements: function() {
      var elements = [];

      $.each(this.elements, function(i, element) {
        elements.push(element);
      });

      return elements;
    },

    has: function(selector) {
      return this.elements.hasOwnProperty(selector);
    },

    get: function(selector, defaultValue) {
      defaultValue = defaultValue || null;
      return this.has(selector) ? this.elements[selector] : defaultValue;
    },

    getAllParents: function(selector) {
      var self = this;

      var parents = this.get(selector).getParents();
      $.each(parents, function(i, parent) {
        parents = parents.concat(self.getAllParents(parent));
      });

      return parents;
    },

    getAllChildren: function(selector) {
      var self = this;

      var children = this.get(selector).getChildren();
      $.each(children, function(i, child) {
        children = children.concat(self.getAllChildren(child));
      });

      return children;
    },

    clearElementValue: function(element, $element) {
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

      $element.trigger('clear.hierarchical.ite-form');
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

      $element.trigger('change.hierarchical.ite-form');
    },

    getJQueryElement: function(selector, context, replacementTokens) {
      var elementSelector = SF.util.strtr(selector, replacementTokens);

      return $(elementSelector, context);
    },

    add: function(selector, elementData) {
      if (this.has(selector)) {
        return;
      }

      var self = this;

      // add plugins
      $.each(elementData['plugins'], function(plugin, pluginData) {
        if (!self.hasPlugin(plugin)) {
          self.plugins[plugin] = [];
        }
        self.plugins[plugin].push(selector);
      });

      // add parent elements
      $.each(elementData['parents'], function(index, parentSelector) {
        if (!self.has(parentSelector)) {
          self.elements[parentSelector] = new Element(parentSelector);
        }
        self.get(parentSelector).addChild(selector);
      });

      // add element
      this.elements[selector] = new Element(
        selector,
        elementData['plugins'],
        elementData['parents'],
        elementData['options']
      );
    },

    set: function(elements) {
      var self = this;

      $.each(elements, function(selector, elementData) {
        self.add(selector, elementData);
      });
    },

    applyPlugins: function(context, replacementTokens) {
      var self = this;
      $.each(this.plugins, function(plugin, selectors) {
        if ('undefined' === typeof SF.plugins[plugin]) {
          return;
        }
        $.each(selectors, function(index, selector) {
          var $element = self.getJQueryElement(selector, context, replacementTokens);
          if (!$element.length) {
            return;
          }

          if ('undefined' === typeof SF.plugins[plugin].isApplied || SF.plugins[plugin].isApplied($element)) {
            return;
          }
          if ('undefined' !== typeof SF.plugins[plugin].apply) {
            var pluginData = self.get(selector).getPluginData(plugin);
            $element.trigger('apply.plugin.ite-form', [pluginData]);
            SF.plugins[plugin].apply($element, pluginData);
          }
        });
      });
    },

    applyHierarchical: function(context, replacementTokens) {
      var self = this;
      $.each(this.elements, function(selector, elementObject) {
        if (!elementObject.hasParents()) {
          return;
        }

        $.each(elementObject.getParents(), function(i, parentSelector) {
          var $parent = self.getJQueryElement(parentSelector, context, replacementTokens);
          if (!$parent.length || SF.util.hasEvent($parent, 'change.hierarchical.ite-form')) {
            return;
          }

          var parentElement = self.get(parentSelector);
          var data = {
            selector: selector,
            context: context,
            replacementTokens: replacementTokens
          };

          if (parentElement.hasChildrenSelector()) {
            $parent.on('change.hierarchical.ite-form', parentElement.getChildrenSelector(), data, SF.callbacks.hierarchicalChange);
          } else {
            $parent.on('change.hierarchical.ite-form', data, SF.callbacks.hierarchicalChange);
          }
        });
      });
    },

    apply: function(context, replacementTokens) {
      this.applyPlugins(context, replacementTokens);
      this.applyHierarchical(context, replacementTokens);
    }
  };

  ElementBag.prototype.fn = ElementBag.prototype;
  SF.fn.elements = new ElementBag();

// http://stackoverflow.com/questions/5202296/add-a-hook-to-all-ajax-requests-on-a-page/5202312#5202312

})(jQuery);