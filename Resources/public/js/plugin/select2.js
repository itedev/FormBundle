(function($) {
  SF.fn.plugins['select2'] = new SF.classes.Plugin({
    isInitialized: function ($element) {
      return 'SELECT' !== $element.prop('tagName') || 'undefined' !== typeof $element.data('select2');
    },

    destroy: function ($element) {
      if (!this.isInitialized($element)) {
        return;
      }

      $element.off('.plugin-select2.ite');

      $element.select2('destroy');
    },

    initialize: function ($element, pluginData) {
      var extras = pluginData.extras;
      var options = pluginData.options;
      var self = this;

      options = $.extend(true, {
        templateResult: function (data) {
          if (data.element && true === $(data.element).data('hidden')) {
            return null;
          }

          return data.text;
        }
      }, options);

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

        $element.sfBindFirst('change.plugin-select2.ite', function (e) {
          var value = $element.val();

          var $option = $element.find('option[value="' + value + '"]');
          if ($option.length > 0) {
            var data = 'undefined' !== typeof $option.data('data') ? $option.data('data') : {};
            self.processOptionOptions($element, $option, data);
          }
        });
      }

      // dynamic
      if (extras.hasOwnProperty('dynamic')) {
        var domain = extras['domain'];

        if (!extras['preload_choices']) {
          $element
            .on('select2:opening.plugin-select2.ite', function (e) {
              if ($element.select2('isOpen')) {
                return;
              }

              var choices = SF.dynamicChoiceDomains.get(domain);
              var selectedValue = $element.val();

              $element.children('option[value!=""]').remove();
              $.each(choices, function (value, label) {
                var option = new Option(label, value, selectedValue === value, selectedValue === value);
                $element.append(option);
              });
            })
            .on('select2:close.plugin-select2.ite', function (e) {
              $element.children('option[value!=""]:not(:selected)').remove();
            })
          ;
        } else {
          $element
            .on('ite:select2:before-create-option.plugin-select2.ite', function (e) {
              $('[data-dynamic-choice-domain="' + domain + '"]').each(function () {
                var $sibling = $(this);

                if ($sibling.is($element)) {
                  return;
                }

                if (0 === $sibling.children('option[value="' + e.text + '"]').length) {
                  $sibling.append('<option value="' + e.text + '">' + e.text + '</option>');
                }
              });
            })
          ;
        }

        $element.on('ite:select2:before-create-option.plugin-select2.ite', function (e) {
          var choices = SF.dynamicChoiceDomains.get(domain);
          if (!choices.hasOwnProperty(e.text)) {
            choices[e.text] = e.text;
            SF.dynamicChoiceDomains.set(domain, choices);
          }
        });
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

              if ('undefined' !== typeof this._request) {
                $.each(this._request.responseJSON, function () {
                  if (this.text.toLowerCase() == term.toLowerCase()) {
                    optionsMatch = true;
                  }
                });
              }

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

        $element.on('select2:selecting.plugin-select2.ite', function (e) {
          var selection = e.params.args.data;
          if (!selection.hasOwnProperty('isNew')) {
            return;
          }

          $element.select2('close');
          e.preventDefault();

          var event = $.Event('ite:select2:before-create-option', {
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
              dataType: 'undefined' !== typeof options.ajax && 'dataType' in options.ajax ? options.ajax.dataType : 'json',
              success: function (response) {
                if ($.isPlainObject(response)) {
                  if (response.hasOwnProperty('id') && response.hasOwnProperty('text')) {
                    $element.formView().resetErrors($element);

                    // self.clearOptions($element);
                    self.addOption($element, response);

                    var event = $.Event('ite:select2:after-create-option', {
                      option: response
                    });
                    $element.trigger(event);
                  } else if (response.hasOwnProperty('errors')) {
                    $element.formView().showErrors(response.errors, $element);
                  }
                }
              }
            });
          } else {
            $element.find('[value="' + selection.id + '"]').remove();
            // self.clearOptions($element);
            self.addOption($element, {
              id: selection.id,
              text: selection.id
            });
          }

          return false;
        })
      }

      // $element.sfBindFirst('select2:select.plugin-select2.ite', function (e) {
      //   var data = e.params.data;
      //   var $option = $element.find('option[value="' + data.id + '"]');
      //
      //   if ($option.length > 0) {
      //     self.processOptionOptions($element, $option, data);
      //   } else {
      //     self.addOption($element, data);
      //   }
      // });

      $element.select2(options);
    },

    processOptionOptions: function ($element, $option, data) {
      if ('undefined' !== typeof data.options) {
        var event = $.Event('ite:select2:process-option-options', {
          option: $option,
          data: data
        });
        $element.trigger(event);

        $.each(data.options, function (optionName, options) {
          switch (optionName) {
            case 'attr':
              $.each(options, function (name, value) {
                if ('object' === typeof value || 'array' === typeof value) {
                  value = JSON.stringify(value);
                }

                $option.attr(name, value);
              });

              break;
          }
        });
      }
    },

    addOption: function ($element, data, setValue = true, triggerEvent = true) {
      setValue = 'undefined' !== typeof setValue ? setValue : true;

      let $option = $element.children('[value="' + data.id + '"]');
      if (0 === $option.length) {
        $option = this.createOptionFromData($element, data);

        $element.append($option).triggerHandler('change.select2');
      }

      if (setValue) {
        $element.val(data.id);
      }
      if (triggerEvent) {
        $element.trigger('change');
      }

      return $option;
    },

    createOptionFromData: function ($element, data) {
      var $option = $('<option value="' + data.id + '">' + data.text + '</option>');
      this.processOptionOptions($element, $option, data);

      return $option;
    },

    clearOptions: function ($element) {
      var required = $element.prop('required');

      if (required) {
        $element.empty();
      } else {
        $element.children('option[value!=""]').remove();
      }
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
    },

    setData: function ($element, data) {
      var domain = $element.data('dynamicChoiceDomain');
      if ('undefined' !== typeof domain
        && 0 === $element.children('option[value="' + data + '"]').length) {
        var choices = SF.dynamicChoiceDomains.get(domain);

        $.each(choices, function (value, label) {
          if (data == value) {
            var option = new Option(label, value);
            $element.append(option);

            return false; // break
          }
        });
      }

      $element
        .val(data)
        .triggerHandler('change.select2')
      ;

      return true;
    }
  });
})(jQuery);
