/**
 * Created by c1tru55 on 29.12.15.
 */
(function($) {
  SF.fn.plugins['ckeditor'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return false;
    },

    initialize: function($element, pluginData, view) {
      var ckeditorInstance = $element.data('ckeditorInstance')

      if (ckeditorInstance) {
        ckeditorInstance.removeAllListeners();
        CKEDITOR.remove(ckeditorInstance);
      }

      $element.ckeditor(pluginData.options);
    },

    setValue: function($element, $newElement, view) {
      $.when($element.val($newElement.val())).then(function() {
        // data is set
      });
    }
  });
})(jQuery);