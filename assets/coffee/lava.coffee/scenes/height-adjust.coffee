###
Height adjust callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaHeightAdjust'
	selector = '.lava-scene.js-height-adjust'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.on "load.lava.#{namespace}", methods.load


			$(window).on "resize.lava.#{namespace}", methods.resizeWindow


	methods.load = (e) ->
		$(this).attr 'data-height-adjust-min', $(this).height()
		setTimeout methods.resizeWindow, 100

	methods.resizeWindow = (e) ->
		$( selector ).each methods.resize

	methods.resize = () ->
		$this = $(@)
		doc_height = $(document).height()
		win_height = $(window).height()
		min_height = $this.attr( 'data-height-adjust-min' )


		if doc_height is win_height
			# they are the same so we can make it larger
			loop_count = 0
			while (doc_height is win_height) and ( loop_count < 1000 )
				#make it larger by 1px until condition is no longer true or until loop expires
				loop_count += 1
				$this.height $this.height() + 1
				doc_height = $(document).height()
				win_height = $(window).height()

			$this.height $this.height() - 1

		old_doc_height = doc_height = $(document).height()

		if ( doc_height > win_height ) and ( $this.height() > min_height )
			#doc is too big so lets first check to see if we can alter it
			$this.height $this.height() - 1
			doc_height = $(document).height()
			while (doc_height > win_height) and ( old_doc_height > doc_height ) and ( $this.height() > min_height )
				old_doc_height = doc_height
				$this.height $this.height() - 1
				doc_height = $(document).height()

			#debug.log( (doc_height > win_height), ( old_doc_height > doc_height ), ( $this.height() > min_height ), old_doc_height )









	lavaBindMethods methods, namespace