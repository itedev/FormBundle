(function($) {
  SF.elements.fn.isBootstrapDatetimepicker2PluginApplied = function(element) {
    return 'undefined' !== typeof element.data('datetimepicker');
  };

  SF.elements.fn.applyBootstrapDatetimepicker2Plugin = function(element, elementData) {
    element.datetimepicker(elementData.options);
  };
})(jQuery);