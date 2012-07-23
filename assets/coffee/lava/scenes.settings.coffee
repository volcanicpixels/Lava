###
Settings Scene callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaSettingsScene'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$scenes = $(this).find( '.lava-scene.lava-settings-scene' )

			$scenes.on "load.lava.#{namespace}", methods.load


	lavaBindMethods methods, namespace