###
Animations
###
do ($ = jQuery) ->
	methods = {}
	selector = '.js-lava-animation-slide-right'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )
			$elems.on 'active.lava.lavaAnimation', methods.slideRight

	methods.slideRight = () ->
		$(this).css({'opacity': 0, 'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)

	lavaBindMethods methods, 'lavaAnimations'