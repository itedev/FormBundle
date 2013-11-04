(function($) {
  SF.elements.fn.isBootstrapColorpickerPluginApplied = function(element) {
    return 'undefined' !== typeof element.data('colorpicker');
  };

  SF.elements.fn.applyBootstrapColorpickerPlugin = function(element, elementData) {
    var options = elementData.options;

    element.colorpicker(options);
  };
})(jQuery);