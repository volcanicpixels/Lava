###
Skins page callbacks
###

###
Only the active skin gets loaded so if a user changes the skin they would have to save before they could configure it.

This aims to improve this experience by loading the settings via ajax when the skin is changed
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderPageSkins'
	selector = '.cinder-scene[data-scene-id="Settings_Skins"] .cinder-setting-skin-radio';
	cache = {}

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )
			$elems.on "change.cinder.#{namespace}", methods.change

	methods.change = (e) ->
		###
		Get now checked skin id
		check whether skin_id is in cache
		###
		skin = $(this).val()
		current_skin = $('.cinder-scene[data-scene-id="Settings_Skin"]').attr( 'data-skin-id' );
		cache_element = {
			'scene': $('.cinder-scene[data-scene-id="Settings_Skin"]').clone(),
			'actions': $('.cinder-actionbar-block[data-scene-id="Settings_Skin"] *').clone(),
			'hidden' : $('.cinder-programme li[data-scene-id="Settings_Skin"]').hasClass( 'hidden-descendant' )
		}
		cache[current_skin] = cache_element
		$('.cinder-programme li[data-scene-id="Settings_Skin"]').addClass( 'hidden-descendant' )
		if skin of cache
			methods.doReplace( cache[skin] )
		else
			data = {
				'action' : cinderVars.plugin_namespace + '_get_skin_settings',
				'skin' : skin
			}
			$.getJSON( ajaxurl, data, methods.doReplace )

	methods.doReplace = (data) ->
		if 'scene' of data
			$('.cinder-scene[data-scene-id="Settings_Skin"]').remove()
			$('#cinder_stage').append( data['scene'] )
			$('.cinder-scene[data-scene-id="Settings_Skin"]').cinder().trigger 'load.cinder'
		if 'actions' of data
			$('.cinder-actionbar-block[data-scene-id="Settings_Skin"]').html( '' )
			$('.cinder-actionbar-block[data-scene-id="Settings_Skin"]').append( data['actions'] )
			$('.cinder-actionbar-block[data-scene-id="Settings_Skin"] *').cinder().trigger 'load.cinder'
		if 'hidden' of data and not data['hidden']
			$('.cinder-programme li[data-scene-id="Settings_Skin"]').removeClass( 'hidden-descendant' )
		else
			$('.cinder-programme li[data-scene-id="Settings_Skin"]').addClass( 'hidden-descendant' )





	cinder methods, namespace