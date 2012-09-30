###
Skins page callbacks
###

###
Only the active skin gets loaded so if a user changes the skin they would have to save before they could configure it.

This aims to improve this experience by loading the settings via ajax when the skin is changed
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaPageSkins'
	selector = '.lava-scene[data-scene-id="Settings_Skins"] .lava-setting-skin-radio';
	cache = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )
			$elems.on "change.lava.#{namespace}", methods.change

	methods.change = (e) ->
		###
		Get now checked skin id
		check whether skin_id is in cache
		###
		skin = $(this).val()
		current_skin = $('.lava-scene[data-scene-id="Settings_Skin"]').attr( 'data-skin-id' );
		cache_element = {
			'scene': $('.lava-scene[data-scene-id="Settings_Skin"]').clone(),
			'actions': $('.lava-actionbar-block[data-scene-id="Settings_Skin"] *').clone(),
			'hidden' : $('.lava-programme li[data-scene-id="Settings_Skin"]').hasClass( 'hidden-descendant' )
		}
		cache[current_skin] = cache_element
		$('.lava-programme li[data-scene-id="Settings_Skin"]').addClass( 'hidden-descendant' )
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
			$('.lava-scene[data-scene-id="Settings_Skin"]').remove()
			$('#lava_stage').append( data['scene'] )
			$('.lava-scene[data-scene-id="Settings_Skin"]').lava().trigger 'load.lava'
		if 'actions' of data
			$('.lava-actionbar-block[data-scene-id="Settings_Skin"]').html( '' )
			$('.lava-actionbar-block[data-scene-id="Settings_Skin"]').append( data['actions'] )
			$('.lava-actionbar-block[data-scene-id="Settings_Skin"] *').lava().trigger 'load.lava'
		if 'hidden' of data and not data['hidden']
			$('.lava-programme li[data-scene-id="Settings_Skin"]').removeClass( 'hidden-descendant' )
		else
			$('.lava-programme li[data-scene-id="Settings_Skin"]').addClass( 'hidden-descendant' )





	lavaBindMethods methods, namespace