(function($) {

  var Collection = function(element) {
    this.$collection = $(element);
    this.itemsWrapperSelector = '.collection-items:first';
    this.$itemsWrapper = this.$collection.find(this.itemsWrapperSelector);
    this.itemSelector = ' > .collection-item';

    this.showAnimation = this.$collection.data('show-animation');
    this.hideAnimation = this.$collection.data('hide-animation');
    this.initialize();
  };

  Collection.prototype = {
    constructor: Collection,
    initialize: function() {
      this.index = this.$itemsWrapper.find(this.collectionItemSelector).length - 1;
    },
    add: function(afterShowCallback) {
      var prototype = this.$collection.data('prototype');
      var prototypeName = this.$collection.data('prototypeName');
      if ('undefined' === typeof prototypeName) {
        prototypeName = '__name__';
      }

      var re = new RegExp(prototypeName, 'g');

      this.index++;
      var itemHtml = prototype.replace(re, this.index);
      var $item = $(itemHtml);

      var event = $.Event('ite-before-add.collection');
      this.$collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
      }

      $item.hide();
      this.$itemsWrapper.append($item);

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
      $item[showMethod](showLength, function() {
        if ($.isFunction(afterShowCallback)) {
          afterShowCallback.apply(self.$collection, [$item]);
        }

        self.$collection.trigger('ite-add.collection', [$item]);

        var view = SF.forms.find(self.$collection.attr('id'));
        if (null !== view) {
          view.addCollectionItem(self.index);
        }
      });
    },
    remove: function($item) {
      var event = $.Event('ite-before-remove.collection');
      this.$collection.trigger(event, [$item]);
      if (false === event.result) {
        return;
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
      $item[hideMethod](hideLength, function() {
        $item.remove();

        self.$collection.trigger('ite-remove.collection', [$item]);
      });
    },
    itemsWrapper: function() {
      return this.$itemsWrapper;
    },
    items: function() {
      return this.$itemsWrapper.find(this.itemSelector);
    },
    isEmpty: function() {
      return 0 === this.count();
    },
    clear: function() {
      this.$itemsWrapper.empty();
    },
    count: function() {
      return this.items().length;
    },
    parents: function() {
      return this.$collection.parents('[data-collection-id]');
    },
    parentsCount: function() {
      return this.parents().length;
    },
    hasParent: function() {
      return 0 !== this.parentsCount();
    }
  };

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

  $(function() {
    $('body')
      .on('click.ite.collection', '[data-collection-add-btn]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        var $collection = $($btn.data('collectionAddBtn'));
        if (!$collection.length) {
          return;
        }

        $collection.collection('add');
      })
      .on('click.ite.collection', '[data-collection-remove-btn]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn = $(this);
        var $collection = $($btn.data('collectionRemoveBtn'));
        var $item = $btn.closest('.collection-item');

        if (!$collection.length || !$item.length) {
          return;
        }

        $collection.collection('remove', $item);
      })
    ;
  });

})(jQuery);