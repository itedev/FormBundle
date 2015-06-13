(function($) {
  SF.fn.plugins['bootstrap_spinedit'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('spinedit');
    },

    initialize: function($element, pluginData) {
      $element.spinedit(pluginData.options);
    },

    setValue: function($element) {
      $element.spinedit('setValue', $element.val());
    }
  };
})(jQuery);