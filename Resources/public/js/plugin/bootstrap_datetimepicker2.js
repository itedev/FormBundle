(function($) {
  SF.fn.plugins['bootstrap_datetimepicker2'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('datetimepicker');
    },

    initialize: function($element, pluginData) {
      $element.datetimepicker(pluginData.options);
    }
  });
})(jQuery);