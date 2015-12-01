(function($) {
  SF.fn.plugins['minicolors'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('minicolors-initialized');
    },

    initialize: function($element, pluginData) {
      $element.minicolors(pluginData.options);
    }
  });
})(jQuery);