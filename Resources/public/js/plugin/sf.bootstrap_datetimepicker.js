(function($) {
  SF.elements.fn.isBootstrapDatetimepickerPluginApplied = function(element) {
    return 'undefined' !== typeof element.data('datetimepicker');
  };

  SF.elements.fn.applyBootstrapDatetimepickerPlugin = function(element, elementData) {
    element.datetimepicker(elementData.options);
  };
})(jQuery);