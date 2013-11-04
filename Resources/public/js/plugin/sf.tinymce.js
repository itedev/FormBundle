(function($) {
  SF.elements.fn.isTinymcePluginApplied = function(element) {
    return 'undefined' !== typeof window.tinyMCE
      && 'undefined' !== typeof window.tinyMCE.get(element.attr('id'));
    // !!(element.id && 'tinymce' in window && tinymce.get(element.id))
  };

  SF.elements.fn.applyTinymcePlugin = function(element, elementData) {
    var options = elementData.options;

    if (element.is('[required]')) {
      options.oninit = function(editor) {
        editor.onChange.add(function(ed, l) {
          ed.save();
        });
      };
    }

    element.tinymce(options);
  };
})(jQuery);