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

    var name = element.is('input[type="file"]')
      ? element.attr('name')
      : element.find('input[type="file"]').attr('name');
    var property = name.substr(name.indexOf('['));

    var url = options['url'];
    url = addGetParameter(url, 'paramName', options['paramName']);
    url = addGetParameter(url, 'property', property);

    options = $.extend(true, options, {
      url: url,
      uploadTemplate: function (o) {
        return Twig.render(template_upload, {o: o});
      },
      downloadTemplate: function (o) {
        return Twig.render(template_download, {o: o});
      }
    });

    element.fileupload(options);
  };
})(jQuery);