(function($) {
  SF.fn.plugins['typeahead'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('tt-typeahead');
    },

    initialize: function($element, pluginData) {
      console.log(pluginData);
//      $element.typeahead(pluginData.options);
    }
  };
})(jQuery);