(function($) {
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

  // Element
  var Element = function(selector, options) {
    this.selector = selector;
    this.options = options || {};
  };
  Element.prototype = {
    getSelector: function() {
      return this.selector;
    },

    getPlugins: function() {
      var plugins = [];

      if (this.hasPlugins()) {
        $.each(this.options['plugins'], function(plugin, pluginData) {
          plugins.push(plugin);
        });
      }

      return plugins;
    },

    hasPlugins: function() {
      return this.hasOption('plugins') && SF.util.objectLength(this.options['plugins']) > 0;
    },

    hasPlugin: function(plugin) {
      return this.hasOption('plugins') && this.options['plugins'].hasOwnProperty(plugin);
    },

    getPluginData: function(plugin) {
      if (!this.hasPlugin(plugin)) {
        return null;
      }

      return this.options['plugins'][plugin];
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
    }
  };

  Element.prototype.fn = Element.prototype;

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

    add: function(selector, options) {
      if (this.has(selector)) {
        return;
      }

      this.beforeAdd(selector, options);

      // add element
      this.elements[selector] = new Element(
        selector,
        options
      );
    },

    beforeAdd: function(selector, options) {
      var self = this;
      if (options.hasOwnProperty('plugins')) {
        $.each(options['plugins'], function(plugin, pluginData) {
          if (!self.hasPlugin(plugin)) {
            self.plugins[plugin] = [];
          }
          self.plugins[plugin].push(selector);
        });
      }
    },

    set: function(elements) {
      var self = this;

      $.each(elements, function(selector, elementData) {
        self.add(selector, elementData);
      });
    },

    getJQueryElement: function(selector, context, replacementTokens) {
      return $(SF.util.strtr(selector, replacementTokens), context);
    },

    apply: function(context, replacementTokens) {
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
            var event = $.Event('ite-before-apply.plugin');
            $element.trigger(event, [pluginData, plugin]);
            if (false === event.result) {
              return;
            }
            SF.plugins[plugin].apply($element, pluginData);
            $element.trigger('ite-apply.plugin', [pluginData, plugin]);
          }
        });
      });
    }
  };

  ElementBag.prototype.fn = ElementBag.prototype;

  SF.classes = $.extend(SF.classes, {
    Element: Element,
    ElementBag: ElementBag
  });

  SF.fn.elements = new ElementBag();

// http://stackoverflow.com/questions/5202296/add-a-hook-to-all-ajax-requests-on-a-page/5202312#5202312

})(jQuery);