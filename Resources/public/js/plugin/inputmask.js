(function($) {
  SF.fn.plugins['inputmask'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('_inputmask');
    },

    initialize: function($element, pluginData) {
      $element.inputmask(pluginData.options);
    }

    //setValue: function($element) {
    //  $element.trigger('change');
    //}
  };
})(jQuery);