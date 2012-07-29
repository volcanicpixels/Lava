###
Skins page callbacks
###

###
Only the active skin gets loaded so if a user changes the skin they would have to save before they could configure it.

This aims to improve this experience by loading the settings via ajax when the skin is changed
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaSkinsPage'
	cache = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$(this).find( '.lava-scene[data-scene-id="choose_skin"] .lava-setting-skin-radio' ).on "change.lava.#{namespace}", methods.change
			$(this).filter( '.lava-scene[data-scene-id="choose_skin"] .lava-setting-skin-radio' ).on "change.lava.#{namespace}", methods.change

	methods.change = (e) ->
		###
		Get now checked skin id
		check whether skin_id is in cache
		###
		skin = $(this).val()
		current_skin = $('.lava-scene[data-scene-id="configure_skin"]').attr( 'data-skin-id' );
		cache_element = {
			'scene': $('.lava-scene[data-scene-id="configure_skin"]').clone(),
			'actions': $('.lava-actionbar-block[data-scene-id="configure_skin"] *').clone(),
			'hidden' : $('.lava-programme li[data-scene-id="configure_skin"]').hasClass( 'hidden-descendant' )
		}
		cache[current_skin] = cache_element
		$('.lava-programme li[data-scene-id="configure_skin"]').addClass( 'hidden-descendant' )
		if skin of cache
			methods.doReplace( cache[skin] )
		else
			data = {
				'action' : lavaVars.plugin_namespace + '_get_skin_settings',
				'skin' : skin
			}
			$.getJSON( ajaxurl, data, methods.doReplace )

	methods.doReplace = (data) ->
		if 'scene' of data
			$('.lava-scene[data-scene-id="configure_skin"]').remove()
			$('#lava-stage').append( data['scene'] )
			$('.lava-scene[data-scene-id="configure_skin"]').lava().trigger 'load.lava'
		if 'actions' of data
			$('.lava-actionbar-block[data-scene-id="configure_skin"]').html( '' )
			$('.lava-actionbar-block[data-scene-id="configure_skin"]').append( data['actions'] )
			$('.lava-actionbar-block[data-scene-id="configure_skin"] *').lava().trigger 'load.lava'
		if 'hidden' of data and not data['hidden']
			$('.lava-programme li[data-scene-id="configure_skin"]').removeClass( 'hidden-descendant' )
		else
			$('.lava-programme li[data-scene-id="configure_skin"]').addClass( 'hidden-descendant' )





	lavaBindMethods methods, namespace