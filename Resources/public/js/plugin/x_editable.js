(function($) {
  SF.fn.plugins['x_editable'] = {
    isApplied: function(element) {
      return false;
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      element.editable(options);
    }
  };
})(jQuery);