// Generated by CoffeeScript 1.3.3

/*
Lava provides all of the lovely UI stuff
*/


/*
Global functions
*/


(function() {
  var lavaBindMethods, result, y,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  lavaBindMethods = function(methods, namespace) {
    if (namespace == null) {
      namespace = 'general';
    }
    if (methods['ready']) {
      jQuery(document).on("ready.lava." + namespace, methods['ready']);
    }
    if (methods['init']) {
      jQuery(document).on("init.lava." + namespace, methods['init']);
    }
    if (methods['defaultState']) {
      return jQuery(document).on("defaultState.lava." + namespace, methods['defaultState']);
    }
  };

  jQuery(document).ready(function() {
    return jQuery('.lava-cntr').lava();
  });

  /*
  Main methods
  */


  (function($, window, document) {
    var methods;
    methods = {};
    methods.init = function() {
      $(this).trigger("init.lava", this);
      $(this).trigger("defaultState.lava", this);
      return this;
    };
    return $.fn.extend({
      lava: function(method) {
        var args;
        if (methods[method]) {
          args = Array.prototype.slice.call(arguments, 1);
          return methods[method].apply(this, args);
        } else if (typeof method === 'object' || !method) {
          return methods.init.apply(this, arguments);
        } else {
          return debug.error('Lava call failed', method, methods);
        }
      }
    });
  })(jQuery, window, document);

  /*
  Animations
  */


  (function($) {
    var methods;
    methods = {};
    methods.init = function(e, lava) {
      var selector;
      selector = '.js-lava-animation-slide-right';
      return $(lava).each(function() {
        $(this).find(selector).on('active.lava.lavaAnimation', methods.slideRight);
        return $(this).filter(selector).on('active.lava.lavaAnimation', methods.slideRight);
      });
    };
    methods.slideRight = function() {
      return $(this).css({
        'opacity': 0,
        'position': 'relative',
        'left': '-10px'
      }).animate({
        'opacity': 1,
        'left': 0
      }, 200);
    };
    return lavaBindMethods(methods, 'lavaAnimations');
  })(jQuery);

  /*
  HTML5 history
  */


  (function($) {
    var methods;
    methods = {};
    methods.init = function(e, lava) {
      if ($('html').hasClass('history')) {
        return $(lava).each(function() {
          $(this).find('a.js-lava-address').on('click.lava.lavaHistory', methods.click);
          return $(this).filter('a.js-lava-address').on('click.lava.lavaHistory', methods.click);
        });
      }
    };
    methods.click = function(e) {
      var url;
      if (window.History.enabled) {
        e.preventDefault();
        if ($(this).hasClass('lava-disabled') || $(this).parents('.lava-disabled').length > 0) {
          return;
        }
        url = $(this).attr('href');
        return window.History.pushState(null, null, url);
      }
    };
    return lavaBindMethods(methods, 'lavaHistory');
  })(jQuery);

  /*
  Lava navigation
  */


  (function($, window, document) {
    var methods;
    methods = {};
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        $(this).find('.js-lava-nav').on('click.lava.lavaNav', 'li', methods.click);
        return $(this).filter('.js-lava-nav').on('click.lava.lavaNav', 'li', methods.click);
      });
    };
    methods.click = function() {
      var $lavaNav, attr, identifier, targetCntr;
      if ($(this).hasClass('lava-disabled')) {
        return;
      }
      $lavaNav = $(this).parents('.js-lava-nav');
      targetCntr = $lavaNav.attr('data-lava-nav-target-cntr');
      attr = $lavaNav.attr('data-lava-nav-identifier');
      identifier = $(this).attr(attr);
      $lavaNav;

      $lavaNav.find('li').removeClass('active-descendant').addClass('inactive-descendant');
      $(this).removeClass('inactive-descendant').addClass('active-descendant');
      $("#" + targetCntr).find('.js-lava-nav-target.active-descendant').trigger('inactive.lava');
      $("#" + targetCntr).find('.js-lava-nav-target').removeClass('active-descendant').addClass('inactive-descendant');
      return $("#" + targetCntr).find(".js-lava-nav-target[" + attr + "='" + identifier + "']").removeClass('inactive-descendant').addClass('active-descendant').trigger('active.lava');
    };
    return lavaBindMethods(methods, 'lavaNav');
  })(jQuery, window, document);

  /*
  Scene callbacks
  */


  (function($, window, document) {
    var methods;
    methods = {};
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        var $scenes;
        $scenes = $(this).find('.lava-scene');
        $scenes.on('load.lava.lavaScene', methods.load);
        $scenes.on('active.lava.lavaScene', methods.active);
        $scenes = $(this).filter('.lava-scene');
        $scenes.on('load.lava.lavaScene', methods.load);
        return $scenes.on('active.lava.lavaScene', methods.active);
      });
    };
    methods.defaultState = function(e, lava) {
      return $(lava).each(function() {
        var $scenes;
        $scenes = $(this).find('.lava-scene');
        $scenes.trigger('load.lava');
        return $(this).find('.lava-scene.active-descendant').trigger('active.lava');
      });
    };
    methods.load = function() {
      var $actionBlock, sceneId;
      sceneId = $(this).attr('data-scene-id');
      $actionBlock = $(".lava-actionbar-block[data-scene-id='" + sceneId + "']");
      return $(this).data('lava.scene.actionBlock', $actionBlock);
    };
    methods.active = function() {
      var $actionBar, $actionBlock;
      $actionBar = $(".lava-actionbar-cntr");
      $actionBar.find('.lava-actionbar-block.active-descendant').addClass('inactive-descendant').removeClass('active-descendant').trigger('inactive.lava');
      $actionBlock = $(this).data('lava.scene.actionBlock');
      return $actionBlock.addClass('active-descendant').removeClass('inactive-descendant').trigger('active.lava');
    };
    return lavaBindMethods(methods, 'lavaScene');
  })(jQuery, window, document);

  /*
  Height adjust callbacks
  */


  (function($, window, document) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaHeightAdjust';
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        var $blocks;
        $blocks = $(this).find('.lava-scene.js-height-adjust');
        $blocks.on("load.lava." + namespace, methods.load);
        $blocks = $(this).filter('.lava-scene.js-height-adjust');
        $blocks.on("load.lava." + namespace, methods.load);
        return $(window).on("resize.lava." + namespace, methods.resizeWindow);
      });
    };
    methods.load = function(e) {
      $(this).attr('data-height-adjust-min', $(this).height());
      return setTimeout(methods.resizeWindow, 100);
    };
    methods.resizeWindow = function(e) {
      return $('.lava-scene.js-height-adjust').each(methods.resize);
    };
    methods.resize = function() {
      var $this, doc_height, loop_count, min_height, old_doc_height, win_height, _results;
      $this = $(this);
      doc_height = $(document).height();
      win_height = $(window).height();
      min_height = $this.attr('data-height-adjust-min');
      if (doc_height === win_height) {
        loop_count = 0;
        while ((doc_height === win_height) && (loop_count < 1000)) {
          loop_count += 1;
          $this.height($this.height() + 1);
          doc_height = $(document).height();
          win_height = $(window).height();
        }
        $this.height($this.height() - 1);
      }
      old_doc_height = doc_height = $(document).height();
      if ((doc_height > win_height) && ($this.height() > min_height)) {
        $this.height($this.height() - 1);
        doc_height = $(document).height();
        _results = [];
        while ((doc_height > win_height) && (old_doc_height > doc_height) && ($this.height() > min_height)) {
          old_doc_height = doc_height;
          $this.height($this.height() - 1);
          _results.push(doc_height = $(document).height());
        }
        return _results;
      }
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, window, document);

  /*
  No actionbar callbacks
  */


  (function($, window, document) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaNoActionbarScene';
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        var $blocks;
        $blocks = $(this).find('.lava-scene.lava-scene-no-actionbar');
        $blocks.on("active.lava." + namespace, methods.active);
        $blocks.on("inactive.lava." + namespace, methods.inactive);
        $blocks = $(this).filter('.lava-scene.lava-scene-no-actionbar');
        $blocks.on("active.lava." + namespace, methods.active);
        return $blocks.on("inactive.lava." + namespace, methods.inactive);
      });
    };
    methods.active = function(e) {
      return $('#lava-theatre').addClass('no-actionbar');
    };
    methods.inactive = function(e) {
      return $('#lava-theatre').removeClass('no-actionbar');
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, window, document);

  /*
  Settings Scene callbacks
  */


  (function($, window, document) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaSettingsScene';
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        var $scenes;
        return $scenes = $(this).find('.lava-scene.lava-settings-scene');
      });
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, window, document);

  y = Object;

  y.x = true;

  result = false;

  if (__indexOf.call(y, 'x') >= 0) {
    result = true;
  }

  /*
  Form Attribute
  */


  (function($, Modernizr) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaPolyfill.lavaFormattrribute';
    Modernizr.addTest('formattribute', function() {
      var bool, div, form, id, input;
      try {
        form = document.createElement("form");
        input = document.createElement("input");
        div = document.createElement("div");
        id = "formtest";
        bool = false;
        form.id = id;
        input.setAttribute("form", id);
        div.appendChild(form);
        div.appendChild(input);
        document.documentElement.appendChild(div);
        bool = form.elements.length === 1;
        div.parentNode.removeChild(div);
        return bool;
      } catch (e) {
        return false;
      }
    });
    methods.init = function(e, lava) {
      if (!Modernizr.formattribute) {
        return $(lava).each(function() {
          $(this).find('*[type="submit"][form]').on("click.lava." + namespace, methods.submitClick);
          return $(this).find('form[id]').on("submit.lava." + namespace, methods.formSubmit);
        });
      }
    };
    methods.submitClick = function(e) {
      var $form_, id;
      e.preventDefault();
      id = $(this).attr('form');
      $form_ = $("#" + id);
      $(this).attr('data-lava-formattribute', 'yes');
      $form_.submit();
      return $(this).removeAttr('data-lava-formattribute');
    };
    methods.formSubmit = function(e) {
      var $form, id;
      e.preventDefault();
      id = $(this).attr('id');
      $form = $(this).clone().removeAttr('id');
      $("*[form='" + id + "']").each(function() {
        var $clone;
        $clone = $(this).clone().removeAttr('form');
        if ($(this).is(':checked')) {
          $clone.attr('checked', 'checked');
        } else {
          $clone.removeAttr('checked');
        }
        return $clone.appendTo($form);
      });
      $form.find('*[data-lava-formattribute="yes"]').each(function() {
        var name, value;
        value = $(this).val();
        if (value === void 0) {
          value = '';
        }
        name = $(this).attr('name');
        if (typeof name !== 'undefined') {
          return $("<input type='hidden' name='" + name + "' value='" + value + "'>").appendTo($form);
        }
      });
      $form.appendTo(document.documentElement);
      $form.submit();
      return $form.remove();
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, Modernizr);

  (function($, Modernizr) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaPolyfill.lavaImgLabelFix';
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        return $(this).find('label img.js-lava-fix-click').on("click.lava." + namespace, methods.click);
      });
    };
    methods.click = function(e) {
      e.preventDefault();
      return $(this).parents('label').click();
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, Modernizr);

  /*
  Skins page callbacks
  */


  /*
  Only the active skin gets loaded so if a user changes the skin they would have to save before they could configure it.
  
  This aims to improve this experience by loading the settings via ajax when the skin is changed
  */


  (function($, window, document) {
    var cache, methods, namespace;
    methods = {};
    namespace = 'lavaSkinsPage';
    cache = {};
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        $(this).find('.lava-scene[data-scene-id="choose_skin"] .lava-setting-skin-radio').on("change.lava." + namespace, methods.change);
        return $(this).filter('.lava-scene[data-scene-id="choose_skin"] .lava-setting-skin-radio').on("change.lava." + namespace, methods.change);
      });
    };
    methods.change = function(e) {
      /*
      		Get now checked skin id
      		check whether skin_id is in cache
      */

      var cache_element, current_skin, data, skin;
      skin = $(this).val();
      current_skin = $('.lava-scene[data-scene-id="configure_skin"]').attr('data-skin-id');
      cache_element = {
        'scene': $('.lava-scene[data-scene-id="configure_skin"]').clone(),
        'actions': $('.lava-actionbar-block[data-scene-id="configure_skin"] *').clone(),
        'hidden': $('.lava-programme li[data-scene-id="configure_skin"]').hasClass('hidden-descendant')
      };
      cache[current_skin] = cache_element;
      $('.lava-programme li[data-scene-id="configure_skin"]').addClass('hidden-descendant');
      if (skin in cache) {
        return methods.doReplace(cache[skin]);
      } else {
        data = {
          'action': lavaVars.plugin_namespace + '_get_skin_settings',
          'skin': skin
        };
        return $.getJSON(ajaxurl, data, methods.doReplace);
      }
    };
    methods.doReplace = function(data) {
      if ('scene' in data) {
        $('.lava-scene[data-scene-id="configure_skin"]').remove();
        $('#lava-stage').append(data['scene']);
        $('.lava-scene[data-scene-id="configure_skin"]').lava().trigger('load.lava');
      }
      if ('actions' in data) {
        $('.lava-actionbar-block[data-scene-id="configure_skin"]').html('');
        $('.lava-actionbar-block[data-scene-id="configure_skin"]').append(data['actions']);
        $('.lava-actionbar-block[data-scene-id="configure_skin"] *').lava().trigger('load.lava');
      }
      if ('hidden' in data && !data['hidden']) {
        return $('.lava-programme li[data-scene-id="configure_skin"]').removeClass('hidden-descendant');
      } else {
        return $('.lava-programme li[data-scene-id="configure_skin"]').addClass('hidden-descendant');
      }
    };
    return lavaBindMethods(methods, namespace);
  })(jQuery, window, document);

}).call(this);
