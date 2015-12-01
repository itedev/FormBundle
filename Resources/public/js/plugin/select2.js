(function($) {
  SF.fn.plugins['select2'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'SELECT' !== $element.prop('tagName') || 'undefined' !== typeof $element.data('select2');
    },

    initialize: function($element, pluginData) {
      var extras = pluginData.extras;
      var options = pluginData.options;

      // google fonts
      if (extras.hasOwnProperty('google_fonts')) {
        options = $.extend(true, {
          formatResult: function(state) {
            var option = $(state.element);
            return '<div style="height: 28px; background: url(/bundles/iteform/img/google_fonts.png); background-position: 0 -' + ((option.index() * 30) - 2) + 'px;"></div>';
          }
        }, options);
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
            return {
              id: params.term,
              text: params.term,
              isNew: true
            };
          }
        }, options);
        if (extras.hasOwnProperty('create_url')) {
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
        } else {
          $element.on('select2:select', function(e) {
            var selection = e.params.data;
            if (!selection.hasOwnProperty('isNew')) {
              return;
            }

            var term = selection.id;
            $element
              .find('[value="' + term + '"]')
                .replaceWith('<option value="' + term + '">' + term + '</option>')
              .end()
              .val(term)
              .triggerHandler('change.select2')
            ;
          });
        }
      }

      $element.select2(options);
    },

    clearValue: function($element) {
      $element
        .html('')
        .val('')
        .triggerHandler('change.select2')
      ;

      return true;
    },

    setValue: function($element, $newElement) {
      $element
        .html($newElement.html())
        .val($newElement.val())
        .triggerHandler('change.select2')
      ;

      return true;
    }
  });
})(jQuery);