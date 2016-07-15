import _ from 'underscore';
import Marionette from 'backbone.marionette';
import attributeTemplate from 'templates/brander-eav/Widgets/attribute.item.twig';


export default Marionette.ItemView.extend({
//        "tagName":           'li',
  'changeStateEvents':   [
    // 'processing',
    // 'error',
    // 'saved',
  ], // or: {"eventName": "css-class-name"}
  'cssClassStatePrefix': 'item-',

  'initialize'() {
    this.template = attributeTemplate;
    _.each(this.changeStateEvents, function (value, index) {
      var eventName;
      if (_.isArray(this.changeStateEvents)) {
        eventName = value;
      } else {
        eventName = index;
      }
      this.on(eventName, this.getChangeStateHandler(eventName), this);
    }, this);
  },

  'getChangeStateHandler'(eventName) {
    return function () {
      this.state = eventName;
      var cssClass;
      if (_.isArray(this.changeStateEvents)) {
        cssClass = this.cssClassStatePrefix + eventName;
      } else {
        cssClass = this.cssClassStatePrefix + this.changeStateEvents[eventName];
      }
      this.$el.addClass(cssClass);
    };
  },

  'ui': {
    'edit':   '.edit',
    'remove': '.remove',
  },

  'triggers': {
    'click @ui.edit':   'select',
    'click @ui.remove': 'remove',
  },
});
