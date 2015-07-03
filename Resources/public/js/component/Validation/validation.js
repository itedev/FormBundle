/**
 * Created by c1tru55 on 20.06.15.
 */
(function($) {

  var baseIsInitializable = SF.fn.classes.FormView.prototype.isInitializable;
  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    isInitializable: function() {
      var initializable = baseIsInitializable.call(this);

      return initializable || this.hasOption('constraints');
    },

    initialize: function($element) {
      baseInitialize.call(this, $element);

      var validatorConstraints = this.getOption('constraints', {});
      $.each(validatorConstraints, function(validator, constraints) {
        if ('undefined' === typeof SF.validators[validator]) {
          throw new Error('Validator "' + validator + '" is not registered.');
        }

        //if (!$.isFunction(SF.validators[validator].isInitialized)) {
        //  throw new Error('Validator "' + validator + '" must implement method "isInitialized".');
        //}
        //
        //if (SF.validators[validator].isInitialized($element)) {
        //  return;
        //}
        //
        //if (!$.isFunction(SF.validators[validator].initialize)) {
        //  throw new Error('Validator "' + validator + '" must implement method "initialize".');
        //}
        //
        //var event = $.Event('ite-before-apply.plugin', {
        //  plugin: plugin
        //});
        //$element.trigger(event, [pluginData]);
        //if (false === event.result) {
        //  return;
        //}
        //
        //SF.validators[validator].initialize($element, constraints, self);
        //
        //event = $.Event('ite-apply.plugin', {
        //  plugin: plugin
        //});
        //$element.trigger(event, [pluginData]);
      });
    }
  });

})(jQuery);