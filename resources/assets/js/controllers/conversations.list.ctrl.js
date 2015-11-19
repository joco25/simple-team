'use strict';
module.exports = [
  '$stateParams', '$www', function($stateParams, $www) {
    this.topics = [];
    this.filters = {
      type: $stateParams.type || 'latest',
      busy: false,
      page: 1,
      disableInfiniteScroll: false
    };
    this.loadConversations = (function(_this) {
      return function() {
        if (_this.filters.busy) {
          return;
        }
        _this.filters.busy = true;
        return $www.get('/api/topics/' + _this.filters.type, {
          take: 50,
          page: _this.filters.page
        }).success(function(data) {
          _this.topics = _this.topics.concat(data.topics);
          _this.filters.busy = false;
          return _this.filters.disableInfiniteScroll = data.topics.length === 0 ? true : false;
        });
      };
    })(this);
    this.nextPage = (function(_this) {
      return function() {
        if (_this.filters.busy) {
          return;
        }
        _this.filters.page += 1;
        return _this.loadConversations();
      };
    })(this);
    this.toggleTopicUserStar = (function(_this) {
      return function(topic) {
        if (topic.is_starred) {
          _this.unstarTopic(topic.id);
        } else {
          _this.starTopic(topic.id);
        }
        return topic.is_starred = !topic.is_starred;
      };
    })(this);
    this.starTopic = function(topicId) {
      return $www.post('/api/topicStars', {
        topic_id: topicId
      });
    };
    this.unstarTopic = function(topicId) {
      return $www["delete"]('/api/topicStars', {
        topic_id: topicId
      });
    };
    this.loadConversations();
  }
];

// ---
// generated by coffee-script 1.9.2