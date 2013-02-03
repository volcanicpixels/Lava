###
Form Attribute
###

do ($ = jQuery, Modernizr) ->
	methods = {}
	namespace = 'cinderPolyfill.cinderFormattrribute'

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

	methods.init = (e, cinder) ->
		if not Modernizr.formattribute
			$(cinder).each () ->
				$(@).find( '*[type="submit"][form]' ).on "click.cinder.#{namespace}", methods.submitClick
				$(@).find( 'form[id]' ).on "submit.cinder.#{namespace}", methods.formSubmit

	methods.submitClick = (e) ->
		e.preventDefault()
		id = $(@).attr('form')
		$form_ = $( "##{id}" )
		$(@).attr 'data-cinder-formattribute', 'yes'
		$form_.submit()
		$(@).removeAttr 'data-cinder-formattribute'

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
		$form.find('*[data-cinder-formattribute="yes"]').each () ->
			value = $(@).val()
			if value is undefined
				value = ''
			name  = $(@).attr('name')
			if typeof(name) != 'undefined'
				$("<input type='hidden' name='#{name}' value='#{value}'>").appendTo $form
		$form.appendTo( document.documentElement )
		$form.submit()
		$form.remove()



	cinder methods, namespace