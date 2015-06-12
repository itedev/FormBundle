/**
 * Created by c1tru55 on 16.05.15.
 */
(function($) {

  var FormView = function(viewData, parent) {
    this.parent = parent || null;
    this.options = {};
    this.children = {};
    this.initialized = false;

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

      this.children[name] = view;

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

    walk: function(callback) {
      callback.call(this);
      $.each(this.children, function(name, childView) {
        childView.walk(callback);
      });

      return this;
    },

    addCollectionItem: function(name) {
      if (!this.hasOption('prototype_view')) {
        return;
      }

      var prototype = this.getOption('prototype_view');
      var prototypeName = this.getOption('prototype_name');

      var collectionItem = prototype.clone();

      var replaceStringCallback = function(string) {
        var re = new RegExp(prototypeName, 'g');

        return string.replace(re, name);
      };
      var walkViewCallback = function() {
        var self = this;
        $.each(this.options, function(optionName, optionValue) {
          if (optionValue instanceof FormView) {
            optionValue.walk(walkViewCallback);
          } else if ($.isArray(optionValue) || $.isPlainObject(optionValue)) {
            $.each(optionValue, function(subOptionName, subOptionValue) {
              if ('string' === typeof subOptionValue) {
                optionValue[subOptionName] = replaceStringCallback.call(null, subOptionValue);
              }
            });
          } else if ('string' === typeof optionValue) {
            optionValue = replaceStringCallback.call(null, optionValue);
          }
          self.options[optionName] = optionValue;
        });
      };

      collectionItem.walk(walkViewCallback);

      this.addChild(name, collectionItem);
      this.initialize();

      return this;
    },

    getJQueryElement: function(context) {
      return $('#' + this.getOption('id'), context);
    },

    isInitialized: function() {
      return this.initialized;
    },

    initialize: function(force) {
      force = force || false;

      if (!this.initialized || force) {
        this._initialize();
        this.initialized = true;
        $.each(this.children, function(name, childView) {
          childView.initialize(force);
        });
      }

      return this;
    },

    _initialize: function() {
      var self = this;

      var plugins = this.getOption('plugins', {});
      $.each(plugins, function(plugin, pluginData) {
        if ('undefined' === typeof SF.plugins[plugin]) {
          throw new Error('Plugin "' + plugin + '" is not registered.');
        }

        if (!$.isFunction(SF.plugins[plugin].isApplied)) {
          throw new Error('Plugin "' + plugin + '" must implement method "isApplied".');
        }

        var $element = self.getJQueryElement();
        if (SF.plugins[plugin].isApplied($element)) {
          return;
        }

        if (!$.isFunction(SF.plugins[plugin].apply)) {
          throw new Error('Plugin "' + plugin + '" must implement method "apply".');
        }

        var event = $.Event('ite-before-apply.plugin', {
          plugin: plugin
        });
        $element.trigger(event, [pluginData]);
        if (false === event.result) {
          return;
        }

        SF.plugins[plugin].apply($element, pluginData);

        event = $.Event('ite-apply.plugin', {
          plugin: plugin
        });
        $element.trigger(event, [pluginData]);
      });
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
      if (this.has(name)) {
        return this;
      }

      this.forms[name] = new FormView(viewData);

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
        view.initialize(force);
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

})(jQuery);