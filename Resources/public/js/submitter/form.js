/**
 * Created by c1tru55 on 03.07.15.
 */
(function($) {

  SF.fn.submitters['form'] = new SF.classes.Submitter({
    isInitialized: function ($form) {
      return SF.util.hasEvent($form, 'submit.form-plugin');
    },

    initialize: function ($form, submitterData) {
      var options = submitterData.options;

      // disable options:
      // clearForm (?)
      // dataType (?)
      // replaceTarget (?)
      // target (?)

      var beforeSubmit = 'undefined' !== typeof options['beforeSubmit'] ? typeof options['beforeSubmit'] : function () {};
      var success = 'undefined' !== typeof options['success'] ? typeof options['success'] : function () {};
      var context = options.context || $form.get(0);

      $.extend(options, {
        beforeSubmit: function (arr, $form, options) {
          var event = $.Event('before-submit.ite.modal');
          $form.trigger(event);
          if (false === event.result) {
            return false;
          }

          beforeSubmit.call(context, [arr, $form, options]);
        },

        success: function (response, status, jqXHR) {
          //$form.trigger('');

          var target, replaceTarget, content;
          if ('replace' || 'form') {
            if ('replace') {
              target = submitterData['target'];
              replaceTarget = submitterData['replace_target'];
            } else if ('form') {
              target = '#' + $form.attr('id');
              replaceTarget = false;

            }

            var replaceMethod = replaceTarget ? 'replaceWith' : 'html';
            var $target = $(target)[replaceMethod](response);
          } else if ('errors') {
            $form.resetErrorsRecursive();
            $form.showErrorsRecursive();
          }

          success.call(context, [data, status, jqXHR || $form, $form]);
        }
      });

      $form.ajaxForm(options);
    },

    submit: function ($form, options) {
      $form.ajaxSubmit(options);
    }
  });

})(jQuery);
