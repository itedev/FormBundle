(function($) {
  SF.fn.plugins['knob'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('kontroled');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.knob(options);
    }
  };
})(jQuery);