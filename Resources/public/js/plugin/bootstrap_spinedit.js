(function($) {
  SF.fn.plugins['bootstrap_spinedit'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('spinedit');
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.spinedit(options);
    }
  };
})(jQuery);