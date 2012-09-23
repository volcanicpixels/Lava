###
Form Attribute
###

do ($ = jQuery, Modernizr) ->
	methods = {}
	namespace = 'lavaPolyfill.lavaFormattrribute'

	Modernizr.addTest( 'formattribute', () ->
		try
			form = document.createElement "form"
			input = document.createElement "input"
			div = document.createElement "div"
			id = "formtest"
			bool = false
			form.id = id
			input.setAttribute "form", id
			div.appendChild form
			div.appendChild input 

			document.documentElement.appendChild div

			bool = form.elements.length is 1;

			div.parentNode.removeChild(div);
			return bool
		catch e
			return false
	)

	methods.init = (e, lava) ->
		if not Modernizr.formattribute
			$(lava).each () ->
				$(@).find( '*[type="submit"][form]' ).on "click.lava.#{namespace}", methods.submitClick
				$(@).find( 'form[id]' ).on "submit.lava.#{namespace}", methods.formSubmit

	methods.submitClick = (e) ->
		e.preventDefault()
		id = $(@).attr('form')
		$form_ = $( "##{id}" )
		$(@).attr 'data-lava-formattribute', 'yes'
		$form_.submit()
		$(@).removeAttr 'data-lava-formattribute'

	methods.formSubmit = (e) ->
		e.preventDefault()
		id = $(@).attr 'id'
		$form = $(@).clone().removeAttr('id')
		$( "*[form='#{id}']" ).each () ->
			$clone = $(@).clone().removeAttr('form')

			# This is a hack to get around the fact that IE does not copy accross check state when cloned
			if $(@).is ':checked'
				$clone.attr( 'checked', 'checked' )
			else
				$clone.removeAttr('checked')
			$clone.appendTo $form
		$form.find('*[data-lava-formattribute="yes"]').each () ->
			value = $(@).val()
			if value is undefined
				value = ''
			name  = $(@).attr('name')
			if typeof(name) != 'undefined'
				$("<input type='hidden' name='#{name}' value='#{value}'>").appendTo $form
		$form.appendTo( document.documentElement )
		$form.submit()
		$form.remove()



	lavaBindMethods methods, namespace