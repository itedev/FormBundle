(function($) {
  SF.fn.plugins['knob'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('kontroled');
    },

    initialize: function($element, pluginData) {
      $element.knob(pluginData.options);
    },

    setValue: function($element) {
      $element.trigger('change');

      return true;
    }
  });
})(jQuery);