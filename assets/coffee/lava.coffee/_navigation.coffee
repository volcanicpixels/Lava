###
cinder navigation
###


do ($ = jQuery, window, document) ->
	
	methods = {}

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$(this).find('.js-cinder-nav').on( 'click.cinder.cinderNav', 'li', methods.click )
			$(this).filter('.js-cinder-nav').on( 'click.cinder.cinderNav', 'li', methods.click )

	methods.click = () ->
		if $(this).hasClass( 'cinder-disabled' )
			return
		$cinderNav = $(this).parents('.js-cinder-nav')
		targetCntr = $cinderNav.attr 'data-cinder-nav-target-cntr'
		attr   = $cinderNav.attr 'data-cinder-nav-identifier'
		identifier = $(this).attr attr
		$cinderNav.find( 'li' ).removeClass('active-descendant').addClass('inactive-descendant')
		$(this).removeClass('inactive-descendant').addClass('active-descendant')
		$("##{targetCntr}").find('.js-cinder-nav-target.active-descendant').trigger('inactive.cinder')
		$("##{targetCntr}").find('.js-cinder-nav-target').removeClass('active-descendant').addClass('inactive-descendant')
		$("##{targetCntr}").find(".js-cinder-nav-target[#{attr}='#{identifier}']").removeClass('inactive-descendant').addClass('active-descendant').trigger( 'active.cinder' )

	cinder methods, 'cinderNav'