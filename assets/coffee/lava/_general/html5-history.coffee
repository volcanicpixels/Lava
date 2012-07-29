###
HTML5 history
###

do ($ = jQuery) ->
	methods = {}

	methods.init = (e, lava) ->
		if $('html').hasClass 'history'
			$(lava).each () ->
				$(this).find('a.js-lava-address').on 'click.lava.lavaHistory', methods.click
				$(this).filter('a.js-lava-address').on 'click.lava.lavaHistory', methods.click

	methods.click = (e) ->
		if window.History.enabled
			e.preventDefault()
			if $(this).hasClass( 'lava-disabled' ) or $(this).parents( '.lava-disabled' ).length > 0
				return
			url = $(this).attr 'href'
			window.History.pushState( null, null, url)

	lavaBindMethods methods, 'lavaHistory'