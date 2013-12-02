(function ($) {
  "use strict";

  var Constructor = function(options) {
    this.init('tinymce', options, Constructor.defaults);

    //extend tinymce manually as $.extend not recursive
    this.options.tinymce = $.extend(true, {}, Constructor.defaults.tinymce, options.tinymce);
  };

  $.fn.editableutils.inherit(Constructor, $.fn.editabletypes.abstractinput);

  $.extend(Constructor.prototype, {
    render: function () {
      var deferred = $.Deferred(),
        msieOld;

      //generate unique id as it required for tinymce
      this.$input.attr('id', 'textarea_'+(new Date()).getTime());

      this.setClass();
      this.setAttr('placeholder');

      //resolve deffered when widget loaded
      $.extend(true, this.options.tinymce, {
        setup: function(editor) {
          editor.on('init', function(e) {
            // dirty hack
            setTimeout(function() {
              deferred.resolve();
            }, 100);
          });
        }
      });

      this.$input.tinymce(this.options.tinymce);

      /*
       In IE8 wysihtml5 iframe stays on the same line with buttons toolbar (inside popover).
       The only solution I found is to add <br>. If you fine better way, please send PR.
       */
      msieOld = /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase());
      if(msieOld) {
        this.$input.before('<br><br>');
      }

      return deferred.promise();
    },

    value2html: function(value, element) {
      $(element).html(value);
    },

    html2value: function(html) {
      return html;
    },

    value2input: function(value) {
      tinymce.get(this.$input.attr('id')).setContent(value || '');
    },

    activate: function() {
      tinymce.get(this.$input.attr('id')).focus();
    },

    isEmpty: function($element) {
      if($.trim($element.html()) === '') {
        return true;
      } else if($.trim($element.text()) !== '') {
        return false;
      } else {
        //e.g. '<img>', '<br>', '<p></p>'
        return !$element.height() || !$element.width();
      }
    }
  });

  Constructor.defaults = $.extend(true, {}, $.fn.editabletypes.abstractinput.defaults, {
    /**
     @property tpl
     @default <textarea></textarea>
     **/
    tpl:'<textarea></textarea>',
    /**
     @property inputclass
     @default editable-tinymce
     **/
    inputclass: 'editable-tinymce',
    /**
     Placeholder attribute of input. Shown when input is empty.

     @property placeholder
     @type string
     @default null
     **/
    placeholder: null,
    /**
     Wysihtml5 default options.
     See https://github.com/jhollingworth/bootstrap-wysihtml5#options

     @property tinymce
     @type object
     @default {}
     **/
    tinymce: {}
  });

  $.fn.editabletypes.tinymce = Constructor;

}(window.jQuery));