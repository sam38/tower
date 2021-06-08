// Initialize the app when page is ready
$(document).ready(function() {
	
    // this function will validate the form and post to via AJAX.
	const validateAndPost = function(e) {
		e.preventDefault();
		
		const $form = $(this);
        // disable buttons
        const $buttons = $form.find('button');
        $buttons.attr('disabled', 'disabled');

		// Clear previous error and validate each field
		$form.find('.invalid-feedback, .alert').remove();
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
                                // date should be in the format DD/MM/YYYY e.g. 21/1/2020
                                validationError = 'Incorrect Date format.';
                                fieldIsValid = false;
                                const dateParts = value.split('/');
                                if (dateParts.length === 3 
                                    && Date.parse(dateParts.reverse().join('-')) // YYYY-MM-DD
                                ) {
                                    fieldIsValid = true;
                                }
								break;
							case 'email':
                                const regexEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
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
		if ($form.find('.form-control.is-invalid').length) {
            $buttons.removeAttr('disabled');
            return false;
        }

		// perform AJAX request
		$.post(
			`/wp-json/tower-forms/v1/form?type=${$form.attr('action')}`, 
			$form.serialize()
		).done(function(data) {
            if (data.success) {
                // form persisted!
                $form.trigger('reset');
                // add alert box
                $form.find('.card-body')
                    .prepend(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="far fa-check-circle"></i> Your form has been submitted.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            } else {
                // backend error - display error to associated field
                for (let fieldName in data.errors) {
                    $form.find(`.form-control[name="${fieldName}"]`)
                        .addClass('is-invalid')
                        .parent().append(`
                            <small class="invalid-feedback">
                                ${data.errors[fieldName]}
                            </small>
                        `);
                }
            }
            // $form.reset();
        }).fail(function(error) {
            alert('Unable to complete your request!\r\nCheck console for detail');
            console.error(error);
        }).always(function() {
            $buttons.removeAttr('disabled');
        });
	};

	// find all forms which requires validation
	const $forms = $('form.validate-form');

	// Disable default HTML5 validation
	$forms.attr('novalidate', 'novalidate').bind('submit', validateAndPost);

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
					.bind('submit', validateAndPost);
			},
			error: function(error) {
                $('#tab-claim').html(`
                    <h4 class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load!
                    </h4>
                    <p>Make sure your Permalink settings is set to "Post name".</p>
                `);
				console.error(error);
			}
		});
		// Unbind AJAX load for future requests
		$btnClaimsForm.off();
	});
});