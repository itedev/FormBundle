(function($) {
  SF.fn.plugins['inputmask'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('_inputmask');
    },

    initialize: function($element, pluginData) {
      $element.inputmask(pluginData.options);
    }

    //setValue: function($element) {
    //  $element.trigger('change');
    //}
  });
})(jQuery);