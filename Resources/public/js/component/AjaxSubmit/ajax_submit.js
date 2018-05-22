/**
 * Created by c1tru55 on 20.06.15.
 */
(function($) {

  var Submitter = function(methods) {
    methods = methods || {};

    var self = this;
    $.each(methods, function(methodName, method) {
      self[methodName] = method;
    });
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

  SF.fn.submitters = {};

  SF.fn.classes = $.extend(SF.fn.classes, {
    Submitter: Submitter
  });

  var baseIsInitializable = SF.fn.classes.FormView.prototype.isInitializable;
  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    isInitializable: function() {
      var initializable = baseIsInitializable.call(this);

      return initializable || this.hasOption('ajax_submit');
    },

    initialize: function($element, initializationMode) {
      baseInitialize.apply(this, [$element, initializationMode]);

      if ('forward' !== initializationMode) {
        return;
      }

      if (!this.getOption('ajax_submit', false)) {
        return;
      }

      var submitter = SF.submitters[this.getOption('submitter')];

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