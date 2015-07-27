(function($) {
  SF.fn.plugins['icheck'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('iCheck');
    },

    initialize: function($element, pluginData, view) {
      if (view.hasOption('delegate_selector')) {
        $element.find(view.getOption('delegate_selector')).iCheck(pluginData.options);
      } else {
        $element.iCheck(pluginData.options);
      }
    }

//    setValue: function($element) {
//      $element.trigger('change');
//    }
  };
})(jQuery);