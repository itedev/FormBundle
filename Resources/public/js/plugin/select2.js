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

        options = $.extend(true, {
          ajax: {
            data: function (params) {
              return {
                term: params.term,
                page: params.page,
                property: property
              };
            },
            processResults: function (data) {
              return {
                results: data
              };
            }
          }
        }, options);
      }

      // create
      if (extras.hasOwnProperty('allow_create') && extras.allow_create === true) {
        options = $.extend(true, {
          tags: true,
          createTag: function (params) {
            //var results = arguments.callee.caller.arguments[0].results;
            //if (0 === results.length) {
              return {
                id: params.term,
                text: params.term,
                isNew: true
              };
            //}
          }
        }, options);
        $element.on('select2:selecting', function(e) {
          var selection = e.params.args.data;
          if (!selection.hasOwnProperty('isNew')) {
            return;
          }

          $element.select2('close');
          e.preventDefault();

          $.ajax({
            type: 'post',
            url: extras.create_url,
            data: {
              text: selection.id
            },
            dataType: 'dataType' in options.ajax ? options.ajax.dataType : 'json',
            success: function(response) {
              if ($.isPlainObject(response) && response.hasOwnProperty('id') && response.hasOwnProperty('text')) {
                $element
                  .html('<option value="' + response.id + '">' + response.text + '</option>')
                  .val(response.id)
                  .trigger('change')
                ;
              }
            }
          });
        });
      }

      $element.select2(options);
    },

    clearValue: function($element) {
      $element
        .html('')
        .val('')
      ;
    },

    setValue: function($element, $newElement) {
      $element
        .html($newElement.html())
        .val($newElement.val())
        .triggerHandler('change.select2')
      ;
    }
  };
})(jQuery);