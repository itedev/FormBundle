(function($) {
  SF.fn.plugins['bootstrap_colorpicker'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('colorpicker');
    },

    initialize: function($element, pluginData) {
      $element.colorpicker(pluginData.options);
    }
  };
})(jQuery);