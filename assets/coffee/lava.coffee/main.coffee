###
Cinder is the javascript UI framework
###

###
It is an event driven framework, each module defines a 
###

cinder = window.cinder = (methods, namespace = 'general') ->
	if methods['ready']
		jQuery(document).on "ready.cinder.#{namespace}", methods['ready']
	if methods['init']
		jQuery(document).on "init.cinder.#{namespace}", methods['init']
	if methods['defaultState']
		jQuery(document).on "defaultState.cinder.#{namespace}", methods['defaultState']

jQuery(document).ready () ->
	jQuery('.cinder-cntr').cinder()

###
Main methods
###

do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw

	methods = {}

	methods.init = () ->
		$(this).trigger "init.cinder", this
		$(this).trigger "defaultState.cinder", this
		return this

	# Adds plugin object to jQuery
	$.fn.extend
		cinder: (method) ->
			if methods[method]
				args =  Array.prototype.slice.call arguments, 1
				return methods[method].apply this, args
			else if typeof method is 'object' or not method
				return methods.init.apply this, arguments
			else
				console.error 'Cinder call failed', method, methods