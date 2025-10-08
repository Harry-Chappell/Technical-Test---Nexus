document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.hcdigital-form');

    forms.forEach(form => {

        const jsCheckField = form.querySelector('.js-check-field');
        if (jsCheckField) {
            jsCheckField.value = 'valid';
        }

        
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(function(field) {
            const label = form.querySelector('label[for="' + field.name + '"]');
            if (label && !label.innerHTML.includes('*')) {
                label.innerHTML += ' <span style="color:red">*</span>';
            }
        });

        
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(function(input) {
            const label = form.querySelector('label[for="' + input.name + '"]');
            if (label && input.accept) {
                let info = document.createElement('div');
                info.className = 'hcdigital-file-types';
                info.style.fontSize = '0.9em';
                info.style.color = '#666';
                info.style.marginBottom = '8px';
                info.textContent = 'Allowed file types: ' + input.accept + ' (Max size: 5MB)';
                label.parentNode.insertBefore(info, label.nextSibling);
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const formElements = form.elements;

            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (!element.name) continue;

                if (element.dataset.isReplyTo === 'true') {
                    formData.append(element.name + '_is_reply_to', 'true');
                }
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const formContainer = form.parentElement;
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = 'Sending...';

            const existingError = form.previousElementSibling;
            if (existingError && existingError.classList.contains('notice-error')) {
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
                            successMessage.className = 'notice notice-success';
                            successMessage.innerHTML = data.data.message;
                            form.innerHTML = '';
                            form.appendChild(successMessage);
                        }
                    } else {
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'notice notice-error';
                        errorMessage.innerHTML = data.data || 'An error occurred. Please try again.';
                        formContainer.insertBefore(errorMessage, form);
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'notice notice-error';
                    errorMessage.innerHTML = 'A network error occurred. Please try again.';
                    formContainer.insertBefore(errorMessage, form);
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    });
});