###
No actionbar callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderNoActionbarScene'
	selector = '.cinder-scene.cinder-scene-no-actionbar'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.on "active.cinder.#{namespace}", methods.active
			$elems.on "inactive.cinder.#{namespace}", methods.inactive

	methods.active = (e) ->
		# remove action bar
		$('#cinder_theatre').addClass( 'no-actionbar' )

	methods.inactive = (e) ->
		# replace actionbar
		$('#cinder_theatre').removeClass( 'no-actionbar' )


	cinder methods, namespace