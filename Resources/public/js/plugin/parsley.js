(function($) {
  SF.fn.plugins['parsley'] = new SF.classes.Plugin({
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('parsleyForm');
    },

    apply: function(element, elementData) {
      var extras = elementData.extras;
      var options = elementData.options;

      $.each(extras.constraints, function(index, constraint) {
        var field = $(constraint.selector, element);
        if (!field.length) {
          return;
        }

        $.each(constraint.attr, function(name, value) {
          field.attr(name, value);
        });
      });

      element.parsley(options);
    }
  });
})(jQuery);