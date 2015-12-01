(function($) {
  SF.fn.plugins['fineuploader'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('fineuploader');
    },

    initialize: function($element, pluginData) {
      var options = pluginData.options;

      var propertyPath = $element.data('property-path');
      var url = SF.util.addGetParameter(options['request']['endpoint'], 'propertyPath', propertyPath);

      options = $.extend(true, options, {
        request: {
          endpoint: url
        }
      });

      $element.fineUploader(options);
    }
  });
})(jQuery);