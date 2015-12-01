(function($) {
  SF.fn.plugins['starrating'] = new SF.classes.Plugin({
    isInitialized: function($element, view) {
      return 'undefined' !== typeof $element.find(view.getOption('delegate_selector')).data('rating');
    },

    initialize: function($element, pluginData, view) {
      $element.find(view.getOption('delegate_selector')).rating(pluginData.options);
    }
  });
})(jQuery);