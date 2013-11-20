(function($) {
  SF.plugins['bootstrap_colorpicker'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('colorpicker');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.colorpicker(options);
    }
  };
})(jQuery);