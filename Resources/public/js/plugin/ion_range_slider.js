(function($) {
  SF.fn.plugins['ion_range_slider'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('ionRangeSlider');
    },

    initialize: function($element, pluginData) {
      $element.ionRangeSlider(pluginData.options);
    },

    setValue: function($element) {
      $element.trigger('change');

      return true;
    }
  });
})(jQuery);