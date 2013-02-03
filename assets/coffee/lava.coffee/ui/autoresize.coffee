###
Autosizing
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderAutoResize'
	selector = '.cinder-auto-resize'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.autoResize()
	cinder methods, namespace