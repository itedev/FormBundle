(function($) {
  SF.fn.plugins['icheck'] = {
    isInitialized: function($element) {
      return !$element.is($element.icheck('data'));
    },

    initialize: function($element, pluginData, view) {
      $element.icheck(pluginData.options);
    },

    setValue: function($element, $newElement, view) {
      if (view.hasOption('delegate_selector')) {
        $element
          .html($newElement.html())
          .removeData('sfInitialized')
        ;
      } else {
        $element.icheck($newElement.prop('checked') ? 'checked' : 'unchecked');
      }
    }
  };
})(jQuery);