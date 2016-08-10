(function($) {
  SF.fn.util = $.extend(SF.fn.util, {
    arrayUnique: function(arr) {
      return $.grep(arr, function(v, k) {
        return $.inArray(v, arr) === k;
      });
    }
  });

  SF.fn.callbacks = $.extend(SF.fn.callbacks, {
    hierarchicalChange: function(e) {
      var $element = $(e.delegateTarget);

      var view = $element.formView();
      var $form = view.getForm();

      $form.hierarchical('trigger', [$element]);
    }
  });

  var baseIsInitializable = SF.fn.classes.FormView.prototype.isInitializable;
  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  var baseAddCollectionItem = SF.fn.classes.FormView.prototype.addCollectionItem;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    isInitializable: function() {
      var initializable = baseIsInitializable.call(this);

      return initializable || this.hasOption('hierarchical_children') || this.hasOption('hierarchical_originator');
    },

    initialize: function($element) {
      baseInitialize.call(this, $element);

      var hierarchicalChildren = this.getOption('hierarchical_children', []);
      var isHierarchicalOriginator = this.getOption('hierarchical_originator', false);

      if (!hierarchicalChildren.length && !isHierarchicalOriginator) {
        return;
      }

      if ('undefined' !== typeof $element.data('hierarchical')) {
        return;
      }

      var delegateSelector = this.getOption('delegate_selector', false);
      if (delegateSelector) {
        $element.on('change.ite.hierarchical', delegateSelector, SF.callbacks.hierarchicalChange);
      } else {
        $element.on('change.ite.hierarchical', SF.callbacks.hierarchicalChange);
      }

      $element.data('hierarchical', true);
    },

    addCollectionItem: function(name) {
      var collectionItem = baseAddCollectionItem.call(this);
      var root = this.getRoot();

      var walkViewCallback = function() {
        var id = this.getId();
        var hierarchicalParents = this.getOption('hierarchical_parents', []);
        if (!hierarchicalParents.length) {
          return;
        }

        $.each(hierarchicalParents, function(i, hierarchicalParent) {
          var parentView = root.find(hierarchicalParent);

          var hierarchicalChildren = parentView.getOption('hierarchical_children', []);
          if (-1 === $.inArray(id, hierarchicalChildren)) {
            hierarchicalChildren.push(id);
          }
          parentView.setOption('hierarchical_children', hierarchicalChildren);
        });
      };

      collectionItem.walkRecursive(walkViewCallback);

      return collectionItem;
    }
  });

})(jQuery);