/**
 * Created by c1tru55 on 29.12.15.
 */
(function($) {
  SF.fn.plugins['ckeditor'] = new SF.classes.Plugin({
    isInitialized: function($element) {
      return 'undefined' !== typeof window.CKEDITOR
        && 'undefined' !== typeof window.CKEDITOR.instances[$element.attr('id')];
    },

    initialize: function($element, pluginData, view) {
      $element.ckeditor(pluginData.options);
      //CKEDITOR.replace($element.attr('id'), pluginData.options);
    },

    setValue: function($element, $newElement, view) {
      $.when($element.val($newElement.val())).then(function() {
        // data is set
      });
    }
  });
})(jQuery);