(function($) {
  SF.elements.fn.isSelect2PluginApplied = function(element) {
    return 'undefined' !== typeof element.data('select2');
  };

  SF.elements.fn.applySelect2Plugin = function(element, elementData) {
    var extras = elementData.extras;
    var options = elementData.options;

    var initSelectionCallback = options.hasOwnProperty('initSelection') ? options['initSelection'] : null;

    if (extras.hasOwnProperty('ajax')) {
      options = $.extend(true, options, {
        initSelection: function(el, callback) {
          if (el.val()) {
            callback(el.data('default-value'));
          }

          // call initSelection callback - if set
          if (initSelectionCallback) {
            initSelectionCallback.call(element, el, callback);
          }
        },
        ajax: {
          data: function(term, page) {
            return {
              q: term
            };
          },
          results: function(data, page) {
            return {
              results: data
            };
          }
        }
      });
    }

    element.select2(options);
  };
})(jQuery);