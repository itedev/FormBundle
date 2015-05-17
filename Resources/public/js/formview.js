/**
 * Created by c1tru55 on 16.05.15.
 */
(function($) {

  var FormView = function(viewData, parent) {
    this.parent = parent || null;
    this.options = {};
    this.children = {};
    this.applied = false;

    var self = this;
    $.each(viewData['options'], function(name, value) {
      self.options[name] = 'prototype' === name
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
      return null !== this.parent ? this.getRoot() : this;
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
        return;
      }

      this.children[name] = view;
    },

    toArray: function() {
      var result = {
        options: {},
        children: {}
      };

      $.each(this.options, function(name, value) {
        result.options[name] = 'prototype' === name
          ? value.toArray()
          : value;
      });
      $.each(this.children, function(name, childView) {
        result.children[name] = childView.toArray();
      });

      return result;
    },

    clone: function(view) {
      var viewArray = view.toArray();

      return new FormView(viewArray, view.getParent());
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
          return false;
        }
      });

      return result;
    },

    addCollectionItem: function(prototypeName) {
      if (!this.hasOption('prototype')) {
        return;
      }

      var prototype = this.getOption('prototype');
      var collectionItem = prototype.clone();

      //this.addChild(prototypeName, );
    },

    isApplied: function() {
      return this.applied;
    },

    apply: function() {
      console.log(this.getOption('name'));
      $.each(this.children, function(name, childView) {
        childView.apply();
      });
      //this.applied = true;
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
        return;
      }

      this.forms[name] = new FormView(viewData);
    },

    set: function(forms) {
      var self = this;
      $.each(forms, function(name, viewData) {
        self.add(name, viewData);
      });
    },

    apply: function() {
      $.each(this.forms, function(name, view) {
        view.apply();
      });
    }
  };

  FormBag.prototype.fn = FormBag.prototype;

  SF.fn.classes = $.extend(SF.fn.classes, {
    FormView: FormView,
    FormBag: FormBag
  });

  SF.fn.forms = new FormBag();

})(jQuery);