(function($) {
  SF.fn.plugins['tinymce'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof window.tinyMCE
        && 'undefined' !== typeof window.tinyMCE.get($element.attr('id'));
      // !!(element.id && 'tinymce' in window && tinymce.get(element.id))
    },

    initialize: function($element, pluginData) {
      $element.tinymce(pluginData.options);
    }
  };
})(jQuery);