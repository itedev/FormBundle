/**
 * Created by c1tru55 on 20.06.15.
 */
(function($) {

  var Submitter = function(name) {
    this.name = name;
  };

  Submitter.prototype = {
    isInitialized: function($element) {
      throw new Error('Method "isInitialized" must be implemented.');
    },

    initialize: function($element) {
      throw new Error('Method "initialize" must be implemented.');
    },

    submit: function($element) {
      throw new Error('Method "submit" must be implemented.');
    }
  };

  Submitter.prototype.fn = Submitter.prototype;

  var SubmitterBag = function() {
    this.submitters = {};
  };

  SubmitterBag.prototype = {
    has: function(name) {
      return this.submitters.hasOwnProperty(name);
    },

    add: function(name, submitter) {
      if (!this.has(name)) {
        this.submitters[name] = submitter;
      }

      return this;
    },

    get: function(name) {
      if (!this.has(name)) {
        throw new Error('Submitter "' + name + '" is not registered.');
      }

      return this.submitters[name];
    }
  };

  SubmitterBag.prototype.fn = SubmitterBag.prototype;

  SF.fn.classes = $.extend(SF.fn.classes, {
    Submitter: Submitter,
    SubmitterBag: SubmitterBag
  });

  var baseIsInitializable = SF.fn.classes.FormView.prototype.isInitializable;
  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    isInitializable: function() {
      var initializable = baseIsInitializable.call(this);

      return initializable || this.hasOption('ajax_submit');
    },

    initialize: function($element) {
      baseInitialize.call(this, $element);

      if (!this.getOption('ajax_submit', false)) {
        return;
      }

      var submitter = SF.submitters.get(this.getOption('submitter'));

      if (submitter.isInitialized($element)) {
        return;
      }

      submitter.initialize($element, this);
//
//
//      var event = $.Event('ite-before-initialize.submitter', {
//        plugin: plugin
//      });
//      $element.trigger(event, [pluginData]);
//      if (false === event.result) {
//        return;
//      }
//
//      SF.validators[validator].initialize($element, constraints, self);
//
//      event = $.Event('ite-initialize.submitter', {
//        plugin: plugin
//      });
//      $element.trigger(event, [pluginData]);
    }
  });

})(jQuery);