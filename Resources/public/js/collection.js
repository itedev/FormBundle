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
        SF.elements.apply(replacementTokens, $item);

        self.onAdd.apply($collection, [$item, self.index, $collection]);
        $collection.triggerHandler('add.ite-collection-item', [$item, self.index, $collection]);
      }

      this.index++;

      var $collection = $(this.collectionSelector);
      var prototypeName = $collection.data('prototype-name');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var splitRe = new RegExp("(data\\-collection\\-id=(?:\"|&quot;)[^\"&]*?(?:\"|&quot;))", 'g');
      var replaceRe = new RegExp(prototypeName, 'g');

      var prototype = $collection.data('prototype');
      var parts = prototype.split(splitRe);
      for (var i in parts) {
        if (-1 === parts[i].indexOf('data-collection-id')) {
          parts[i] = parts[i].replace(replaceRe, this.index);
        }
      }

      var itemHtml = parts.join('');
      var $item = $(itemHtml).attr('data-index', this.index);

      var result = this.beforeAdd.apply($collection, [$item, this.index, $collection]);
      if (false === result) {
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

        self.onRemove.apply($collection, [$item, index, $collection]);
        $collection.triggerHandler('remove.ite-collection-item', [$item, index, $collection]);
      }

      if (0 !== $btn.parents('.collection-item').length) {
        var $item = $btn.closest('.collection-item');
        var index = $item.data('index');
        var $collection = $(this.collectionSelector);

        var result = this.beforeRemove.apply($collection, [$item, index, $collection]);
        if (false === result) {
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
    getItems: function() {
      return $(this.collectionItemSelector);
    },
    getParents: function() {
      return $(this.collectionSelector).parents('[data-collection-id]');
    },
    hasParent: function() {
      return 0 !== $(this.collectionSelector).parents('[data-collection-id]').length;
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
        $this.data('collection', (data = new Collection(this)));
      }
      if ($.isFunction(data[method])) {
        data[method].apply(data, Array.prototype.slice.call(methodArguments, 1));
      } else {
        $.error('Method with name "' +  method + '" does not exist in jQuery.collection');
      }
    });
  };

  $.fn.collection.defaults = {
    beforeAdd: function(item, index, collection) {},
    onAdd: function(item, index, collection) {},
    beforeRemove: function(item, index, collection) {},
    onRemove: function(item, index, collection) {},
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
    $('body').on('click.collection.data-api', '[data-collection-add-btn]', function (e) {
      var $btn = $(e.target);
      $btn.closest('[data-collection-id]').collection('add');
      e.preventDefault();
    });

    // remove
    $('body').on('click.collection.data-api', '[data-collection-remove-btn]', function (e) {
      var $btn = $(e.target);
      $btn.closest('[data-collection-id]').collection('remove', $btn);
      e.preventDefault();
    });
  });

}(window.jQuery);