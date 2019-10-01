/**
 * jquery.nijc.suggest.js
 * Version: 2011-06-30
 * Licensed under the MIT license
 */

(function($) {

if (typeof(jQuery.NIJC) == 'undefined')
  jQuery.NIJC = function() {};

jQuery.NIJC.Suggest = function(text, config) {
  this.initialize(text, config);
};

jQuery.NIJC.Suggest.prototype = {
  key: {
     BACKSPACE:  8,
     TAB:        9,
     ENTER:     13,
     ESC:       27,
     LEFT:      37,
     UP:        38,
     DOWN:      40,
  },
  config: null,

  /**
   * initialize object
   * @access private
   */
  initialize: function(input, config) {
    this.config = $.extend({
      list: [],
      url: null,
      method: 'GET',
      params: 'q=',
      timeout: 500,
      size: 5,
    }, config);
    this.initializeTextInput(input);
    this.initializeSuggestArea();
  },

  /******************* search functions *******************/
  searchCacheKeyword: null,
  searchCacheResults: null,

  /**
   * search suggest items
   * @param string keyword search keyword
   */
  search: function(keyword) {
    var myself = this;
    var keyword = $.trim(keyword);
    if (this.searchCacheKeyword == keyword) {
      // hit cache
      this.updateSuggestArea(this.searchCacheResults);
    } else {
      // do search!
      if (this.config.list.length != 0)
        this.searchFromList(keyword);
      else if (this.config.url != null)
        this.searchFromUrl(keyword);
    }
  },

  /**
   * is cached search keyword?
   * @param string keyword
   * @return bool false if not
   */
  isCachedSearchKeyword: function(keyword) {
    var keyword = $.trim(keyword);
    return (this.searchCacheKeyword == keyword);
  },

  /**
   * search suggest items from predefined list
   * @param string keyword search keyword
   */
  searchFromList: function(keyword) {
    var myself = this;
    var results = [];
    var re = new RegExp('^' + keyword, 'i');
    $.each(this.config.list, function() {
      if (this.match(re))
        results.push(this);
    });
    this.updateSuggestArea(results);
    this.searchCacheKeyword = keyword;
    this.searchCacheResults = results;
  },

  /**
   * search suggest items from predefined list
   * @param string keyword search keyword
   */
  searchFromUrl: function(keyword) {
    var myself = this;
    var results = [];
    $.ajax({
      url: this.config.url,
      type: this.config.method,
      data: this.config.params + encodeURIComponent(keyword),
      timeout: 3000,
      success: function(response) {
        results = response.split("\n");
        results = $.grep(results, function(v, k) {
          return ($.trim(v).length != 0);
        });
        myself.updateSuggestArea(results);
        myself.searchCacheKeyword = keyword;
        myself.searchCacheResults = results;
      }
    });
  },

  /****************** timer event **************************/
  timerId: null,

  /**
   * set timer
   * @param function func callback function
   */
  setTimer: function(func) {
    var myself = this;
    if (this.timerId != null) {
      clearTimeout(this.timerId);
      this.timerId = null;
    }
    this.timerId = setTimeout(function() {
      func.call(myself);
      clearTimeout(myself.timerId);
      myself.timerId = null;
    }, this.config.timeout);
  },

  /******************* text input **************************/
  textInput: null,

  /**
   * initialize text input
   * @param object input text input jQuery object
   */
  initializeTextInput: function(input) {
    var myself = this;
    input.attr('autocomplete', 'off');
    input.keyup(function(e) {
      switch (e.keyCode) {
      case myself.key.DOWN:
        if (myself.isShowSuggestArea()) {
          myself.focusSuggestArea();
        } else {
          myself.search(input.val());
        }
        break;
      default:
        if (!myself.isCachedSearchKeyword(input.val())) {
          myself.setTimer(function() {
            myself.search(input.val());
          });
        }
      }
      return true;
    });
    input.dblclick(function() {
      if (input.val() == '')
        myself.search(input.val());
      return true;
    });
    input.focus(function() {
      myself.setTimer(function() {
        myself.search(input.val());
      });
      return true;
    });
    input.blur(function() {
      myself.setTimer(function() {
        if (!myself.isFocusSuggestArea)
          myself.hideSuggestArea();
      });
      return true;
    });
    this.textInput = input;
  },

  /**
   * focus text input
   */
  focusTextInput: function() {
    this.textInput.focus();
  },

  /**
   * set text input
   * @param string keyword
   */
  setTextInput: function(keyword) {
    this.textInput.val(keyword);
  },

  /**
   * get text input position
   * @return array text input position
   */
  getPositionTextInput: function() {
    var position = this.textInput.position();
    var ret = {
      top: position.top,
      left: position.left,
      height: this.textInput.outerHeight(),
      width: this.textInput.outerWidth(),
    };
    return ret;
  },
  
  /******************* suggest area ************************/
  suggestArea: null,
  isFocusSuggestArea: false,

  /**
   * initialize suggest area
   */
  initializeSuggestArea: function() {
    var myself = this;
    var suggest = $('<select size="' + this.config.size + '"></select>');
    suggest.hide();
    suggest.keydown(function(e) {
      switch (e.keyCode) {
      case myself.key.TAB:
        // disable to move current focus to next element
        return false;
      case myself.key.BACKSPACE:
        // disable to move page on press backspace key
        return false;
      case myself.key.LEFT:
      case myself.key.UP:
        if (suggest.val() == 0)
          myself.focusTextInput();
        break;
      default:
      }
      return true;
    });
    suggest.keyup(function(e) {
      switch (e.keyCode) {
      case myself.key.TAB:
      case myself.key.ENTER:
        myself.setTextInput(myself.getSuggestValue());
        myself.focusTextInput();
        break;
      case myself.key.BACKSPACE:
      case myself.key.ESC:
        myself.focusTextInput();
        break;
      default:
      }
      return true;
    });
    suggest.click(function() {
      myself.setTextInput(myself.getSuggestValue());
      myself.focusTextInput();
      return true;
    });
    suggest.focus(function() {
      myself.isFocusSuggestArea = true;
      return true; 
    });
    suggest.blur(function() {
      myself.isFocusSuggestArea = false;
      myself.suggestArea.val('');
      myself.suggestArea.get(0).selectedIndex = -1;
      return true; 
    });
    $('body').append(suggest);
    myself.suggestArea = suggest;
  },

  /**
   * show suggest area
   * @param string keyword search keyword
   */
  showSuggestArea: function() {
    var myself = this;
    var position = this.getPositionTextInput();
    this.suggestArea.css({
      'z-index': 102,
      'position': 'absolute',
      'width': position.width,
      'top': position.top + position.height,
      'left': position.left,
    });
    this.suggestArea.slideDown('fast');
  },

  /**
   * hide suggest area
   */
  hideSuggestArea: function() {
    this.suggestArea.slideUp('fast');
  },

  /**
   * is show suggest area?
   */
  isShowSuggestArea: function() {
    return this.suggestArea.is(':visible');
  },

  /**
   * update suggest area
   * @param string keyword search keyword
   */
  updateSuggestArea: function(list) {
    var myself = this;
    this.suggestArea.empty();
    if (list.length == 0) {
      this.hideSuggestArea();
    } else {
      $.each(list, function() {
        myself.appendSuggestItem(this);
      });
      myself.showSuggestArea();
    }
  },

  /**
   * focus suggest area
   */
  focusSuggestArea: function() {
    this.suggestArea.focus();
    if (this.suggestArea.children().length != 0)
      this.suggestArea.val(0);
  },

  /**
   * append suggest item
   * @param string keyword list item
   */
  appendSuggestItem: function(keyword) {
    var idx = this.suggestArea.children().length;
    var option = $('<option>' + keyword + '</option>');
    option.val(idx);
    this.suggestArea.append(option);
  },

  /**
   * get current suggest value
   */
  getSuggestValue: function() {
    return this.suggestArea.find(':selected').text();
  },
};

jQuery.fn.NijcSuggest = function(config) {
  new jQuery.NIJC.Suggest(this, config);
}

})(jQuery);
