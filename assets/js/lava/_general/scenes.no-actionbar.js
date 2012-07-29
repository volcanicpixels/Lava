// Generated by CoffeeScript 1.3.3

/*
No actionbar callbacks
*/


(function() {

  (function($, window, document) {
    var methods, namespace;
    methods = {};
    namespace = 'lavaNoActionbarScene';
    methods.init = function(e, lava) {
      return $(lava).each(function() {
        var $blocks;
        $blocks = $(this).find('.lava-scene.lava-scene-no-actionbar');
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

}).call(this);
