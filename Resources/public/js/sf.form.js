(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  SF.fn.plugins = {};
  SF.fn.validators = {};

  SF.fn.util = $.extend(SF.fn.util, {
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

  var FormView = function(viewData, parent) {
    this.parent = parent || null;
    this.options = {};
    this.children = {};

    var self = this;
    $.each(viewData['options'], function(name, value) {
      self.options[name] = 'prototype_view' === name
        ? new FormView(value, self)
        : value;
    });
    $.each(viewData['children'], function(name, childViewData) {
      var childView = new FormView(childViewData, self);
      self.addChild(name, childView);
    });
  };

  FormView.prototype = {
    getParent: function() {
      return this.parent;
    },

    getRoot: function() {
      return null !== this.parent ? this.parent.getRoot() : this;
    },

    isRoot: function() {
      return null === this.parent;
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

    getId: function() {
      return this.getOption('id');
    },

    getName: function() {
      return this.getOption('name');
    },

    getFullName: function() {
      return this.getOption('full_name');
    },

    getChildren: function() {
      return this.children;
    },

    hasChild: function(name) {
      return this.children.hasOwnProperty(name);
    },

    getChild: function(name, defaultValue) {
      defaultValue = defaultValue || null;

      return this.hasChild(name) ? this.children[name] : defaultValue;
    },

    addChild: function(name, view) {
      if (this.hasChild(name)) {
        return this;
      }

      view.parent = this;
      this.children[name] = view;

      return this;
    },

    removeChild: function(name) {
      if (!this.hasChild(name)) {
        return this;
      }

      delete this.children[name];

      return this;
    },

    clearChildren: function() {
      this.children = {};

      return this;
    },

    toArray: function() {
      var result = {
        options: {},
        children: {}
      };

      $.each(this.options, function(name, value) {
        var newValue;
        if (value instanceof FormView) {
          newValue = value.toArray();
        } else if ($.isPlainObject(value)) {
          newValue = $.extend(true, {}, value);
        } else {
          newValue = value;
        }
        result.options[name] = newValue;
      });
      $.each(this.children, function(name, childView) {
        result.children[name] = childView.toArray();
      });

      return result;
    },

    clone: function() {
      return new FormView(this.toArray(), this.parent);
    },

    findByOption: function(name, value) {
      var currentValue = this.getOption(name);
      if (currentValue == value) {
        return this;
      }

      var result = null;
      $.each(this.children, function(childName, childView) {
        result = childView.findByOption(name, value);
        if (null !== result) {
          return false; // break
        }
      });

      return result;
    },

    find: function(id) {
      return this.findByOption('id', id);
    },

    walkRecursive: function(callback) {
      callback.call(this);
      $.each(this.children, function(name, childView) {
        childView.walkRecursive(callback);
      });

      return this;
    },

    addCollectionItem: function(name) {
      if (!this.hasOption('prototype_view')) {
        return;
      }

      var replaceStringCallback = function(string) {
        var re = new RegExp(prototypeName, 'g');

        return string.replace(re, name);
      };
      var walkViewCallback = function() {
        var self = this;
        $.each(this.options, function(optionName, optionValue) {
          if (optionValue instanceof FormView) {
            optionValue.walkRecursive(walkViewCallback);
          } else if ($.isArray(optionValue) || $.isPlainObject(optionValue)) {
            $.each(optionValue, function(subOptionName, subOptionValue) {
              if ('string' === typeof subOptionValue) {
                optionValue[subOptionName] = replaceStringCallback(subOptionValue);
              }
            });
          } else if ('string' === typeof optionValue) {
            optionValue = replaceStringCallback(optionValue);
          }
          self.options[optionName] = optionValue;
        });
      };

      var prototype = this.getOption('prototype_view');
      var prototypeName = this.getOption('prototype_name');

      var collectionItem = prototype.clone();
      collectionItem.walkRecursive(walkViewCallback);
      this.addChild(name, collectionItem);
      collectionItem.initializeRecursive();

      return this;
    },

    getElement: function(context) {
      return $('#' + this.getId(), context);
    },

    getForm: function(context) {
      return this.getRoot().getElement(context);
    },

    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('sfInitialized');
    },

    isInitializable: function() {
      return this.hasOption('plugins');
    },

    setInitialized: function($element) {
      $element.data('sfInitialized', 1);
    },

    initializeRecursive: function(force) {
      force = force || false;

      if (this.isInitializable()) {
        // view is initializable
        var $element = this.getElement();
        if (0 !== $element.length) {
          // element exists
          if (!this.isInitialized($element) || force) {
            this.initialize($element);
            this.setInitialized($element);
          }
        } else {
          // element does not exist
        }
      }
      $.each(this.children, function(name, childView) {
        childView.initializeRecursive(force);
      });

      return this;
    },

    initialize: function($element) {
      var self = this;

      var plugins = this.getOption('plugins', {});
      $.each(plugins, function(plugin, pluginData) {
        if ('undefined' === typeof SF.plugins[plugin]) {
          throw new Error('Plugin "' + plugin + '" is not registered.');
        }

        if (!$.isFunction(SF.plugins[plugin].isInitialized)) {
          throw new Error('Plugin "' + plugin + '" must implement method "isInitialized".');
        }

        if (SF.plugins[plugin].isInitialized($element)) {
          return;
        }

        if (!$.isFunction(SF.plugins[plugin].initialize)) {
          throw new Error('Plugin "' + plugin + '" must implement method "initialize".');
        }

        var event = $.Event('ite-before-apply.plugin', {
          plugin: plugin
        });
        $element.trigger(event, [pluginData]);
        if (false === event.result) {
          return;
        }

        SF.plugins[plugin].initialize($element, pluginData, self);

        event = $.Event('ite-apply.plugin', {
          plugin: plugin
        });
        $element.trigger(event, [pluginData]);
      });
    },

    //clearElementValue: function($element) {
    //  var event = $.Event('ite-before-clear.hierarchical');
    //  $element.trigger(event);
    //  if (false === event.result) {
    //    return;
    //  }
    //
    //  if (element.hasDelegateSelector()) {
    //    $element.html('');
    //  } else {
    //    var node = $element.get(0);
    //    if (rxText.test(node.nodeName) && !rxCheckable.test(node.type)) {
    //      $element.val('');
    //    } else if (rxSelect.test(node.nodeName)) {
    //      $element.html('');
    //    }
    //  }
    //
    //  if (element.hasPlugins()) {
    //    $.each(element.getPlugins(), function(i, plugin) {
    //      if ('undefined' !== typeof SF.plugins[plugin].clearValue) {
    //        SF.plugins[plugin].clearValue($element);
    //      }
    //    });
    //  }
    //
    //  $element.trigger('ite-clear.hierarchical');
    //},

    getValue: function($element) {
      //var $element = this.getElement(context);

      var value;
      var valueTaken = false;

      // try to get value via plugins
      if (this.hasOption('plugins')) {
        var plugins = this.getOption('plugins', {});
        $.each(plugins, function (plugin, pluginData) {
          if ($.isFunction(SF.plugins[plugin].getValue)) {
            value = SF.plugins[plugin].getValue($element);
            valueTaken = true;

            return false; // break
          }
        });

        if (valueTaken) {
          return value;
        }
      }

      // get value in regular way
      if (!valueTaken) {
        var delegateSelector = this.getOption('delegate_selector', false);
        if (delegateSelector) {
          // checkbox or radio
          value = [];

          $element.find(delegateSelector).filter(function() {
            return this.checked;
          }).each(function() {
            value.push($(this).val());
          });
          if ('input[type="radio"]' === delegateSelector) {
            return value.length ? value[0] : null;
          }

          return value;
        } else {
          // input, textarea or select
          var element = $element.get(0);
          if (rxText.test(element.nodeName) || rxSelect.test(element.nodeName)) {
            return $element.val();
          }
        }
      }

      return $element.html();
    },

    setValue: function($element, $newElement) {
      var valueSet = false;

      // try to set value via plugins
      if (!this.hasOption('plugins')) {
        var plugins = this.getOption('plugins', {});
        $.each(plugins, function (plugin, pluginData) {
          if ($.isFunction(SF.plugins[plugin].setValue)) {
            SF.plugins[plugin].setValue($element, $newElement);
            valueSet = true;

            return false; // break
          }
        });
      }

      // set value in regular way
      if (!valueSet) {
        if (!this.getOption('compound', false)) {
          // simple field
          var element = $element.get(0);
          if (rxText.test(element.nodeName)) {
            // input or text
            $element.val($newElement.val());
          } else if (rxSelect.test(element.nodeName)) {
            // select
            $element
              .html($newElement.html())
              .val($newElement.val())
            ;
          } else {
            $element.html($newElement.html());
          }
        } else {
          // compound field
          $element.html($newElement.html());
        }
      }
    },

    mergeRecursive: function(view) {
      this.merge(view);

      var self = this;
      $.each(this.children, function(childName, childView) {
        if (view.hasChild(childName)) {
          // merge intersected child
          childView.mergeRecursive(view.getChild(childName));
        } else {
          // remove existing child
          self.removeChild(childName);
        }
      });
      $.each(view.getChildren(), function(childName, childView) {
        if (!self.hasChild(childName)) {
          // add new child
          self.addChild(childName, childView);
        }
      });
    },

    merge: function(view) {
//      var frozenOptions = {
//        id: this.options['id'],
//        name: this.options['name'],
//        full_name: this.options['full_name']
//      };
//      this.options = $.extend(true, {}, this.options, view.getOptions(), frozenOptions);
      $.extend(this.options, view.getOptions());
    }
  };

  FormView.prototype.fn = FormView.prototype;

  var FormBag = function() {
    this.forms = {};
  };

  FormBag.prototype = {
    has: function(name) {
      return this.forms.hasOwnProperty(name);
    },

    get: function(name, defaultValue) {
      defaultValue = defaultValue || null;

      return this.has(name) ? this.forms[name] : defaultValue;
    },

    add: function(name, viewData) {
      var view = new FormView(viewData);
      if (this.has(name)) {
        this.forms[name].mergeRecursive(view);

        return this;
      }

      this.forms[name] = view;

      return this;
    },

    set: function(forms) {
      var self = this;
      $.each(forms, function(name, viewData) {
        self.add(name, viewData);
      });

      return this;
    },

    initialize: function(force) {
      $.each(this.forms, function(name, view) {
        view.initializeRecursive(force);
      });

      return this;
    },

    findByOption: function(name, value) {
      var result = null;
      $.each(this.forms, function(viewName, view) {
        result = view.findByOption(name, value);
        if (null !== result) {
          return false; // break
        }
      });

      return result;
    },

    find: function(id) {
      var result = null;
      $.each(this.forms, function(viewName, view) {
        result = view.find(id);
        if (null !== result) {
          return false; // break
        }
      });

      return result;
    }
  };

  FormBag.prototype.fn = FormBag.prototype;

  SF.fn.classes = $.extend(SF.fn.classes, {
    FormView: FormView,
    FormBag: FormBag
  });

  SF.fn.forms = new FormBag();

  $(document)
    .on('ite-pre-ajax-complete', function(e, data) {
      if (!data.hasOwnProperty('forms')) {
        return;
      }

      SF.forms.set(data['forms']);
    })
    .on('ite-post-ajax-complete', function(e, data) {
      if (!data.hasOwnProperty('forms')) {
        return;
      }

      SF.forms.initialize();
    })
  ;

})(jQuery);