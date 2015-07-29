(function($) {
  SF.fn.plugins['knob'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('kontroled');
    },

    initialize: function($element, pluginData) {
      $element.knob(pluginData.options);
    },

    setValue: function($element) {
      $element.trigger('change');
    }
  };
})(jQuery);