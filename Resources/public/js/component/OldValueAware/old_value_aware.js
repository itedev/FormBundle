(function($) {
  SF.fn.callbacks = $.extend(SF.fn.callbacks, {
    oldValueAwareChange: function (e) {
      var $element = $(e.delegateTarget);

      var view = $element.formView();

      e.oldValue = $element.data('oldValue');
      $element.data('oldValue', view.getValue());
    }
  });

  var baseIsInitializable = SF.fn.classes.FormView.prototype.isInitializable;
  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    isInitializable: function () {
      var initializable = baseIsInitializable.call(this);

      return initializable || this.hasOption('old_value_aware');
    },

    initialize: function ($element, initializationMode) {
      baseInitialize.apply(this, [$element, initializationMode]);

      if ('forward' !== initializationMode) {
        return;
      }

      var isOldValueAware = this.getOption('old_value_aware');
      if (!isOldValueAware) {
        return;
      }

      if ('undefined' !== typeof $element.data('oldValueAware')) {
        return;
      }

      $element.data('oldValue', this.getValue());
      var delegateSelector = this.getOption('delegate_selector', false);
      if (delegateSelector) {
        $element.on('change.ite.hierarchical', delegateSelector, SF.callbacks.oldValueAwareChange);
      } else {
        $element.on('change.ite.hierarchical', SF.callbacks.oldValueAwareChange);
      }

      $element.data('oldValueAware', true);
    }
  });

})(jQuery);