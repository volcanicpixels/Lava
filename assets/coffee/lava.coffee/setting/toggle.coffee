###
Toggle settings callback
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderSettingToggle'
	selector = '.cinder-setting.cinder-setting-toggle .cinder-setting-toggle-input'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.on "change.cinder.#{namespace}", methods.change
			$elems.trigger "change.cinder.#{namespace}"

	methods.change = (e) ->
		setting_id = $(this).parents( '.cinder-setting-toggle' ).attr( 'data-setting-id' )
		$elems = $( ".cinder-setting[data-setting-toggle='#{setting_id}']" )
		if $(this).is ':checked'
			$(this).parents( '.cinder-setting-toggle' ).addClass( 'cinder-setting-no-border' )
			$elems.removeClass( 'cinder-setting-toggle-hidden' ).addClass( 'cinder-setting-toggle-visible' )
		else
			$elems.removeClass( 'cinder-setting-toggle-visible' ).addClass( 'cinder-setting-toggle-hidden' )
			$(this).parents( '.cinder-setting-toggle' ).removeClass( 'cinder-setting-no-border' )


	cinder methods, namespace