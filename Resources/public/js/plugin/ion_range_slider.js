(function($) {
  SF.fn.plugins['ion_range_slider'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('ionRangeSlider');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.ionRangeSlider(options);
    },

    setValue: function(element) {
      element.trigger('change');
    }
  };
})(jQuery);