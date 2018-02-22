(function($) {
  SF.fn.plugins['select2'] = new SF.classes.Plugin({
    isInitialized: function ($element) {
      return 'SELECT' !== $element.prop('tagName') || 'undefined' !== typeof $element.data('select2');
    },

    destroy: function ($element) {
      if (!this.isInitialized($element)) {
        return;
      }

      $element.select2('destroy');
    },

    initialize: function ($element, pluginData) {
      var extras = pluginData.extras;
      var options = pluginData.options;
      var self = this;

      // google fonts
      if (extras.hasOwnProperty('google_fonts')) {
        options = $.extend(true, {
          formatResult: function (state) {
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
            var term = $.trim(params.term);

            if ('' === term) {
              return null;
            }

            var createOptionText = (extras.hasOwnProperty('create_option_format') && 'string' === typeof extras['create_option_format'])
              ? extras['create_option_format'].replace('%term%', term)
              : term;

            if (extras.hasOwnProperty('case_sensitive') && extras.case_sensitive === false) {
              var optionsMatch = false;

              $.each(this._request.responseJSON, function () {
                if (this.text.toLowerCase() == term.toLowerCase()) {
                  optionsMatch = true;
                }
              });

              this.$element.find('option').each(function() {
                if (this.value.toLowerCase().indexOf(term.toLowerCase()) > -1) {
                  optionsMatch = true;
                }
              });

              if (optionsMatch) {
                return null;
              }
            }

            return {
              id: term,
              text: createOptionText,
              isNew: true
            };
          }
        }, options);

        $element.on('select2:selecting', function(e) {
          var selection = e.params.args.data;
          if (!selection.hasOwnProperty('isNew')) {
            return;
          }

          $element.select2('close');
          e.preventDefault();

          var event = $.Event('before-create-option.ite.plugin.select2', {
            text: selection.id
          });
          $element.trigger(event);
          if (false === event.result) {
            return false;
          }

          if (extras.hasOwnProperty('create_url') && 'string' === typeof extras['create_url']) {
            $.ajax({
              type: 'post',
              url: extras.create_url,
              data: {
                text: selection.id
              },
              dataType: 'dataType' in options.ajax ? options.ajax.dataType : 'json',
              success: function (response) {
                if ($.isPlainObject(response) && response.hasOwnProperty('id') && response.hasOwnProperty('text')) {
                  self.addOption($element, response);
                }
              }
            });
          } else {
            self.addOption($element, selection);
          }

          return false;
        })
      }

      $element.on('select2:select', function (e) {
        var data = e.params.data;
        var $option = $element.find('option[value="' + data.id + '"]');

        if ($option.length > 0) {
          self.processOptionOptions($element, $option, data);
        } else {
          self.addOption($element, data);
        }
      });

      $element.select2(options);
    },

    processOptionOptions: function ($element, $option, data) {
      if (typeof data.options !== 'undefined') {
        var event = $.Event('process-option-options.ite.plugin.select2', {
          option: $option,
          data: data
        });
        $element.trigger(event);

        $.each(data.options, function (optionName, options) {
          switch (optionName) {
            case 'attr':
              $.each(options, function (name, value) {
                if (typeof value === 'object' || typeof value === 'array') {
                  value = JSON.stringify(value);
                }

                $option.attr(name, value);
              });
              break;
          }
        });
      }
    },

    addOption: function ($element, data, setValue) {
      setValue = setvalue || true;
      var $option = this.createOptionFromData($element, data);

      $element
        .append($option)
      ;

      if (setValue) {
        $element
          .val(data.id)
          .trigger('change')
        ;
      }
    },

    createOptionFromData: function ($element, data) {
      var $option = $('<option value="' + data.id + '">' + data.text + '</option>');

      this.processOptionOptions($element, $option, data);

      return $option;
    },

    clearValue: function ($element) {
      $element
        .html('')
        .val('')
        .triggerHandler('change.select2')
      ;

      return true;
    },

    setValue: function ($element, $newElement) {
      $element
        .html($newElement.html())
        .val($newElement.val())
        .triggerHandler('change.select2')
      ;

      return true;
    }
  });
})(jQuery);