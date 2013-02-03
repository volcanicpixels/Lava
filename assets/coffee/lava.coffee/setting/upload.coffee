do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'cinderFileUpload'
	selector = '.cinder-file-upload'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.each () ->
				$cntr = $(this).parents('.cinder-file-upload-cntr')
				$dropZone = $cntr.find('.cinder-file-upload-dropzone')

				$(this).fileupload({
					dataType: 'json',
					dropZone: $dropZone,
					change: (e, data) ->
						$cntr.find('.cinder-file-upload-message').show()
					,drop: (e, data) ->
						$cntr.find('.cinder-file-upload-message').show()
					,done: (e, data) ->
						url = data.result.url
						$cntr.find('.cinder-file-upload-message').hide()
						$cntr.find('.cinder-file-upload-val').val(url)
						$cntr.find('.cinder-file-upload-src').attr('src', url)
				})

	cinder methods, namespace