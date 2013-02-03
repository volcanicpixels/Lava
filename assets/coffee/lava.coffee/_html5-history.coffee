###
HTML5 history
###

do ($ = jQuery) ->
	methods = {}

	methods.init = (e, cinder) ->
		if $('html').hasClass 'history'
			$(cinder).each () ->
				$(this).find('a.js-cinder-address').on 'click.cinder.cinderHistory', methods.click
				$(this).filter('a.js-cinder-address').on 'click.cinder.cinderHistory', methods.click

	methods.click = (e) ->
		if window.History.enabled
			e.preventDefault()
			if $(this).hasClass( 'cinder-disabled' ) or $(this).parents( '.cinder-disabled' ).length > 0
				return
			url = $(this).attr 'href'
			window.History.pushState( null, null, url)

	cinder methods, 'cinderHistory'