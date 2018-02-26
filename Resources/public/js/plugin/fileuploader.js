(function ($) {
  SF.fn.plugins['fileuploader'] = new SF.classes.Plugin({
    isInitialized: function ($element, pluginData, formView) {
      var $fileElement = $('#' + formView.getChild(formView.getOption('child_names')['file']).getId());

      return 0 !== $fileElement.closest('.fileuploader').length;
    },

    initialize: function ($element, pluginData, formView) {
      var $fileElement = $('#' + formView.getChild(formView.getOption('child_names')['file']).getId());
      var $dataElement = $('#' + formView.getChild(formView.getOption('child_names')['data']).getId());

      var baseUploadOnSuccess = 'undefined' !== typeof pluginData.options.upload && 'undefined' !== typeof pluginData.options.upload.onSuccess
        ? pluginData.options.upload.onSuccess
        : null;
      var baseOnRemove = 'undefined' !== typeof pluginData.options.onRemove
        ? pluginData.options.onRemove
        : null;
      var files = 'undefined' !== typeof pluginData.options.files ? pluginData.options.files : [];

      var dataData = $dataElement.val() ? JSON.parse($dataElement.val()) : [];
      $.each(dataData, function (i, file) {
        files.push({
          file: file.fileName,
          name: file.originalName,
          type: file.type,
          size: file.size
        });
      });

      $.extend(true, pluginData.options, {
        files: files,
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

            if (null !== baseUploadOnSuccess) {
              baseUploadOnSuccess.apply(this, [data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR]);
            }
          }
        },

        onRemove: function (item, listEl, parentEl, newInputEl, inputEl) {
          var originalName = item.name;

          var dataData = $dataElement.val() ? JSON.parse($dataElement.val()) : [];
          $.each(dataData, function (i, file) {
            if (originalName === file.originalName) {
              dataData.splice(i, 1);
            }

            return false;
          });
          $dataElement.val(JSON.stringify(dataData));

          if (null !== baseOnRemove) {
            baseOnRemove.apply(this, [item, listEl, parentEl, newInputEl, inputEl]);
          }

          return true;
        },
      });

      $fileElement.fileuploader(pluginData.options);
    }
  });
})(jQuery);
