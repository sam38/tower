// Initialize the app when page is ready
$(document).ready(function() {

	const regexEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	const regexDate = /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/;
	

	// 
	const validate = function(e) {
		e.preventDefault();
		
		const $form = $(this);

		// Clear previous error and validate each field
		$form.find('.invalid-feedback').remove();
		$form.find('.form-control').removeClass('is-invalid').each(function(e, elm) {
			let $field = $(elm),
				validationRules = $field.data('validate'),
				value = $.trim($field.val());

			if (typeof validationRules !== 'undefined' && validationRules !== null) {
				// Split out the validation rules.
				let fieldIsValid = true,
					validationError = null;
				validationRules.split('|').forEach(rule => {
					if (fieldIsValid) {
						switch (rule) {
							case 'date':
								fieldIsValid = regexDate.test(value);
								validationError = 'Incorrect Date format.';
								break;
							case 'email':
								fieldIsValid = regexEmail.test(value);
								validationError = 'Enter a valid email address.';
								break;
							case 'number':
								fieldIsValid = ! isNaN(value);
								validationError = 'This field needs to be a valid number.';
								break;
							case 'required':
								fieldIsValid = value !== '';
								validationError = 'This field is required.';
								break;
						}
					}
				});
				if (! fieldIsValid) {
					$field.addClass('is-invalid')
						.parent().append(`<small class="invalid-feedback">${validationError}</small>`);
				}
			}
		});
		if ($form.find('.form-control.is-invalid').length) return false;

		// if form validation passes, then complete AJAX request
		$.post(
			getAPIUrl($form.attr('action')), 
			$form.serialize()
		);
		return false;
	};

	// find all forms which requires validation
	const $forms = $('form.validate-form');

	// Disable HTML5 validation
	$forms.attr('novalidate', 'novalidate').bind('submit', validate);

	// Load `Claims` form via AJAX
	const $btnClaimsForm = $('#btn-form-claim');
	$btnClaimsForm && $btnClaimsForm.on('click', function() {
		$.ajax({
			dataType: 'JSON',
			type: 'GET',
			url: '/wp-json/tower-forms/v1/claims',
			success: function(data) {
				// Add the form HTML to the DOM and bind form events
				$('#tab-claim').html(data)
					.find('form.validate-form')
					.bind('submit', validate);
			},
			error: function(error) {
				alert('Unable to load form! Check console for details.');
				console.error(error);
			}
		});
		// Unbind AJAX load for future requests
		$btnClaimsForm.off();
	});

	// A helper function to return full API URL with appended URI
	function getAPIUrl(uri) {
		// e.g. http://forms.tower.co.nz/wp-json/wp/v2/...
		return `/wp-json/wp/v2/${uri}`;
	}
});