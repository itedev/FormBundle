/**
 * Created by c1tru55 on 11.06.15.
 */
(function($) {
  var Hierarchical = function(form, options) {
    this.$form = $(form);
    this.options = $.extend(true, {}, $.fn.hierarchical.defaults, options);

    this.initialize();
  };

  Hierarchical.prototype = {
    constructor: Hierarchical,

    initialize: function() {},

    trigger: function($elements, force) {
      var self = this;

      if (!$.isArray($elements)) {
        $elements = [$elements];
      }
      force = force || false;

      var originators = [];
      var originatorDatas = [];
      var submit = false;
      $.each($elements, function(i, $element) {
        var view = SF.forms.find($element.attr('id'));

        var originator = view.getOption('full_name');
        var originatorValue = view.getValue($element);

        var eventData = {
          originator: originator,
          children: {}
        };
        var childrenDatas = {};
        var childrenCount = 0;
        var hierarchicalChildren = view.getOption('hierarchical_children', []);
        $.each(hierarchicalChildren, function(i, hierarchicalChild) {
          var childView = SF.forms.find(hierarchicalChild);
          var $childElement = childView.getElement();
          childrenDatas[hierarchicalChild] = {
            view: childView,
            $element: $childElement
          };

          if ($childElement.length) {
            eventData.children[hierarchicalChild] = $childElement.get(0);
            childrenCount++;
          }
        });

        var originatorData = {
          view: view,
          $element: $element,
          originator: originator,
          originatorValue: originatorValue,
          eventData: eventData,
          childrenDatas: childrenDatas
        };
        originatorDatas.push(originatorData);
        originators.push(originator);

        if (!childrenCount) {
          submit = false;
          return;
        }

        var event = $.Event('ite-before-submit.hierarchical', eventData);
        $element.trigger(event);
        if (false !== event.result) {
          submit = true;
        }
      });

      if (!submit && !force) {
        return;
      }

      var jqxhr = this.$form.data('hierarchicalJqxhr');
      if (jqxhr) {
        jqxhr.abort('hierarchicalAbort');
      }
      jqxhr = $.ajax({
        type: this.$form.attr('method'),
        url: this.$form.attr('action'),
        data: this.$form.serialize(),
        dataType: 'html',
        headers: {
          'X-SF-Hierarchical': '1',
          'X-SF-Hierarchical-Originator': originators.join(',')
        },
        success: function(response) {
          self.$form.removeData('hierarchicalJqxhr');

          var $newContext = $(response);

          $.each(originatorDatas, function(i, originatorData) {
            var event = $.Event('ite-after-submit.hierarchical', originatorData.eventData);
            originatorData.$element.trigger(event, [$newContext]);
            if (false === event.result) {
              return;
            }

            $.each(originatorData.childrenDatas, function(i, childData) {
              var childView = childData.view;
              var $childElement = childData.$element;
              var $newChildElement = childView.getElement($newContext);

              if (!$childElement.length) {
                return;
              }

              // set element value
              var childEventData = {
                originator: originatorData.originator,
                originatorValue: originatorData.originatorValue,
                relatedTarget: $newChildElement.get(0)
              };
              event = $.Event('ite-before-change.hierarchical', childEventData);
              $childElement.trigger(event, [$newContext]);
              if (false === event.result) {
                return;
              }

              childView.setValue($childElement, $newChildElement);
              if (childView.getOption('hierarchical_trigger_event', false)) {
                $childElement.trigger(childView.getOption('hierarchical_trigger_event'));
              }

              event = $.Event('ite-after-change.hierarchical', childEventData);
              $childElement.trigger(event, [$newContext]);
            });

            event = $.Event('ite-after-children-change.hierarchical', originatorData.eventData);
            originatorData.$element.trigger(event, [$newContext]);
          });
        }
      });
      jqxhr.fail(function() {
        if (0 !== jqxhr.readyState || 'hierarchicalAbort' !== jqxhr.statusText) {
          self.$form.removeData('hierarchicalJqxhr');
        }

        $.each(originatorDatas, function(i, originatorData) {
          var event = $.Event('ite-after-submit.hierarchical', originatorData.eventData);
          originatorData.$element.trigger(event);
        });
      });
      this.$form.data('hierarchicalJqxhr', jqxhr);
    },

    active: function() {
      return 'undefined' !== typeof this.$form.data('hierarchicalJqxhr');
    }
  };

  $.fn.hierarchical = function(option) {
    var methodArguments = arguments, value;
    this.each(function() {
      var $this = $(this);

      var data = $this.data('hierarchical');
      if (!data) {
        var options = typeof option == 'object' ? option : {};
        $this.data('hierarchical', (data = new Hierarchical(this, options)));
      }
      if ('string' === $.type(option)) {
        if ($.isFunction(data[option])) {
          value = data[option].apply(data, Array.prototype.slice.call(methodArguments, 1));
        } else {
          $.error('Method with name "' +  option + '" does not exist in jQuery.hierarchical');
        }
      }

      return ('undefined' === typeof value) ? this : value;
    });
  };

  $.fn.hierarchical.defaults = {};

})(jQuery);