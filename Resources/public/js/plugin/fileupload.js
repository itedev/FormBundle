(function($) {
  SF.fn.plugins['fileupload'] = {
    isInitialized: function($element) {
      return $element.data('blueimp-fileupload');
    },

    initialize: function($element, pluginData) {
      var options = pluginData.options;

      var propertyPath = $element.is('input[type="file"]')
        ? $element.attr('name')
        : $element.data('property-path');
      var url = SF.util.addGetParameter(options['url'], 'propertyPath', propertyPath);

      options = $.extend(true, options, {
        url: url,
        uploadTemplate: function (o) {
          return Twig.render(template_upload, {o: o});
        },
        downloadTemplate: function (o) {
          return Twig.render(template_download, {o: o});
        }
      });

      $element.fileupload(options);
    }
  };
})(jQuery);