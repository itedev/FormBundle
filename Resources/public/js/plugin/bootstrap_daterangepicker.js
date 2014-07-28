(function($) {
  SF.fn.plugins['bootstrap_daterangepicker'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('daterangepicker');
    },

    apply: function(element, elementData) {
      element.daterangepicker(elementData.options);
    }
  };
})(jQuery);