(function($) {
  SF.fn.plugins['typeahead'] = {
    isInitialized: function($element) {
      return 'undefined' !== typeof $element.data('tt-typeahead');
    },

    initialize: function($element, pluginData) {
      var engineOptions = $.extend(true, {
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          prepare: function(query, settings) {
            settings.data = {
              term: query
            };

            return settings;
          }
        }
      }, pluginData.engine_options);

      var source = new Bloodhound(engineOptions);

      var datasetOptions = $.extend({
        source: source
      }, pluginData.dataset_options);

      $element.typeahead(pluginData.options, datasetOptions);
    }
  };
})(jQuery);