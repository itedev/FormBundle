!function ($) {

  "use strict";

  /* Collection PUBLIC CLASS DEFINITION
   * ============================== */

  var Collection = function(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn.collection.defaults, options);

    var itemSelector = this.element.data('widget-controls') === 'true'
        ? '#' + this.element.attr('id') + ' > .controls > .collection-item'
        : '#' + this.element.attr('id') + ' > .collection-item'
      ;

    this.index = $(itemSelector).length - 1;
  };

  Collection.prototype = {
    constructor: Collection,
    add: function () {
      this.index++;

      var prototypeName = this.element.data('prototype-name');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var itemHtml = this.element.data('prototype').replace(new RegExp(prototypeName, 'g'), this.index);
      var item = $(itemHtml).attr('data-index', this.index);

      if (this.element.data('widget-controls') === 'true') {
        this.element.children('.controls:first').append(item);
      } else {
        this.element.append(item);
      }

      this.element.triggerHandler('add.ite-collection-item', [item]);
    },
    remove: function ($btn) {
      if ($btn.parents('.collection-item').length !== 0) {
        var item = $btn.closest('.collection-item');
        item.remove();
        this.element.triggerHandler('remove.ite-collection-item', [item]);
      }
    }

  };


  /* COLLECTION PLUGIN DEFINITION
   * ======================== */

  $.fn.collection = function(method) {
    var methodArguments = arguments;
    return this.each(function() {
      var $this = $(this);

      var data = $this.data('collection');
      if (!data) {
        $this.data('collection', (data = new Collection(this, {})));
      }
      if ($.isFunction(data[method])) {
        data[method].apply(data, Array.prototype.slice.call(methodArguments, 1));
      } else {
        $.error('Method with name "' +  method + '" does not exist in jQuery.collection');
      }
    });
  };

  $.fn.collection.defaults = {
    beforeAdd: function() {},
    onAdd: function() {},
    beforeRemove: function() {},
    onRemove: function() {}
  };

  $.fn.collection.Constructor = Collection;


  /* COLLECTION DATA-API
   * =============== */

  $(function () {
    // add
    $('body').on('click.collection.data-api', '[data-collection-add-btn]', function (e) {
      var $btn = $(e.target);

      var $collection = $($btn.data('collection-add-btn'));
      $collection.collection('add');

      e.preventDefault();
    });

    // remove
    $('body').on('click.collection.data-api', '[data-collection-remove-btn]', function (e) {
      var $btn = $(e.target);

      var $collection = $($btn.data('collection-remove-btn'));
      $collection.collection('remove', $btn);

      e.preventDefault();
    });
  });

}(window.jQuery);