(function($) {
  function addGetParameter(url, paramName, paramValue) {
    var urlParts = url.split('?', 2);
    var baseURL = urlParts[0];
    var queryString = [];
    if (urlParts.length > 1) {
      var parameters = urlParts[1].split('&');
      for (var i = 0; i < parameters.length; ++i) {
        if (parameters[i].split('=')[0] != paramName) {
          queryString.push(parameters[i]);
        }
      }
    }
    queryString.push(paramName + '=' + encodeURIComponent(paramValue));

    return baseURL + '?' + queryString.join('&');
  }

  SF.elements.fn.isFileuploadPluginApplied = function(element) {
    return element.data('blueimp-fileupload');
  };

  SF.elements.fn.applyFileuploadPlugin = function(element, elementData) {
    var options = elementData.options;

    options = $.extend(true, options, {
      url: addGetParameter(options['url'], 'paramName', options['paramName'])
    });

    element.fileupload(options);
  };
})(jQuery);