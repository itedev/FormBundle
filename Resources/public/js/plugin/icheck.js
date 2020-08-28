(function($) {
  SF.fn.plugins['icheck'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return !$element.is($element.icheck('data'));
    },

    destroy: function ($element) {
      if (!this.isInitialized($element)) {
        return;
      }

      $element.icheck('destroy');
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
    },

    setData: function ($element, data, view) {
      let delegateSelector = view.getOption('delegate_selector', false);

      if (delegateSelector) {
        // checkbox or radio
        $element.find(delegateSelector).each(function() {
          var checked = ':radio' === delegateSelector
            ? this.value === data
            : -1 !== $.inArray(this.value, data);
          $(this).prop('checked', checked);
        });
        $element.icheck('updated');
      } else {
        $element.icheck(data ? 'checked' : 'unchecked');
      }

      return true;
    }
  });
})(jQuery);