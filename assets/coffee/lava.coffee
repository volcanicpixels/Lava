###
Lava provides all of the lovely UI stuff
###


###
Global functions
###

lavaBindMethods = (methods, namespace = 'general') ->
	if methods['ready']
		jQuery(document).on "ready.lava.#{namespace}", methods['ready']
	if methods['init']
		jQuery(document).on "init.lava.#{namespace}", methods['init']
	if methods['defaultState']
		jQuery(document).on "defaultState.lava.#{namespace}", methods['defaultState']

jQuery(document).ready () ->
	jQuery('.lava-cntr').lava()

###
Main methods
###

do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw

	methods = {}

	methods.init = () ->
		$(this).trigger "init.lava", this
		$(this).trigger "defaultState.lava", this
		return this

	# Adds plugin object to jQuery
	$.fn.extend
		# Change pluginName to your plugin's name.
		lava: (method) ->
			if methods[method]
				args =  Array.prototype.slice.call arguments, 1
				return methods[method].apply this, args
			else if typeof method is 'object' or not method
				return methods.init.apply this, arguments
			else
				debug.error 'Lava call failed', method, methods

###
Lava navigation
###


do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw
	
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


###
Animations
###
do ($ = jQuery) ->
	methods = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$(this).find('.js-lava-animation-slide-right').on 'active.lava.lavaAnimation', methods.slideRight

	methods.slideRight = () ->
		$(this).css({'opacity': 0, 'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)

	lavaBindMethods methods, 'lavaAnimations'


###
Scene callbacks
###

do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw

	methods = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$scenes = $(this).find( '.lava-scene' )

			$scenes.on 'load.lava.lavaScene', methods.load
			$scenes.on 'active.lava.lavaScene', methods.active


	methods.defaultState = (e, lava) ->
		$(lava).each () ->
			$scenes = $(this).find( '.lava-scene' )
			$scenes.trigger 'load.lava'
			$(this).find( '.lava-scene.active-descendant' ).trigger 'active.lava' 

	methods.load = () ->
		# add actionbar cntr
		sceneId = $(this).attr 'data-scene-id'
		$actionBlock = $(".lava-actionbar-block[data-scene-id='#{sceneId}']")
		$(this).data 'lava.scene.actionBlock', $actionBlock

	methods.active = () ->
		$actionBar = $(".lava-actionbar-cntr")
		$actionBar.find( '.lava-actionbar-block' ).addClass('inactive-descendant').removeClass('active-descendant')
		$actionBlock = $(this).data 'lava.scene.actionBlock'
		$actionBlock.addClass('active-descendant').removeClass('inactive-descendant')




	lavaBindMethods methods, 'lavaScene'




	
###
Settings Scene callbacks
###

do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw

	methods = {}
	namespace = 'lavaSettingsScene'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$scenes = $(this).find( '.lava-scene.lava-settings-scene' )

			$scenes.on "load.lava.#{namespace}", methods.load



	methods.load = (e) ->
		if not $(this).data 'lava.scene.createdSettingButtons'
			$(this).data 'lava.scene.createdSettingButtons', true
			sceneId = $(this).attr 'data-scene-id'
			$actionBlock = $(".lava-actionbar-block[data-scene-id='#{sceneId}']")


	lavaBindMethods methods, namespace





	







