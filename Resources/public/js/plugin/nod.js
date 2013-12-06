(function($) {
  SF.fn.plugins['nod'] = {
    isApplied: function(element) {
      return element.get(0).hasOwnProperty('__nod');
    },

    apply: function(element, elementData) {
      element.nod(elementData.options.metrics, elementData.options.options);
    }
  };
})(jQuery);