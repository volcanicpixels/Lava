###
Lava provides all of the lovely UI stuff
###


###
Global functions
###

lavaBindMethods = (methods, namespace = 'general') ->
	if methods['ready']
		jQuery(document).on "ready.lava.#{namespace}", methods['ready']
	if methods['init']
		jQuery(document).on "init.lava.#{namespace}", methods['init']
	if methods['defaultState']
		jQuery(document).on "defaultState.lava.#{namespace}", methods['defaultState']

jQuery(document).ready () ->
	jQuery('.lava-cntr').lava()

###
Main methods
###

do ($ = jQuery, window, document) -> # http://snippi.com/s/dp5g8iw

	methods = {}

	methods.init = () ->
		$(this).trigger "init.lava", this
		$(this).trigger "defaultState.lava", this
		return this

	# Adds plugin object to jQuery
	$.fn.extend
		# Change pluginName to your plugin's name.
		lava: (method) ->
			if methods[method]
				args =  Array.prototype.slice.call arguments, 1
				return methods[method].apply this, args
			else if typeof method is 'object' or not method
				return methods.init.apply this, arguments
			else
				debug.error 'Lava call failed', method, methods