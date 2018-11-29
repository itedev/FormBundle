(function($) {

  var Editable = function (element, options) {
    this.element = $(element);

    $.extend(this, $.fn.editable.defaults, options);
  };

  Editable.prototype = {
    constructor: Editable
  };

  $.fn.editable = function (option) {
    var methodArguments = arguments, value;
    this.each(function() {
      var $this = $(this);

      var data = $this.data('editable');
      if (!data) {
        var options = 'object' === typeof option ? option : {};
        $this.data('editable', (data = new Editable(this, options)));
      }
      if ('string' === $.type(option)) {
        if ($.isFunction(data[option])) {
          value = data[option].apply(data, Array.prototype.slice.call(methodArguments, 1));
        } else {
          $.error('Method with name "' +  option + '" does not exist in jQuery.editable');
        }
      }
    });

    return ('undefined' === typeof value) ? this : value;
  };

  function hideAllActiveForms() {
    $('.ite-editable-active:not(.ite-editable-sending)').each(function () {
      $(this).removeClass('ite-editable-active');
    });
  }

  $(function () {

    $('body')
      .on('keyup', function (e) {
        if ('Escape' === e.key) {
          hideAllActiveForms();
        }
      })
      .on('click', '.ite-editable-edit-link', function (e) {
        var $this = $(this);

        hideAllActiveForms();

        var $container = $this.closest('.ite-editable');

        $container.addClass('ite-editable-active');

        return false;
      })
      .on('change', '.ite-editable-form', function (e) {
        var $this = $(e.target);

        var $form = $this.closest('form');
        var $container = $form.closest('.ite-editable');
        var $textContainer = $container.find('.ite-editable-text');
        var $formContainer = $container.find('.ite-editable-form');

        var data = {
          class: $form.attr('data-ite-editable-class'),
          identifier: $form.attr('data-ite-editable-identifier'),
          field: $form.attr('data-ite-editable-field'),
          options: $form.attr('data-ite-editable-options')
        };

        $container.addClass('ite-editable-sending');
        $.ajax({
          type: $form.attr('method'),
          url: $form.attr('action'),
          data: $.param(data) + '&' + $form.serialize(),
          dataType: 'json',
          success: function (response) {
            var $html = $(response.html);
            if (response.success) {
              // success
              $textContainer.html($html.find('.ite-editable-text').html());

              $container.removeClass('ite-editable-active');
            } else {
              // error
              $formContainer.html($html.find('.ite-editable-form').html());
            }
          },
          complete: function (jqXhr, textStatus) {
            $container.removeClass('ite-editable-sending');
          }
        });
      })
    ;

  });

  $.fn.editable.defaults = {};

})(jQuery);
