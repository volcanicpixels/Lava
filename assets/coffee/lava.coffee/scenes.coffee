###
Scene callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$scenes = $(this).find( '.lava-scene' )
			$scenes.on 'load.lava.lavaScene', methods.load
			$scenes.on 'active.lava.lavaScene', methods.active

			$scenes = $(this).filter( '.lava-scene' )
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
		$actionBar.find( '.lava-actionbar-block.active-descendant' ).addClass('inactive-descendant').removeClass('active-descendant').trigger( 'inactive.lava' )
		$actionBlock = $(this).data 'lava.scene.actionBlock'
		$actionBlock.addClass('active-descendant').removeClass('inactive-descendant').trigger( 'active.lava' )




	lavaBindMethods methods, 'lavaScene'