(function($) {
  SF.fn.plugins['icheck'] = {
    isInitialized: function($element) {
      return !$element.is($element.icheck('data'));
//      return 'undefined' !== typeof $element.data('iCheck');
    },

    initialize: function($element, pluginData, view) {
      $element.icheck(pluginData.options);
//      if (view.hasOption('delegate_selector')) {
//        $element.find(view.getOption('delegate_selector')).iCheck(pluginData.options);
//      } else {
//        $element.iCheck(pluginData.options);
//      }
    },

    setValue: function($element, $newElement) {
      $element
        .html($newElement.html())
        .removeData('sfInitialized')
      ;
    }
  };
})(jQuery);