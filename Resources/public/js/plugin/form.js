(function($) {
  SF.fn.plugins['form'] = new SF.classes.Plugin({
    isApplied: function(element) {
      return SF.util.hasEvent(element, 'submit.form-plugin');
    },

    apply: function(element, elementData) {
      var extras = elementData.extras;
      var options = elementData.options;

      if (extras.hasOwnProperty('')) {

      } else {

      }

      var successCallback = options.hasOwnProperty('success') ? options['success'] : null;

      options = $.extend(true, options, {
        success: function(responseText, statusText, xhr, form) {
          if (SF.parameters.has('flashes_selector')) {
            SF.ui.clearFlashes(SF.parameters.get('flashes_selector'));
          }
          SF.plugins['form'].clearFormErrors(form);

          var formErrorsHeader = xhr.getResponseHeader('X-SF-FormErrors');
          if (formErrorsHeader) {
            var formErrors = $.parseJSON(formErrorsHeader);

//          var globalErrors = formErrors['errors'];

            $.each(formErrors['children'], function(childName, childData) {
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
    },

    clearFormErrors: function(form) {
      form.find('.control-group.error').each(function() {
        $(this).removeClass('error');
      });

      form.find('.sf-error').remove();
    }
  });
})(jQuery);