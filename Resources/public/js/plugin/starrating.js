(function($) {
  SF.fn.plugins['starrating'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('rating');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.rating(options);
    }
  };
})(jQuery);