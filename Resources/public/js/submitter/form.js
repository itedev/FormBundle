/**
 * Created by c1tru55 on 03.07.15.
 */
(function($) {
  var submitter = new SF.fn.classes.Submitter();

  SF.fn.submitters['form'] = {
    isInitialized: function($element) {
      return SF.util.hasEvent($element, 'submit.form-plugin');
    },

    initialize: function($element, submitterData) {
      $element.ajaxForm(options);
    }
  };
})(jQuery);