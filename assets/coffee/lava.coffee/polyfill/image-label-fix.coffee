do ($ = jQuery, Modernizr) ->
	methods = {}
	namespace = 'cinderPolyfill.cinderImgLabelFix'


	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$(@).find( 'label img.js-cinder-fix-click' ).on "click.cinder.#{namespace}", methods.click

	methods.click = (e) ->
		e.preventDefault()
		$(@).parents('label').click()


	cinder methods, namespace