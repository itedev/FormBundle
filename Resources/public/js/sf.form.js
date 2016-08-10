(function($) {
  var rxText = /^(?:input|textarea)/i;
  var rxCheckable = /^(?:checkbox|radio)$/i;
  var rxSelect = /^select$/i;

  SF.fn.plugins = {};
  SF.fn.validators = {};

  $.fn.formView = function() {
    if (1 !== this.length) {
      $.error('jQuery.formView can be called only for 1 element.');
    }

    var id = this.attr('id');

    return SF.forms.find(id);
  };

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

  var Plugin = function(methods) {
    methods = methods || {};

    var self = this;
    $.each(methods, function(methodName, method) {
      self[methodName] = method;
    });
  };

  Plugin.prototype = {
    isInitialized: function($element) {
      return false;
    },

    initialize: function($element, pluginData) {
      throw new Error('Method is not implemented.');
    },

    clearValue: function($element) {},

    getValue: function($element, $newElement) {},

    setValue: function($element, $newElement) {}
  };

  Plugin.prototype.fn = Plugin.prototype;

  var FormPath = function(formPath) {
    if ('string' !== typeof formPath) {
      throw new Error('String expected.');
    }
    if ('' === formPath) {
      throw new Error('The form path should not be empty.');
    }
    if ('/' === formPath.slice(-1)) {
      throw new Error('The form path must not end with "/" symbol.');
    }

    this.pathAsString = formPath;
    this.absolute = false;
    if ('/' === formPath.charAt(0)) {
      formPath = formPath.substring(1);
      this.absolute = true;
    }

    var self = this;
    var elements = formPath.split('/');
    this.elements = [];
    this.parents = [];
    $.each(elements, function(i, element) {
      self.elements.push(element);
      self.parents.push('..' === element);
    });

    this.length = this.elements.length;
  };

  FormPath.prototype = {
    getLength: function() {
      return this.length;
    },

    getElements: function() {
      return this.elements;
    },

    isAbsolute: function() {
      return this.absolute;
    },

    isRelative: function() {
      return !this.absolute;
    },

    getElement: function(index) {
      if ('undefined' === typeof this.elements[index]) {
        throw new Error('The index "' + index + '" is not within the form path.');
      }

      return this.elements[index];
    },

    isParent: function(index) {
      if ('undefined' === typeof this.parents[index]) {
        throw new Error('The index "' + index + '" is not within the form path.');
      }

      return this.parents[index];
    },

    getPathAsString: function() {
      return this.pathAsString;
    }
  };

  FormPath.prototype.fn = FormPath.prototype;

  var FormAccessor = function() {};

  FormAccessor.prototype = {
    getView: function(view, formPath) {
      if (!(formPath instanceof FormPath)) {
        formPath = new FormPath(formPath);
      }

      var current = formPath.isAbsolute() ? view.getRoot() : view;
      var length = formPath.getLength();
      for (var i = 0; i < length; i++) {
        var isParent = formPath.isParent(i);
        if (isParent) {
          if (view.isRoot()) {
            // error
          }
          current = current.getParent();
        } else {
          var element = formPath.getElement(i);
          if (!current.hasChild(element)) {
            // error
          }
          current = current.getChild(element)
        }
      }

      return current;
    },

    getReverseFormPath: function(parentView, childView) {
      var elements = [];
      var current = childView;
      while (null !== current && current !== parentView) {
        elements.push(current.getName());
        current = current.getParent();
      }

      if (0 === elements.length) {
        return null;
      }

      return new FormPath(elements.reverse().join('/'));
    }
  };

  FormAccessor.prototype.fn = FormAccessor.prototype;

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

      return collectionItem;
    },

    isCollection: function() {
      return this.hasOption('prototype_view'); // @todo: collection may be without prototype
    },

    isCollectionItem: function() {
      return !this.isRoot() && this.parent.isCollection();
    },

    getClosestCollection: function() {
      return !this.isRoot()
        ? (this.parent.isCollection() ? this.parent : this.parent.getClosestCollection())
        : null;
    },

    getClosestCollectionItem: function() {
      return !this.isRoot()
        ? (this.parent.isCollectionItem() ? this.parent : this.parent.getClosestCollectionItem())
        : null;
    },

    isInsideCollectionItem: function() {
      return null !== this.getClosestCollectionItem();
    },

    findClosestCollectionSiblings: function() {
      var closestCollectionItemView = this.getClosestCollectionItem();
      if (null === closestCollectionItemView) {
        return [];
      }

      var closestCollectionView = closestCollectionItemView.getParent();

      var formAccessor = SF.services.get('form_accessor');
      var formPath = formAccessor.getReverseFormPath(closestCollectionItemView, this);

      var closestCollectionSiblingViews = [];
      $.each(closestCollectionView.getChildren(), function(childName, childView) {
        if (closestCollectionItemView !== childView) {
          var collectionSiblingView = formAccessor.getView(childView, formPath);
          closestCollectionSiblingViews.push(collectionSiblingView)
        }
      });

      return closestCollectionSiblingViews;
    },

    getClosestCollectionSiblingsData: function() {
      var siblings = this.findClosestCollectionSiblings();
      var data = [];
      $.each(siblings, function(i, sibling) {
        var $element = sibling.getElement();
        if (!$element.length) {
          return;
        }

        data.push(sibling.getData($element));
      });

      return data;
    },

    getElement: function(context) {
      return $('#' + this.getId(), context);
    },

    getForm: function(context) {
      return this.getRoot().getElement(context);
    },

    ///

    errorElement: 'span',
    errorWrapper: null,
    errorContainer: $([]),
    errorClass: 'error',

    hasErrors: function () {
      return 0 !== this.getErrors().length;
    },

    getErrors: function () {
      return this.getOption('errors', []);
    },

    showErrorsRecursive: function () {
      if (this.hasErrors()) {
        var $element = this.getElement();
        var errors = this.getErrors();

        this.showErrors(errors, $element);
      }

      $.each(this.children, function(name, childView) {
        childView.showErrorsRecursive();
      });
    },

    resetErrorsRecursive: function () {
      var $element = this.getElement();
      this.resetErrors($element);

      $.each(this.children, function(name, childView) {
        childView.resetErrorsRecursive();
      });
    },

    showErrors: function (message, $element) {
      $element = 'undefined' !== typeof $element ? $element : this.getElement();
      var describedBy = $element.attr('aria-describedby');
      message = 'array' !== $.type(message) ? [message] : message;

      if (this.highlight) {
        this.highlight.apply(this, [$element, this.errorClass]);
      }

      var $error = this.findError($element);
      var $reference = $error;
      if (0 !== $error.length) {
        $error
          .addClass(this.errorClass)
          .html(message)
        ;
      } else {
        $error = $('<' + this.errorElement + '>')
          .attr('id', this.getId + '_error')
          .addClass(this.errorClass)
          .html(message || '')
        ;

        $reference = $error;
        if (this.errorWrapper) {
          $reference = $error.wrap('<' + this.errorWrapper + '/>').parent();
        }
        if (0 !== this.errorContainer.length) {
          this.errorContainer.append($reference);
        } else if (this.errorPlacement) {
          this.errorPlacement($reference, $element);
        } else {
          $reference.insertAfter($element);
        }

        if ($error.is('label')) {
          $error.attr('for', this.getId());
        } else if (0 === $error.parents('label[for="' + this.getId() + '"]').length) {
          var errorId = $error.attr('id').replace(/(:|\.|\[|\])/g, "\\$1");
          if (!describedBy) {
            describedBy = errorId;
          } else if (!describedBy.match(new RegExp("\\b" + errorId + "\\b"))) {
            describedBy += ' ' + errorId;
          }
          $element.attr('aria-describedby', describedBy);
        }
      }
    },

    resetErrors: function ($element) {
      $element = 'undefined' !== typeof $element ? $element : this.getElement();

      if (this.unhighlight) {
        this.highlight.apply(this, [$element, this.errorClass]);
      }
    },

    highlight: function ($element, errorClass) {
      if ('radio' === $element.type()) {
        this.findByName(element.name).addClass(errorClass);
      } else {
        $element.addClass(errorClass);
      }
    },

    unhighlight: function ($element, errorClass) {
      if ('radio' === $element.type()) {
        this.findByName(element.name).removeClass(errorClass);
      } else {
        $element.removeClass(errorClass);
      }
    },

    ///

    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('sfInitialized');
    },

    isInitializable: function() {
      return this.isRoot() || this.hasOption('plugins');
    },

    setInitialized: function($element) {
      $element.data('sfInitialized', true);
    },

    initializeRecursive: function (force) {
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

      if ($element) {
        $element.trigger('post-initialize.ite.form');
      }

      return this;
    },

    initialize: function($element) {
      var self = this;

      var plugins = this.getOption('plugins', {});
      $.each(plugins, function(plugin, pluginData) {
        if ('undefined' === typeof SF.plugins[plugin] || !(SF.plugins[plugin] instanceof Plugin)) {
          throw new Error('Plugin "' + plugin + '" is not registered.');
        }

        if (SF.plugins[plugin].isInitialized($element)) {
          return;
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

    getData: function($element) {
      var value;
      var valueTaken = false;

      // try to get value via plugins
      if (this.hasOption('plugins')) {
        var plugins = this.getOption('plugins', {});
        $.each(plugins, function (plugin, pluginData) {
          value = SF.plugins[plugin].getValue($element);
          if ('undefined' !== typeof value) {
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
          if (':radio' === delegateSelector) {
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

    getValue: function($element) {
      //var $element = this.getElement(context);

      var value;
      var valueTaken = false;

      // try to get value via plugins
      if (this.hasOption('plugins')) {
        var plugins = this.getOption('plugins', {});
        $.each(plugins, function (plugin, pluginData) {
          value = SF.plugins[plugin].getValue($element);
          if ('undefined' !== typeof value) {
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
          if (':radio' === delegateSelector) {
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
      if (this.hasOption('plugins')) {
        var plugins = this.getOption('plugins', {});
        var self = this;
        $.each(plugins, function (plugin, pluginData) {
          var result = SF.plugins[plugin].setValue($element, $newElement, self);
          if ('undefined' !== typeof result) {
            valueSet = true;

            return false; // break
          }
        });

        if (valueSet) {
          return;
        }
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

    triggerEvent: function($element, event) {
      var delegateSelector = this.getOption('delegate_selector', false);
      if (delegateSelector) {
        var $checkedChildren = $element.find(delegateSelector).filter(function() {
          return this.checked;
        });
        if ($checkedChildren.length) {
          $checkedChildren.first().trigger(event);
        }
      } else {
        $element.trigger(event);
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
      this.options = view.getOptions();
      //$.extend(this.options, view.getOptions());
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
    Plugin: Plugin,
    FormPath: FormPath,
    FormAccessor: FormAccessor,
    FormView: FormView,
    FormBag: FormBag
  });

  SF.fn.forms = new FormBag();

  SF.services.set('form_accessor', new FormAccessor());

  // initialize
  // var baseInitialize = SF.fn.initialize;
  // SF.fn = $.extend(SF.fn, {
  //   initialize: function () {
  //     baseInitialize.call(this);

  //     window.SF.forms.initialize();
  //   }
  // });

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
