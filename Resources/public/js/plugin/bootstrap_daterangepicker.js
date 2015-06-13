(function($) {
  SF.fn.plugins['bootstrap_daterangepicker'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('daterangepicker');
    },

    initialize: function($element, pluginData) {
      $element.daterangepicker(pluginData.options);
    }
  };
})(jQuery);