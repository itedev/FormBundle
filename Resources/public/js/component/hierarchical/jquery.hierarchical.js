/**
 * Created by c1tru55 on 11.06.15.
 */
(function($) {
  var Hierarchical = function(form, options) {
    this.$form = $(form);
    $.extend(this.options, $.fn.hierarchical.defaults, options);
  };

  Hierarchical.prototype = {
    constructor: Hierarchical,

    trigger: function(elementDatas, force) {
      var self = this;

      force = force || false;

      var hierarchicalOriginator = [];
      var originatorElements = [];
      var prevented = false;
      $.each(elementDatas, function(i, elementData) {
        var selector = 'string' === typeof elementData ? elementData : elementData.selector;
        var context = 'object' === typeof elementData ? elementData.context : null;
        var replacementTokens = 'object' === typeof elementData ? elementData.replacementTokens : {};

        var element = SF.elements.get(selector);
        var $element = SF.elements.getJQueryElement(selector, context, replacementTokens);

        var originator = SF.util.getFullName($element, element);
        var originatorData = SF.elements.getElementValue($element, element);

        var eventData = {
          originator: originator,
          children: {}
        };
        var $childrenElements = {};
        var childrenCount = 0;
        $.each(element.getHierarchicalChildren(), function (i, childSelector) {
          var $childElement = SF.elements.getJQueryElement(childSelector, context, replacementTokens);
          $childrenElements[childSelector] = $childElement;

          if ($childElement.length) {
            eventData.children['#' + $childElement.attr('id')] = $childElement.get(0);
            childrenCount++;
          }
        });

        var originatorElement = {
          selector: selector,
          context: context,
          replacementTokens: replacementTokens,
          element: element,
          $element: $element,
          originator: originator,
          originatorData: originatorData,
          eventData: eventData,
          $childrenElements: $childrenElements
        };
        originatorElements.push(originatorElement);
        hierarchicalOriginator.push(originator);

        if (!childrenCount) {
          prevented = true;
          return;
        }

        var event = $.Event('ite-before-submit.hierarchical', eventData);
        $element.trigger(event);
        if (false === event.result && !force) {
          prevented = true;
          return;
        }
      });

      if (prevented) {
        return;
      }

      var jqxhr = self.$form.data('hierarchicalJqxhr');
      if (jqxhr) {
        jqxhr.abort('hierarchicalAbort');
      }
      jqxhr = $.ajax({
        type: self.$form.attr('method'),
        url: self.$form.attr('action'),
        data: self.$form.serialize(),
        dataType: 'html',
        headers: {
          'X-SF-Hierarchical': '1',
          'X-SF-Hierarchical-Originator': hierarchicalOriginator.join(',')
        },
        success: function(response) {
          self.$form.removeData('hierarchicalJqxhr');

          var newContext = $(response);

          $.each(originatorElements, function(i, originatorElement) {
            var event = $.Event('ite-after-submit.hierarchical', originatorElement.eventData);
            originatorElement.$element.trigger(event, [newContext]);
            if (false === event.result) {
              return;
            }

            $.each(originatorElement.element.getHierarchicalChildren(), function(i, childSelector) {
              var childElement = SF.elements.get(childSelector);
              var $childElement = originatorElement.$childrenElements[childSelector];
              var $newChildElement = SF.elements.getJQueryElement(childSelector, newContext, originatorElement.replacementTokens);

              if (!$childElement.length) {
                return;
              }

              // set element value
              var childEventData = {
                originator: originatorElement.originator,
                originatorData: originatorElement.originatorData,
                relatedTarget: $newChildElement.get(0)
              };
              event = $.Event('ite-before-change.hierarchical', childEventData);
              $childElement.trigger(event, [newContext]);
              if (false === event.result) {
                return;
              }

              SF.elements.setElementValue($childElement, $newChildElement, childElement);

              event = $.Event('ite-after-change.hierarchical', childEventData);
              $childElement.trigger(event, [newContext]);
            });

            event = $.Event('ite-after-children-change.hierarchical', originatorElement.eventData);
            originatorElement.$element.trigger(event, [newContext]);
          });
        }
      });
      jqxhr.fail(function() {
        if (0 !== jqxhr.readyState || 'hierarchicalAbort' !== jqxhr.statusText) {
          self.$form.removeData('hierarchicalJqxhr');
        }

        $.each(originatorElements, function(i, originatorElement) {
          var event = $.Event('ite-after-submit.hierarchical', originatorElement.eventData);
          originatorElement.$element.trigger(event);
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