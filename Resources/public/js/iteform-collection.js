!function ($) {

  "use strict";

  /* Collection PUBLIC CLASS DEFINITION
   * ============================== */

  var Collection = function(collection) {
    var $collection = $(collection);

    this.collectionSelector = '#' + $collection.attr('id');
    this.collectionId = $collection.data('collection-id');
    this.itemSelector = $collection.data('widget-controls') === 'true'
      ? this.collectionSelector + ' > .controls > .collection-item'
      : this.collectionSelector + ' > .collection-item'
    ;
    this.index = $(this.itemSelector).length - 1;

    $.extend(true, this, $.fn.collection.defaults, $.fn.collection.collections[this.collectionId]);
  };

  Collection.prototype = {
    constructor: Collection,
    add: function () {
      this.index++;

      var $collection = $(this.collectionSelector);
      var prototypeName = $collection.data('prototype-name');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var splitRe = new RegExp("(data\\-collection\\-id=(?:\"|&quot;)[^\"&]*?" + prototypeName + "[^\"&]*?(?:\"|&quot;))", 'g');
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

      if ($collection.data('widget-controls') === 'true') {
        $collection.children('.controls:first').append($item);
      } else {
        $collection.append($item);
      }

      // apply plugins
      var replacementTokens = {};
      $item.parents('.collection-item').each(function() {
        var parentCollectionItem = $(this);

        var parentPrototypeName = parentCollectionItem.closest('[data-collection-id]').data('prototype-name');
        replacementTokens[parentPrototypeName] = parentCollectionItem.data('index');
      });
      replacementTokens[prototypeName] = this.index;
      SF.elements.apply(replacementTokens, $item);

      this.onAdd.apply($collection, [$item, this.index, $collection]);
      $collection.triggerHandler('add.ite-collection-item', [$item]);
    },
    remove: function ($btn) {
      if ($btn.parents('.collection-item').length !== 0) {
        var $item = $btn.closest('.collection-item');
        var index = $item.data('index');
        var $collection = $(this.collectionSelector);

        var result = this.beforeRemove.apply($collection, [$item, index, $collection]);
        if (false === result) {
          return;
        }

        $item.remove();

        this.onRemove.apply($collection, [$item, index, $collection]);
        $collection.triggerHandler('remove.ite-collection-item', [$item]);
      }
    },
    getItems: function() {
      return $(this.itemSelector);
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
    onRemove: function(item, index, collection) {}
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