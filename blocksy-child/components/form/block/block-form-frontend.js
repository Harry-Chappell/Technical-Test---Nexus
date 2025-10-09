document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.hcdigital-form');

    forms.forEach(form => {
        // Populate the JS check field to validate the user has JS enabled
        const jsCheckField = form.querySelector('.js-check-field');
        if (jsCheckField) {
            jsCheckField.value = 'valid';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData();
            const formElements = form.elements;

            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (!element.name) continue;

                if (element.type === 'checkbox' || element.type === 'radio') {
                    if (element.checked) {
                        formData.append(element.name, element.value);
                    }
                } else {
                    formData.append(element.name, element.value);
                }
                
                if (element.dataset.isReplyTo === 'true') {
                    formData.append(element.name + '_is_reply_to', 'true');
                    console.log(element.name, element.dataset.isReplyTo, element.getAttribute('data-is-reply-to'));
                }
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const formContainer = form.parentElement;
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = 'Sending...';

            const existingError = form.previousElementSibling;
            if (existingError && (existingError.classList.contains('hcdigital-form-error') || existingError.classList.contains('hcdigital-form-message'))) {
                existingError.remove();
            }

            fetch(hcdigital_form_ajax.ajax_url, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.redirect) {
                        window.location.href = data.data.redirect;
                    } else {
                        const successMessage = document.createElement('div');
                        successMessage.className = 'hcdigital-form-success';
                        successMessage.innerHTML = data.data.message;
                        form.innerHTML = ''; 
                        form.appendChild(successMessage);
                    }
                } else {
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'hcdigital-form-error';
                    errorMessage.innerHTML = data.data || 'An error occurred. Please try again.';
                    formContainer.insertBefore(errorMessage, form);
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMessage = document.createElement('div');
                errorMessage.className = 'hcdigital-form-error';
                errorMessage.innerHTML = 'A network error occurred. Please try again.';
                formContainer.insertBefore(errorMessage, form);
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    });
});