do ($ = jQuery, Modernizr) ->
	methods = {}
	namespace = 'lavaPolyfill.lavaImgLabelFix'


	methods.init = (e, lava) ->
		$(lava).each () ->
			$(@).find( 'label img.js-lava-fix-click' ).on "click.lava.#{namespace}", methods.click

	methods.click = (e) ->
		e.preventDefault()
		$(@).parents('label').click()


	lavaBindMethods methods, namespace