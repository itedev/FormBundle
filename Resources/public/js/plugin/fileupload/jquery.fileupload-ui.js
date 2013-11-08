(function($) {

  $.widget('blueimp.fileupload', $.blueimp.fileupload, {
    options: {
      add: function(e, data) {
        if (e.isDefaultPrevented()) {
          return false;
        }
        var $this = $(this),
          that = $this.data('blueimp-fileupload') ||
            $this.data('fileupload'),
          options = that.options;
        data.context = that._renderUpload(data.files)
          .data('data', data)
          .addClass('processing');
        options.filesContainer[
          $this.prop('multiple') ? (options.prependFiles ? 'prepend' : 'append') : 'html' // only this line were modified
          ](data.context);
        that._forceReflow(data.context);
        $.when(
            that._transition(data.context),
            data.process(function () {
              return $this.fileupload('process', data);
            })
          ).always(function () {
            data.context.each(function (index) {
              $(this).find('.size').text(
                that._formatFileSize(data.files[index].size)
              );
            }).removeClass('processing');
            that._renderPreviews(data);
          }).done(function () {
            data.context.find('.start').prop('disabled', false);
            if ((that._trigger('added', e, data) !== false) &&
              (options.autoUpload || data.autoUpload) &&
              data.autoUpload !== false) {
              data.submit();
            }
          }).fail(function () {
            if (data.files.error) {
              data.context.each(function (index) {
                var error = data.files[index].error;
                if (error) {
                  $(this).find('.error').text(error);
                }
              });
            }
          });
      }
    }
  });

})(jQuery);