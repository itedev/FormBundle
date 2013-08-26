(function($) {
  SF.elements.fn.isBootstrapDateTimePickerPluginApplied = function(element) {
    return 'undefined' !== typeof element.data('datetimepicker');
  };

  SF.elements.fn.applyBootstrapDateTimePickerPlugin = function(element, elementData) {
    element.datetimepicker(elementData.options);
  };
})(jQuery);