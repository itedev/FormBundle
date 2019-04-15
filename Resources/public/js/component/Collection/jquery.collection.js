(function ($) {

  var Collection = function (element) {
    this.$collection = $(element);
    this.itemsWrapperSelector = '.collection-items:first';
    this.itemSelector = this.itemsWrapperSelector + ' > .collection-item';

    this.showAnimation = this.$collection.data('show-animation');
    this.hideAnimation = this.$collection.data('hide-animation');
    this.initialize();
  };

  Collection.prototype = {
    constructor: Collection,

    initialize: function () {
      this.index = this.$collection.find(this.itemSelector).length - 1;
    },

    add: function (addCallback, addCallback2, index) {
      index = 'undefined' !== typeof index ? parseInt(index) : this.index + 1;

      var prototype = this.$collection.data('prototype');
      var prototypeName = this.$collection.data('prototypeName');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var re = new RegExp(prototypeName, 'g');

      this.index = index;
      var itemHtml = prototype.replace(re, this.index);
      var $item = $(itemHtml);

      var event = $.Event('ite-before-add.collection');
      this.$collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
      }

      $item.hide();
      this.$collection.find(this.itemsWrapperSelector).append($item);

      var showLength = this.showAnimation.length;
      var showMethod;
      switch (this.showAnimation.type.toLowerCase()) {
        case 'fade':
          showMethod = 'fadeIn';
          break;
        case 'slide':
          showMethod = 'slideDown';
          break;
        default:
          showMethod = 'show';
          showLength = 0;
      }

      var self = this;
      $item[showMethod](showLength, function () {
        var collectionView = self.$collection.formView();
        if (null !== collectionView) {
          collectionView.addCollectionItem(self.index);
        }

        if ($.isFunction(addCallback)) {
          addCallback.apply(self.$collection, [$item]);
        }

        self.$collection.trigger('ite-add.collection', [$item]);

        if ($.isFunction(addCallback2)) {
          addCallback2.apply(self.$collection, [$item]);
        }
      });
    },

    addItems: function (count, addCallback, addCallback2, startIndex) {
      if ('undefined' === typeof startIndex) {
        startIndex = this.index + 1;
      }
      var indices = $.isArray(startIndex) ? startIndex : Array.apply(0, Array(count)).map(function (element, i) {
        return parseInt(i) + parseInt(startIndex);
      });

      var prototype = this.$collection.data('prototype');
      var prototypeName = this.$collection.data('prototypeName');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var re = new RegExp(prototypeName, 'g');

      var $itemsWrapper = this.itemsWrapper();
      var items = {};
      for (var i = 0; i < indices.length; i++) {
        this.index = indices[i];
        var itemHtml = prototype.replace(re, this.index);
        var $item = $(itemHtml);

        var event = $.Event('ite-before-add.collection');
        this.$collection.trigger(event, [$item]);
        if (false === event.result) {
          continue;
        }

        $itemsWrapper.append($item);
        items[i] = $item;
      }

      var self = this;
      var collectionView = this.$collection.formView();
      for (i = 0; i < indices.length; i++) {
        var index = indices[i];
        var $item = items[i];

        if (null !== collectionView) {
          collectionView.addCollectionItem(index);
        }

        if ($.isFunction(addCallback)) {
          addCallback.apply(self.$collection, [i, $item, index]);
        }

        self.$collection.trigger('ite-add.collection', [$item]);

        if ($.isFunction(addCallback2)) {
          addCallback2.apply(self.$collection, [i, $item, index]);
        }
      };
    },

    remove: function ($item, force) {
      force = 'undefined' !== typeof force ? force : false;
      if (!force) {
        var event = $.Event('ite-before-remove.collection');
        this.$collection.trigger(event, [$item]);
        if (false === event.result) {
          return;
        }
      }

      var hideLength = this.hideAnimation.length;
      var hideMethod;
      switch (this.hideAnimation.type.toLowerCase()) {
        case 'fade':
          hideMethod = 'fadeOut';
          break;
        case 'slide':
          hideMethod = 'slideUp';
          break;
        default:
          hideMethod = 'hide';
          hideLength = 0;
      }

      var self = this;
      $item[hideMethod](hideLength, function () {
        $item.remove();

        self.$collection.trigger('ite-remove.collection', [$item]);

        var collectionView = self.$collection.formView();
        var itemView = $item.formView();
        if (null !== collectionView && null !== itemView) {
          collectionView.removeChild(itemView.getName());
        }
      });
    },

    itemsWrapper: function () {
      return this.$collection.find(this.itemsWrapperSelector);
    },

    items: function () {
      return this.$collection.find(this.itemSelector);
    },

    item: function (index) {
      return this.$collection.find(this.itemSelector).eq(index);
    },

    currentIndex: function () {
      return this.index;
    },

    isEmpty: function () {
      return 0 === this.count();
    },

    clear: function (resetIndex) {
      resetIndex = 'undefined' !== typeof resetIndex ? resetIndex : false;
      
      this.$collection.find(this.itemsWrapperSelector).empty();
      if (resetIndex) {
        this.index = -1;
      }
      var collectionView = this.$collection.formView();
      if (null !== collectionView) {
        collectionView.clearChildren();
      }
      this.$collection.trigger('ite-clear.collection');
    },

    count: function () {
      return this.items().length;
    },

    parents: function () {
      return this.$collection.parents('[data-collection-id]');
    },

    parentsCount: function () {
      return this.parents().length;
    },

    hasParent: function () {
      return 0 !== this.parentsCount();
    }
  };

  $.fn.collection = function (method) {
    var methodArguments = arguments, value;
    this.each(function () {
      var $this = $(this);

      var data = $this.data('collection');
      if (!data) {
        $this.data('collection', (data = new Collection(this)));
      }
      if ('string' === typeof method) {
        if ($.isFunction(data[method])) {
          value = data[method].apply(data, Array.prototype.slice.call(methodArguments, 1));
        } else {
          $.error('Method with name "' +  method + '" does not exist in jQuery.collection');
        }
      }
    });

    return ('undefined' === typeof value) ? this : value;
  };

  $.fn.collection.Constructor = Collection;

  $(function () {
    $('body')
      .on('click.ite.collection', '[data-collection-add-btn]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        var $collection = $($btn.data('collectionAddBtn'));
        if (!$collection.length) {
          return;
        }

        $collection.collection('add');
      })
      .on('click.ite.collection', '[data-collection-remove-btn]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        var $collection = $($btn.data('collectionRemoveBtn'));

        var $item = 'undefined' !== typeof $btn.data('collectionItem')
          ? $($btn.data('collectionItem'))
          : $btn.closest('.collection-item');

        if (!$collection.length || !$item.length) {
          return;
        }

        $collection.collection('remove', $item);
      })
    ;
  });

})(jQuery);