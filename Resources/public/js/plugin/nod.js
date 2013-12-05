(function($) {
  SF.fn.plugins['nod'] = {
    isApplied: function(element) {
      return false;
    },

    apply: function(element, elementData) {
      element.nod(elementData.options.metrics, elementData.options.options);
    }
  };
})(jQuery);