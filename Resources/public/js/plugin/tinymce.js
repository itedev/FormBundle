(function($) {
  SF.plugins['tinymce'] = {
    isApplied: function(element) {
      return 'undefined' !== typeof window.tinyMCE
        && 'undefined' !== typeof window.tinyMCE.get(element.attr('id'));
      // !!(element.id && 'tinymce' in window && tinymce.get(element.id))
    },

    apply: function(element, elementData) {
      var options = elementData.options;

      if (element.is('[required]')) {
        options.oninit = function(editor) {
          editor.onChange.add(function(ed, l) {
            ed.save();
          });
        };
      }

      element.tinymce(options);
    }
  };
})(jQuery);