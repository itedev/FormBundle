(function($) {

  $.fn.hasEvent = function(event) {
    var eventName, eventNamespace, i;
    if (-1 === event.indexOf('.')){
      eventName = event;
      eventNamespace = '';
    } else {
      eventName = event.substring(0, event.indexOf('.'));
      eventNamespace = event.substring(event.indexOf('.') + 1);
    }

    var events = $._data(this[0], 'events');
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
  };

  // ElementBag
  var ElementBag = function() {};
  ElementBag.prototype.fn = ElementBag.prototype;
  ElementBag = new ElementBag();

  ElementBag.fn.plugins = {};
  ElementBag.fn.elements = {};

  ElementBag.fn.hasPlugin = function(plugin) {
    return _.has(this.plugins, plugin);
  };

  ElementBag.fn.getPlugins = function() {
    return _.keys(this.plugins);
  };

  ElementBag.fn.getPluginElements = function(plugin) {
    return this.hasPlugin(plugin) ? this.plugins[plugin] : {};
  };

  ElementBag.fn.hasElement = function(selector) {
    return _.has(this.elements, selector);
  };

  ElementBag.fn.getElements = function() {
    return _.keys(this.elements);
  };

  ElementBag.fn.addElement = function(plugin, selector, data) {
    if (!this.hasPlugin(plugin)) {
      this.plugins[plugin] = {};
    }
    this.plugins[plugin][selector] = data;
    this.elements[selector] = plugin;
  };

  ElementBag.fn.getElementData = function(selector) {
    if (!this.hasElement(selector)) {
      return null;
    }
    return this.plugins[this.elements[selector]][selector];
  };

  ElementBag.fn.getElementOptions = function(selector) {
    var elementData = this.getElementData(selector);
    return elementData['options'];
  };

  ElementBag.fn.setElementOptions = function(selector, options) {
    if (!this.hasElement(selector)) {
      return;
    }
    this.plugins[this.elements[selector]][selector]['options'] = options;
  };

  ElementBag.fn.updateElementOptions = function(selector, callback) {
    var mixSelectors = selector instanceof Array ? selector : [selector];

    var allSelectors = this.getElements();

    var cleanSelectors = [];
    _.each(allSelectors, function(selector) {
      _.each(mixSelectors, function(mixSelector) {
        if (mixSelector instanceof RegExp) {
          if (mixSelector.test(selector)) {
            cleanSelectors.push(selector);
          }
        } else if ('string' === typeof mixSelector) {
          if (mixSelector === selector) {
            cleanSelectors.push(selector);
          }
        }
      });
    });

    _.each(cleanSelectors, function(selector) {
      var elementData = this.getElementData(selector);
      var options = callback.call(null, elementData);
      this.setElementOptions(selector, options);
    }, this);
  };

  ElementBag.fn.set = function(pluginElements) {
    var self = this;
    _.each(pluginElements, function(selectors, plugin) {
      _.each(selectors, function(options, selector) {
        self.addElement(plugin, selector, options);
      });
    });

    $(document).trigger('sf.element.set');
  };

  ElementBag.fn.apply = function(replacementTokens) {
    var plugin, selector, elementData, element;
    for (plugin in this.plugins) {
      for (selector in this.plugins[plugin]) {
        elementData = this.plugins[plugin][selector];

        if ('object' === typeof replacementTokens) {
          for (var from in replacementTokens) {
            selector = selector.replace(new RegExp(from, 'g'), replacementTokens[from]);
          }
        }

        element = $(selector);

        if (!element.length) {
          continue;
        }

        var camelizePlugin = plugin.charAt(0).toUpperCase() + plugin.substr(1, plugin.length - 1);

        var isAppliedMethod = 'is' + camelizePlugin + 'PluginApplied';
        var applyMethod = 'apply' + camelizePlugin + 'Plugin';

        if ('undefined' === typeof this[isAppliedMethod] || this[isAppliedMethod](element)) {
          continue;
        }
        if ('undefined' !== typeof this[applyMethod]) {
          this[applyMethod](element, elementData);
        }
      }
    }
  };

  SF.fn.elements = ElementBag;

})(jQuery);