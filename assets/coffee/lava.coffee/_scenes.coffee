###
Scene callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$scenes = $(this).find( '.cinder-scene' )
			$scenes.on 'load.cinder.cinderScene', methods.load
			$scenes.on 'active.cinder.cinderScene', methods.active

			$scenes = $(this).filter( '.cinder-scene' )
			$scenes.on 'load.cinder.cinderScene', methods.load
			$scenes.on 'active.cinder.cinderScene', methods.active


	methods.defaultState = (e, cinder) ->
		$(cinder).each () ->
			$scenes = $(this).find( '.cinder-scene' )
			$scenes.trigger 'load.cinder'
			$(this).find( '.cinder-scene.active-descendant' ).trigger 'active.cinder' 

	methods.load = () ->
		# add actionbar cntr
		sceneId = $(this).attr 'data-scene-id'
		$actionBlock = $(".cinder-actionbar-block[data-scene-id='#{sceneId}']")
		$(this).data 'cinder.scene.actionBlock', $actionBlock

	methods.active = () ->
		$actionBar = $(".cinder-actionbar-cntr")
		$actionBar.find( '.cinder-actionbar-block.active-descendant' ).addClass('inactive-descendant').removeClass('active-descendant').trigger( 'inactive.cinder' )
		$actionBlock = $(this).data 'cinder.scene.actionBlock'
		$actionBlock.addClass('active-descendant').removeClass('inactive-descendant').trigger( 'active.cinder' )




	cinder methods, 'cinderScene'