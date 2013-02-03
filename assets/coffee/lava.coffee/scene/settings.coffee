###
Settings Scene callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderSettingsScene'
	selector = '.cinder-scene.cinder-scene-settings'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )



	cinder methods, namespace

