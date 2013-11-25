(function($) {
  SF.fn.plugins['minicolors'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('minicolors-initialized');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.minicolors(options);
    }
  };
})(jQuery);