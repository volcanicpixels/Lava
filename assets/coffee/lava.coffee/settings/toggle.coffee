###
Toggle settings callback
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaToggleSetting'
	selector = '.lava-setting.lava-toggle-setting .lava-setting-toggle-input'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.on "change.lava.#{namespace}", methods.change
			$elems.trigger "change.lava.#{namespace}"

	methods.change = (e) ->
		setting_id = $(this).parents( '.lava-toggle-setting' ).attr( 'data-setting-id' )
		$elems = $( ".lava-setting[data-setting-toggle='#{setting_id}']" )
		if $(this).is ':checked'
			$(this).parents( '.lava-toggle-setting' ).addClass( 'lava-setting-no-border' )
			$elems.removeClass( 'lava-setting-toggle-hidden' ).addClass( 'lava-setting-toggle-visible' )
		else
			$elems.removeClass( 'lava-setting-toggle-visible' ).addClass( 'lava-setting-toggle-hidden' )
			$(this).parents( '.lava-toggle-setting' ).removeClass( 'lava-setting-no-border' )


	lavaBindMethods methods, namespace