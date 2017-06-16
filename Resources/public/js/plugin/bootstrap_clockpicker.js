(function($) {
  SF.fn.plugins['bootstrap_clockpicker'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('clockpicker');
    },

    initialize: function($element, pluginData) {
      $element.clockpicker(pluginData.options);

      //$element.on('dp.change', function() {
      //  $element.trigger('change.ite.hierarchical');
      //});
    }
  });
})(jQuery);