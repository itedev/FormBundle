(function($) {
  SF.fn.plugins['bootstrap_datetimepicker'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('datetimepicker');
    },

    initialize: function($element, pluginData) {
      $element.datetimepicker(pluginData.options);

      $element.on('dp.change', function() {
        $element.trigger('change.ite.hierarchical');
      });
    }
  });
})(jQuery);