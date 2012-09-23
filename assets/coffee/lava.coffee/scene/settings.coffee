###
Settings Scene callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaSettingsScene'
	selector = '.lava-scene.lava-scene-settings'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )



	lavaBindMethods methods, namespace

