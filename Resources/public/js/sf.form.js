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

  SF.util.addGetParameter = function(url, paramName, paramValue) {
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
  };

  SF.util.strtr = function(str, replacementTokens) {
    if ('object' === typeof replacementTokens) {
      $.each(replacementTokens, function(from, to) {
        str = str.replace(new RegExp(from, 'g'), to);
      });
    }
    return str;
  };

  SF.util.getSimpleName = function(element) {
    var name = element.attr('name');
    var bracketIndex = name.lastIndexOf('[');
    if (-1 !== bracketIndex) {
      name = name.substr(bracketIndex + 1, name.lastIndexOf(']') - bracketIndex - 1);
    }

    return name;
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
          selector = SF.util.strtr(selector, replacementTokens);

          var element = $(selector, context);
          if (!element.length) {
            return;
          }

          var camelizedPlugin = SF.util.camelCase(plugin);
          var isAppliedMethod = 'is' + camelizedPlugin + 'PluginApplied';
          var applyMethod = 'apply' + camelizedPlugin + 'Plugin';

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

  // Element
  var Element = function(selector, parents, options) {
    this.selector = selector;
    this.parents = parents || [];
    this.children = [];
    this.options = options || {};
  };
  Element.prototype = {
    getParents: function() {
      return this.parents;
    },

    hasParents: function() {
      return this.parents.length > 0;
    },

    getSelector: function() {
      return this.selector;
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

    hasChild: function(child) {
      return -1 !== $.inArray(child, this.children);
    },

    addChild: function(child) {
      if (!this.hasChild(child)) {
        this.children.push(child);
      }
    }
  };

  // ElementTree
  var ElementTree = function() {
    this.elements = {};
  };
  ElementTree.prototype = {
    has: function(selector) {
      return this.elements.hasOwnProperty(selector);
    },

    get: function(selector, defaultValue) {
      defaultValue = defaultValue || null;
      return this.has(selector) ? this.elements[selector] : defaultValue;
    },

    add: function(selector, parents, options) {
      if (this.has(selector)) {
        return;
      }

      var self = this;
      $.each(parents, function(index, parent) {
        if (!self.has(parent)) {
          self.elements[parent] = new Element(parent);
        }
        self.get(parent).addChild(selector);
      });

      this.elements[selector] = new Element(selector, parents, options);
    },

    set: function(elementTree) {
      var self = this;

      $.each(elementTree, function(selector, elementData) {
        self.add(selector, elementData['parents'], elementData['options']);
      });
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

    apply: function() {
      var self = this;
      $.each(this.elements, function(selector, element) {
        if (!element.hasParents()) {
          return;
        }

        var $parents = $(element.getParents().join(', '));
        if (!$parents.length) {
          return;
        }

        $parents.on('change', function(e) {
          // clear value
          var dependentElement = $(selector);
          dependentElement.html('');
          dependentElement.select2('val', '');

          // get data
          var allParents = self.getAllParents(selector);
          var data = {};
          $.each(allParents, function(index, parent) {
            var parentElement = $(parent);
            var name = SF.util.getSimpleName(parentElement);
            data[name] = parentElement.val();
          });

          $.ajax({
            type: 'post',
            url: element.getOptions()['url'],
            data: data,
            dataType: 'html',
            success: function(response) {
              dependentElement.html(response);

              if (dependentElement.children('option').length) {
                var firstOption = dependentElement.children('option:first');
                firstOption.prop('selected', true);
                dependentElement.select2('val', firstOption.attr('value'));
              }
            }
          });
        });
      });
    }
  };

  ElementTree.prototype.fn = ElementTree.prototype;
  SF.fn.elementTree = new ElementTree();

// http://stackoverflow.com/questions/5202296/add-a-hook-to-all-ajax-requests-on-a-page/5202312#5202312

})(jQuery);