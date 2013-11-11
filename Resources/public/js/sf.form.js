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

  SF.util.camelCase = function(str) {
    return str.replace(/[_\-]/g, ' ')
      .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
        return $1.toUpperCase();
      })
      .replace(/\s/g, '');
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
      return this.hasPlugin(plugin) ? this.plugins[plugin] : {};
    },

    hasElement: function(selector) {
      return this.elements.hasOwnProperty(selector);
    },

    getElements: function() {
      var elements = [];

      $.each(this.elements, function(i, element) {
        elements.push(element);
      });

      return elements;
    },

    addElement: function(plugin, selector, elementData) {
      if (!this.hasPlugin(plugin)) {
        this.plugins[plugin] = {};
      }
      this.plugins[plugin][selector] = elementData;
      this.elements[selector] = plugin;
    },

    set: function(pluginElements) {
      var self = this;

      $.each(pluginElements, function(plugin, selectors) {
        $.each(selectors, function(selector, elementData) {
          self.addElement(plugin, selector, elementData);
        });
      });
    },

    apply: function(context, replacementTokens) {
      var self = this;

      $.each(this.plugins, function(plugin, selectors) {
        $.each(selectors, function(selector, elementData) {
          if ('object' === typeof replacementTokens) {
            $.each(replacementTokens, function(from, to) {
              selector = selector.replace(new RegExp(from, 'g'), to);
            });
          }

          var element = $(selector, context);
          if (!element.length) {
            return;
          }

          var camelizePlugin = SF.util.camelCase(plugin);
          var isAppliedMethod = 'is' + camelizePlugin + 'PluginApplied';
          var applyMethod = 'apply' + camelizePlugin + 'Plugin';

          if ('undefined' === typeof self[isAppliedMethod] || self[isAppliedMethod](element)) {
            return;
          }
          if ('undefined' !== typeof self[applyMethod]) {
            element.trigger('apply.element.ite-form', [elementData]);
            self[applyMethod](element, elementData);
          }
        });
      });
    }
  };

  ElementBag.prototype.fn = ElementBag.prototype;

  SF.fn.elements = new ElementBag();

// http://stackoverflow.com/questions/5202296/add-a-hook-to-all-ajax-requests-on-a-page/5202312#5202312

})(jQuery);