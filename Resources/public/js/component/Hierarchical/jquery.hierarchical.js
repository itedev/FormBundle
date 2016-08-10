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

      // @todo: what if radio/checkbox?
      if (!$.isArray($elements)) {
        $elements = [$elements];
      }
      force = force || false;

      var originatorInfoList = [];
      $.each($elements, function(i, $element) {
        var view = $element.formView();
        var fullName = view.getFullName();

        var childrenInfoMap = {};
        var eventData = {
          originator: fullName,
          children: {},
          force: force
        };
        var hierarchicalChildren = view.getOption('hierarchical_children', []);
        $.each(hierarchicalChildren, function(i, childId) {
          var childView = SF.forms.find(childId);
          if (null === childView) {
            return;
          }

          var $childElement = childView.getElement();
          if (0 === $childElement.length) {
            return;
          }

          var childInfo = {
            view: childView,
            $element: $childElement
          };
          eventData.children[childId] = {
            submit: true,
            element: $childElement.get(0)
          };
          childrenInfoMap[childId] = childInfo;
        });

        var submit = true;
        if (0 === SF.util.objectLength(childrenInfoMap)) {
          // if there are no any child DOM element - don't submit the form
          submit = false;
        }
        if (submit) {
          var parentEvent = $.Event('before-parent-submit.ite.hierarchical', eventData);
          $element.trigger(parentEvent);
          if (false === parentEvent.result) {
            // if any listener return false - don't submit the form
            submit = false;
          }
        }
        if (submit) {
          // if all child elements set corresponding submit flag to false - don't submit the form
          submit = false;
          $.each(parentEvent.children, function(childId, child) {
            if (true === child['submit']) {
              submit = true;

              return false; // break
            }
          });
        }
        if (submit) {
          // if all child elements listeners return false - don't submit the form
          submit = false;
          var childEventData = {
            originator: fullName
          };
          $.each(childrenInfoMap, function(childId, childInfo) {
            var $childElement = childInfo.$element;

            var childEvent = $.Event('before-child-submit.ite.hierarchical', childEventData);
            $childElement.trigger(childEvent);
            if (false !== childEvent.result) {
              submit = true;
            }
          });
        }

        var originatorValue = view.getValue($element);
        var originatorInfo = {
          view: view,
          $element: $element,
          fullName: fullName,
          originatorValue: originatorValue,
          eventData: eventData,
          submit: submit,
          childrenInfoMap: childrenInfoMap
        };
        originatorInfoList.push(originatorInfo);
      });

      var submit = false;
      var originators = [];
      $.each(originatorInfoList, function(i, originatorInfo) {
        if (true === originatorInfo.submit || force) {
          submit = true;
          originators.push(originatorInfo.fullName);
        }
      });

      if (!submit) {
        return;
      }

      this.$form.trigger('form-submit.ite.hierarchical');

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
          var $newContext = $(response);

          $.each(originatorInfoList, function(i, originatorInfo) {
            var event = $.Event('before-children-change.ite.hierarchical', originatorInfo.eventData);
            originatorInfo.$element.trigger(event, [$newContext]);
            if (false === event.result) {
              return;
            }

            $.each(originatorInfo.childrenInfoMap, function(i, childInfo) {
              var childView = childInfo.view;
              var $childElement = childInfo.$element;
              var $newChildElement = childView.getElement($newContext);

              if (!$childElement.length) {
                return;
              }

              if (!childView.getOption('hierarchical_changed', true)) {
                return;
              }

              // set element value
              var childEventData = {
                originator: originatorInfo.fullName,
                originatorValue: originatorInfo.originatorValue,
                relatedTarget: $newChildElement.get(0)
              };
              event = $.Event('before-change.ite.hierarchical', childEventData);
              $childElement.trigger(event, [$newContext]);
              if (false === event.result) {
                return;
              }

              childView.setValue($childElement, $newChildElement);
              if (childView.hasOption('hierarchical_trigger_event')) {
                childView.triggerEvent($childElement, childView.getOption('hierarchical_trigger_event'));
              }

              event = $.Event('after-change.ite.hierarchical', childEventData);
              $childElement.trigger(event, [$newContext]);
            });

            event = $.Event('after-children-change.ite.hierarchical', originatorInfo.eventData);
            originatorInfo.$element.trigger(event, [$newContext]);
          });
        }
      });
      jqxhr.always(function(xhr, reason) {
        if (reason !== 'hierarchicalAbort') {
          self.$form.removeData('hierarchicalJqxhr');
          self.$form.trigger('after-submit.ite.hierarchical');
        }
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