###
Animations
###
do ($ = jQuery) ->
	methods = {}

	methods.init = (e, lava) ->
		$(lava).each () ->
			$(this).find('.js-lava-animation-slide-right').on 'active.lava.lavaAnimation', methods.slideRight

	methods.slideRight = () ->
		$(this).css({'opacity': 0, 'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)

	lavaBindMethods methods, 'lavaAnimations'