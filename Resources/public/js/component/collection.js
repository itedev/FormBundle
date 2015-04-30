!function ($) {

  "use strict";

  /* Collection PUBLIC CLASS DEFINITION
   * ============================== */

  var Collection = function(collection) {
    var $collection = $(collection);

    this.collectionSelector = '#' + $collection.attr('id');
    this.collectionId = $collection.data('collectionId');
    this.collectionItemsSelector = this.collectionSelector + ' .collection-items:first';
    this.collectionItemSelector = this.collectionItemsSelector + ' > .collection-item';

    $(this.collectionItemSelector).each(function(index) {
      $(this).attr('data-index', index);
    });

    this.show = $collection.data('show-animation');
    this.hide = $collection.data('hide-animation');
    this.initialize();
  };

  Collection.prototype = {
    constructor: Collection,
    initialize: function() {
      this.index = $(this.collectionItemSelector).length - 1;
    },
    add: function () {
      var self = this;
      function afterShow() {
        // apply plugins
        var replacementTokens = {};
        $item.parents('.collection-item').each(function() {
          var parentCollectionItem = $(this);

          var parentPrototypeName = parentCollectionItem.closest('[data-collection-id]').data('prototypeName');
          replacementTokens[parentPrototypeName] = parentCollectionItem.data('index');
        });
        replacementTokens[prototypeName] = self.index;

        $collection.trigger('ite-add.collection', [$item]);

        SF.elements.apply($item, replacementTokens);
      }

      this.index++;

      var $collection = $(this.collectionSelector);
      var prototypeName = $collection.data('prototypeName');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var re = new RegExp(prototypeName, 'g');
      var itemHtml = $collection.data('prototype').replace(re, this.index);
      var $item = $(itemHtml).attr('data-index', this.index);

      var event = $.Event('ite-before-add.collection');
      $collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
      }

      $item.hide();
      $(this.collectionItemsSelector).append($item);

      var showLength = this.show.length;
      switch (this.show.type.toLowerCase()) {
        case 'fade':
          $item.fadeIn(showLength, afterShow);
          break;
        case 'slide':
          $item.slideDown(showLength, afterShow);
          break;
        case 'show':
          $item.show(showLength, afterShow);
          break;
        default:
          $item.show(null, afterShow);
          break;
      }
    },
    remove: function ($item) {
      function afterHide() {
        $item.remove();

        $collection.trigger('ite-remove.collection', [$item]);
      }

      var $collection = $(this.collectionSelector);

      var event = $.Event('ite-before-remove.collection');
      $collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
      }

      var hideLength = this.hide.length;
      switch (this.hide.type.toLowerCase()) {
        case 'fade':
          $item.fadeOut(hideLength, afterHide);
          break;
        case 'slide':
          $item.slideUp(hideLength, afterHide);
          break;
        case 'hide':
          $item.hide(hideLength, afterHide);
          break;
        default:
          $item.hide(null, afterHide);
          break;
      }
    },
    itemsWrapper: function() {
      return $(this.collectionItemsSelector);
    },
    items: function() {
      return $(this.collectionItemSelector);
    },
    isEmpty: function() {
      return 0 === this.itemsCount();
    },
    clear: function() {
      this.itemsWrapper().empty();
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
      return 0 !== this.parentsCount();
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

  $.fn.collection.Constructor = Collection;


  /* COLLECTION DATA-API
   * =============== */

  $(function () {
    // add
    $('body').on('click.collection', '[data-collection-add-btn]', function (e) {
      var $btn = $(this);
      var $collection = $($btn.data('collectionAddBtn'));
      if (!$collection.length) {
        return;
      }

      $collection.collection('add');
      e.preventDefault();
    });

    // remove
    $('body').on('click.collection', '[data-collection-remove-btn]', function (e) {
      var $btn = $(this);
      var $collection = $($btn.data('collectionRemoveBtn'));
      var $item = $btn.closest('.collection-item');

      if (!$collection.length || !$item.length) {
        return;
      }

      $collection.collection('remove', $item);
      e.preventDefault();
    });
  });

}(window.jQuery);