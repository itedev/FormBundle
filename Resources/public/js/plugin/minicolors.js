(function($) {
  SF.fn.plugins['minicolors'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('minicolors-initialized');
    },

    initialize: function($element, pluginData) {
      $element.minicolors(pluginData.options);
    }
  };
})(jQuery);