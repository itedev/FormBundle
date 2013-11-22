!function ($) {

  "use strict";

  /* Collection PUBLIC CLASS DEFINITION
   * ============================== */

  var Collection = function(collection) {
    var $collection = $(collection);

    this.collectionSelector = '#' + $collection.attr('id');
    this.collectionId = $collection.data('collection-id');
    this.collectionItemsSelector = this.collectionSelector + ' .collection-items:first';
    this.collectionItemSelector = this.collectionItemsSelector + ' > .collection-item';
    this.index = $(this.collectionItemSelector).length - 1;

    $.extend(true, this, $.fn.collection.defaults, $.fn.collection.collections[this.collectionId]);
  };

  Collection.prototype = {
    constructor: Collection,
    add: function () {
      var self = this;
      function afterShow() {
        // apply plugins
        var replacementTokens = {};
        $item.parents('.collection-item').each(function() {
          var parentCollectionItem = $(this);

          var parentPrototypeName = parentCollectionItem.closest('[data-collection-id]').data('prototype-name');
          replacementTokens[parentPrototypeName] = parentCollectionItem.data('index');
        });
        replacementTokens[prototypeName] = self.index;
        SF.elements.apply($item, replacementTokens);

        self.onAdd.apply($collection, [$item, $collection]);
        $collection.trigger('ite-add.collection', [$item]);
      }

      this.index++;

      var $collection = $(this.collectionSelector);
      var prototypeName = $collection.data('prototype-name');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var re = new RegExp(prototypeName, 'g');
      var itemHtml = $collection.data('prototype').replace(re, this.index);
      var $item = $(itemHtml).attr('data-index', this.index);

      var result = this.beforeAdd.apply($collection, [$item, $collection]);
      if (false === result) {
        return;
      }
      var event = $.Event('ite-before-add.collection');
      $collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
      }

      $item.hide();
      $(this.collectionItemsSelector).append($item);

      switch (this.show.type.toLowerCase()) {
        case 'fade':
          $item.fadeIn(this.show.length, afterShow);
          break;
        case 'slide':
          $item.slideDown(this.show.length, afterShow);
          break;
        case 'show':
          $item.show(this.show.length, afterShow);
          break;
        default:
          $item.show(null, afterShow);
          break;
      }
    },
    remove: function ($btn) {
      var self = this;
      function afterHide() {
        $item.remove();

        self.onRemove.apply($collection, [$item, $collection]);
        $collection.trigger('ite-remove.collection', [$item]);
      }

      if (0 !== $btn.parents('.collection-item').length) {
        var $item = $btn.closest('.collection-item');
        var $collection = $(this.collectionSelector);

        var result = this.beforeRemove.apply($collection, [$item, $collection]);
        if (false === result) {
          return;
        }
        var event = $.Event('ite-before-remove.collection');
        $collection.trigger(event, [$item]);
        if (false === event.result) {
          return;
        }

        switch (this.hide.type.toLowerCase()) {
          case 'fade':
            $item.fadeOut(this.hide.length, afterHide);
            break;
          case 'slide':
            $item.slideUp(this.hide.length, afterHide);
            break;
          case 'hide':
            $item.hide(this.hide.length, afterHide);
            break;
          default:
            $item.hide(null, afterHide);
            break;
        }
      }
    },
    items: function() {
      return $(this.collectionItemSelector);
    },
    itemsCount: function() {
      return this.items().length;
    },
    parents: function() {
      return $(this.collectionSelector).parents('[data-collection-id]');
    },
    parentsCount: function() {
      return this.parents().length;
    },
    hasParent: function() {
      return 0 !== this.parentsCount().length;
    },
    itemsWrapper: function() {
      return $(this.collectionItemsSelector);
    }
  };


  /* COLLECTION PLUGIN DEFINITION
   * ======================== */

  $.fn.collection = function(method) {
    var methodArguments = arguments, value;
    this.each(function() {
      var $this = $(this);

      var data = $this.data('collection');
      if (!data) {
        $this.data('collection', (data = new Collection(this)));
      }
      if ($.isFunction(data[method])) {
        value = data[method].apply(data, Array.prototype.slice.call(methodArguments, 1));
      } else {
        $.error('Method with name "' +  method + '" does not exist in jQuery.collection');
      }
    });
    return ('undefined' === typeof value) ? this : value;
  };

  $.fn.collection.defaults = {
    beforeAdd: function(item, collection) {},
    onAdd: function(item, collection) {},
    beforeRemove: function(item, collection) {},
    onRemove: function(item, collection) {},
    show: {
      type: 'show',
      length: 0
    },
    hide: {
      type: 'hide',
      length: 0
    }
  };
  $.fn.collection.collections = {};

  $.fn.collection.Constructor = Collection;


  /* COLLECTION DATA-API
   * =============== */

  $(function () {
    // add
    $('body').on('click.collection', '[data-collection-add-btn]', function (e) {
      var $btn = $(e.target);
      $btn.closest('[data-collection-id]').collection('add');
      e.preventDefault();
    });

    // remove
    $('body').on('click.collection', '[data-collection-remove-btn]', function (e) {
      var $btn = $(e.target);
      $btn.closest('[data-collection-id]').collection('remove', $btn);
      e.preventDefault();
    });
  });

}(window.jQuery);