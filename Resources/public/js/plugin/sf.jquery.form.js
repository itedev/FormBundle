(function($) {
  SF.elements.fn.isAjaxFormPluginApplied = function(element) {
    return element.hasEvent('submit.form-plugin');
  };

  SF.elements.fn.applyAjaxFormPlugin = function(element, elementData) {
    var extras = elementData.extras;
    var options = elementData.options;

    var successCallback = options.hasOwnProperty('success') ? options['success'] : null;

    options = $.extend(true, options, {
      success: function(responseText, statusText, xhr, form) {
        if (SF.parameters.has('flashes_selector')) {
          SF.clearFlashes(SF.parameters.get('flashes_selector'));
        }
        clearFormErrors(form);

        var formErrorsHeader = xhr.getResponseHeader('X-SF-FormErrors');
        if (formErrorsHeader) {
          var formErrors = $.parseJSON(formErrorsHeader);

//          var globalErrors = formErrors['errors'];

          _.each(formErrors['children'], function(childData, childName) {
            var field = form.find('[name="' + childName + '"]');

            var controlGroup = field.closest('.control-group');
            controlGroup.addClass('error');

            var errorTemplate = '<span class="help-<%= error_type %> sf-error"><% _.each(errors, function(error) { %><%= error %><br /><% }); %></span>';
            field.after(_.template(errorTemplate, childData));
          });
        }

        // call success callback - if set
        if (successCallback) {
          successCallback.call(element, responseText, statusText, xhr, form);
        }
      }
    });

    element.ajaxForm(options);
  };

  function clearFormErrors(form) {
    form.find('.control-group.error').each(function() {
      $(this).removeClass('error');
    });

    form.find('.sf-error').remove();
  }
})(jQuery);