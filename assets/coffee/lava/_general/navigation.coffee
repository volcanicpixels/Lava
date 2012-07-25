###
Lava navigation
###


do ($ = jQuery, window, document) ->
	
	methods = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$(this).find('.js-lava-nav').on( 'click.lava.lavaNav', 'li', methods.click )

	methods.click = () ->
		$lavaNav = $(this).parents('.js-lava-nav')
		targetCntr = $lavaNav.attr 'data-lava-nav-target-cntr'
		attr   = $lavaNav.attr 'data-lava-nav-identifier'
		identifier = $(this).attr attr

		$lavaNav.find( 'li' ).removeClass('active-descendant').addClass('inactive-descendant')
		$(this).removeClass('inactive-descendant').addClass('active-descendant')
		$("##{targetCntr}").find('.js-lava-nav-target').removeClass('active-descendant').addClass('inactive-descendant')
		$("##{targetCntr}").find(".js-lava-nav-target[#{attr}='#{identifier}']").removeClass('inactive-descendant').addClass('active-descendant').trigger( 'active.lava' )

	lavaBindMethods methods, 'lavaNav'