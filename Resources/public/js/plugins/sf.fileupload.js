(function($) {
  SF.elements.fn.isFileuploadPluginApplied = function(element) {
    return element.data('blueimp-fileupload');
  };

  SF.elements.fn.applyFileuploadPlugin = function(element, elementData) {
    element.fileupload(elementData.options);
  };
})(jQuery);