(function($) {
  SF.elements.fn.isSelect2PluginApplied = function(element) {
    return 'undefined' !== typeof element.data('select2');
  };

  SF.elements.fn.applySelect2Plugin = function(element, elementData) {
    var extras = elementData.extras;
    var options = elementData.options;

    // ajax
    if (extras.hasOwnProperty('ajax')) {
      var initSelectionCallback = options.hasOwnProperty('initSelection') ? options['initSelection'] : null;
      var property = element.data('property');

      options = $.extend(true, options, {
        initSelection: function(el, callback) {
          if (el.val()) {
            callback(el.data('default-value'));
          }

          // call initSelection callback - if set
          if (initSelectionCallback) {
            initSelectionCallback.call(element, el, callback);
          }
        },
        ajax: {
          data: function(term, page) {
            return {
              term: term,
              page: page,
              property: property
            };
          },
          results: function(data, page) {
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
  };
})(jQuery);