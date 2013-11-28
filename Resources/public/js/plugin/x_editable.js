(function($) {
  SF.fn.plugins['x_editable'] = {
    isApplied: function(element) {
      return false;
    },

    apply: function(element, elementData) {
      var extras = elementData.extras;
      var options = elementData.options;

      if (extras.hasOwnProperty('boolean')) {
        var newParams = elementData.options['params'];

        options['params'] = function(params) {
          params = $.extend(params, newParams);
          if ($.isArray(params['value'])) {
            params['value'] = params['value'][0];
          }

          return params;
        }
      }

      element.editable(options);
    }
  };
})(jQuery);