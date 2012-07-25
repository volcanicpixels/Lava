###
HTML5 history
###

do ($ = jQuery) ->
	methods = {}

	methods.init = (e, lava) ->
		if $('html').hasClass 'history'
			$(lava).each () ->
				$(this).find('a.js-lava-address').on 'click.lava.lavaHistory', methods.click

	methods.click = (e) ->
		if window.History.enabled
			e.preventDefault()
			url = $(this).attr 'href'
			window.History.pushState( null, null, url)

	lavaBindMethods methods, 'lavaHistory'