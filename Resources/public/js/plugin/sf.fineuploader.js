(function($) {
  SF.elements.fn.isFineuploaderPluginApplied = function(element) {
    return 'undefined' !== typeof element.data('fineuploader');
  };

  SF.elements.fn.applyFineuploaderPlugin = function(element, elementData) {
    var options = elementData.options;

    var propertyPath = element.data('property-path');
    var url = SF.util.addGetParameter(options['request']['endpoint'], 'propertyPath', propertyPath);

    options = $.extend(true, options, {
      request: {
        endpoint: url
      }
    });

    element.fineUploader(options);
  };
})(jQuery);