/**
 * Created by c1tru55 on 20.06.15.
 */
(function($) {
  SF.fn.validators['jquery_validate'] = {
    isInitialized: function($element) {
      //return 'undefined' !== typeof $element.data('colorpicker');
    },

    initialize: function($element, validatorData) {
      $element.validate(validatorData.options);
    }
  };
})(jQuery);