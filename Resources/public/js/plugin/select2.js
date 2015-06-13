(function($) {
  SF.fn.plugins['select2'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('select2');
    },

    initialize: function($element, pluginData) {
      var extras = pluginData.extras;
      var options = pluginData.options;

      // google fonts
      if (extras.hasOwnProperty('google_fonts')) {
        options = $.extend(true, options, {
          formatResult: function(state) {
            var option = $(state.element);
            return '<div style="height: 28px; background: url(/bundles/iteform/img/google_fonts.png); background-position: 0 -' + ((option.index() * 30) - 2) + 'px;"></div>';
          }
        });
      }

      // ajax
      if (extras.hasOwnProperty('ajax')) {
        var property = $element.data('property');

        options = $.extend(true, options, {
          ajax: {
            data: function(params) {
              return {
                term: params.term,
                page: params.page,
                property: property
              };
            },
            processResults: function(data) {
              return {
                results: data
              };
            }
          }
        });
      }

      $element.select2(options);
    },

    clearValue: function($element) {
      $element.select2('val', '');
    },

    setValue: function($element, $newElement) {
      $element.html($newElement.html());
      $element.val($newElement.val());
    }
  };
})(jQuery);