(function($) {
  SF.plugins['bootstrap_datetimepicker2'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('datetimepicker');
    },

    apply: function(element, elementData) {
      element.datetimepicker(elementData.options);
    }
  };
})(jQuery);