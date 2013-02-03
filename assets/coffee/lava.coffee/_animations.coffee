###
Animations
###
do ($ = jQuery) ->
	methods = {}
	selector = '.js-cinder-animation-slide-right'

	methods.init = (e, cinder) ->
		$(cinder).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )
			$elems.on 'active.cinder.cinderAnimation', methods.slideRight

	methods.slideRight = () ->
		$(this).css({'opacity': 0, 'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)

	cinder methods, 'cinderAnimations'