###
Animations
###
do ($ = jQuery) ->
	methods = {}

	methods.init = (e, lava) ->
		selector = '.js-lava-animation-slide-right'
		$(lava).each () ->
			$(this).find( selector ).on 'active.lava.lavaAnimation', methods.slideRight
			$(this).filter( selector ).on 'active.lava.lavaAnimation', methods.slideRight

	methods.slideRight = () ->
		$(this).css({'opacity': 0, 'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)

	lavaBindMethods methods, 'lavaAnimations'