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
      var $element = $(this);

      var view = SF.forms.find($element.attr('id'));
      var $form = view.getForm();

      $form.hierarchical('trigger', [$element]);
    }
  });

  var baseInitialize = SF.fn.classes.FormView.prototype.initialize;
  SF.fn.classes.FormView.prototype = $.extend(SF.fn.classes.FormView.prototype, {
    initialize: function() {
      baseInitialize.call(this);

      var hierarchicalChildren = this.getOption('hierarchical_children', []);
      var isHierarchicalOriginator = this.getOption('hierarchical_originator', false);

      if (!hierarchicalChildren.length && !isHierarchicalOriginator) {
        return;
      }

      var $element = this.getElement();
      if (!$element.length || 'undefined' !== typeof $element.data('hierarchical')) {
        return;
      }

      var delegateSelector = this.getOption('delegate_selector', false);
      if (delegateSelector) {
        $element.on('change.ite.hierarchical', delegateSelector, SF.callbacks.hierarchicalChange);
      } else {
        $element.on('change.ite.hierarchical', SF.callbacks.hierarchicalChange);
      }

      $element.data('hierarchical', true);
    }
  });

})(jQuery);