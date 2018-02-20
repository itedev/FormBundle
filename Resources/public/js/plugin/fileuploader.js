(function ($) {
  SF.fn.plugins['fileuploader'] = new SF.classes.Plugin({
    isInitialized: function ($element) {
      return false;
    },

    initialize: function ($element, pluginData, formView) {
      var $fileElement = $('#' + formView.getChild(formView.getOption('child_names')['file']).getId());
      var $dataElement = $('#' + formView.getChild(formView.getOption('child_names')['data']).getId());

      $.extend(true, pluginData.options, {
        upload: {
          onSuccess: function (data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR) {
            var dataData = $dataElement.val() ? JSON.parse($dataElement.val()) : [];
            $.each(data.files, function (i, file) {
              dataData.push({
                fileName: file.name,
                originalName: file.old_name,
                type: file.type,
                size: file.size
              });
            });
            $dataElement.val(JSON.stringify(dataData));
            // console.log(arguments);
            // console.log(data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR);
          }
        }
      });

      $fileElement.fileuploader(pluginData.options);
    }
  });
})(jQuery);
