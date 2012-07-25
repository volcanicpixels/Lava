// Generated by CoffeeScript 1.3.3
(function() {

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

}).call(this);