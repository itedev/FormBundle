(function($) {
  SF.fn.plugins['x_editable'] = new SF.classes.Plugin({
    isApplied: function(element) {
      return false;
    },

    apply: function(element, elementData) {
      var extras = elementData.extras;
      var options = elementData.options;

      if (extras.hasOwnProperty('view_transformer')) {
        var viewTransformer = extras['view_transformer'];
        var newParams = elementData.options['params'];

        options['params'] = function(params) {
          params = $.extend(params, newParams);

          if ('boolean' === viewTransformer) {
            if ($.isArray(params['value'])) {
              params['value'] = params['value'][0];
            }
          } else if (-1 !== $.inArray(viewTransformer, ['datetime', 'date', 'time'])) {
            var parts = params['value'].split(',');

            if ('datetime' === viewTransformer) {
              params['value'] = {
                date: {
                  year: parts[0],
                  month: parts[1],
                  day: parts[2]
                },
                time: {
                  hour: parts[3]
                }
              };
              if (parts.length >= 5) {
                params['value']['time']['minute'] = parts[4];
                if (6 === parts.length) {
                  params['value']['time']['second'] = parts[5];
                }
              }
            } else if ('date' === viewTransformer) {
              params['value'] = {
                year: parts[0],
                month: parts[1],
                day: parts[2]
              };
            } else if ('time' === viewTransformer) {
              params['value'] = {
                hour: parts[0]
              };
              if (parts.length >= 2) {
                params['value']['minute'] = parts[1];
                if (3 === parts.length) {
                  params['value']['second'] = parts[2];
                }
              }
            }
          }

          return params;
        }
      }

      element.editable(options);
    }
  });
})(jQuery);