(function($) {
  SF.fn.plugins['select2'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof element.data('select2');
    },

    apply: function(element, elementData) {
      var extras = elementData.extras;
      var options = elementData.options;

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
        var property = element.data('property');

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

      // create
      if ('allow_create' in extras && extras.allow_create === true) {
        options = $.extend(true, options, {
          createSearchChoice: function(term, data) {
            if ($(data).filter(function() {
              return this.text.localeCompare(term) === 0;
            }).length === 0) {
              return {
                id: term,
                text: term,
                dynamic: true
              };
            }
          }
        });

        element.on('select2-selecting', function(e) {
          if (!('dynamic' in e.object)) {
            return;
          }

          $.ajax({
            type: 'post',
            url: extras.create_url,
            data: {
              text: e.object.text
            },
            dataType: 'dataType' in options.ajax ? options.ajax.dataType : 'json',
            success: function(response) {
              if ($.isPlainObject(response) && 'id' in response && 'text' in response) {
                element.select2('data', response);
              } else {
                element.select2('val', '');
              }
            }
          }).fail(function() {
              element.select2('val', '');
            });
        });
      }

      element.select2(options);
    },

    clearValue: function(element) {
      element.select2('val', '');
    },

    setValue: function($element, $newElement) {
      $element.html($newElement.html());
      $element.val($newElement.val());
    }
  };
})(jQuery);