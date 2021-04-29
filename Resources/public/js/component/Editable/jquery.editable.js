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
    this.each(function () {
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

  function submitActiveForms() {
    $('.ite-editable-active:not(.ite-editable-sending)').each(function () {
      $(this).find('.ite-editable-submit-link').trigger('click');
    });
  }

  $(function () {

    $('body')
      .on('keyup', function (e) {
        if ('Escape' === e.key) {
          hideAllActiveForms();
        } else if ('Enter' === e.key) {
          submitActiveForms();
        }
      })
      .on('click', '.ite-editable-edit-link', function (e) {
        var $this = $(this);

        hideAllActiveForms();

        var $container = $this.closest('.ite-editable');

        if ($container.hasClass('ite-editable-inline')) {
          $container.addClass('ite-editable-active');

          return false;
        }
      })
      .on('click', '.ite-editable-cancel-link', function (e) {
        var $this = $(this);

        var $container = $this.closest('.ite-editable');

        $container.removeClass('ite-editable-active');

        return false;
      })
      .on('click', '.ite-editable-submit-link', function (e) {
        var $this = $(this);

        var $container = $this.closest('.ite-editable');
        var $form = $container.find('form');
        var $textContainer = $container.find('.ite-editable-text');
        var $formContainer = $container.find('.ite-editable-form');

        if (!$form.length) {
          $form = $container.find('.form-inline');
        }

        var data = {
          class: $container.attr('data-ite-editable-class'),
          identifier: $container.attr('data-ite-editable-identifier'),
          field: $container.attr('data-ite-editable-field'),
          options: $container.attr('data-ite-editable-options')
        };

        $container.addClass('ite-editable-sending');
        $.ajax({
          type: $form.attr('method'),
          url: $form.attr('action'),
          data: $.param(data) + '&' + $form.find(':input').serialize(),
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

        return false;
      })
    ;

  });

  $.fn.editable.defaults = {};

})(jQuery);
