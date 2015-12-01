(function($) {
  SF.fn.plugins['icheck'] = new SF.classes.Plugin({
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

      return true;
    }
  });
})(jQuery);