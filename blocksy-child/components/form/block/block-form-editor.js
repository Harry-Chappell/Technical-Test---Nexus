(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var { InnerBlocks, InspectorControls, useBlockProps } = wp.blockEditor;
    var { PanelBody, TextControl, ToggleControl, SelectControl, TextareaControl, Spinner, Notice } = wp.components;
    var { createElement, Fragment } = wp.element;
    var { useSelect } = wp.data;

    var FORM_NAME = 'hcdigital/form';
    var FORM_INPUT_NAME = 'hcdigital/form-input';
    var FORM_TEXTAREA_NAME = 'hcdigital/form-textarea';
    var FORM_SELECT_NAME = 'hcdigital/form-select';
    var ALLOWED_BLOCKS = [FORM_INPUT_NAME, FORM_TEXTAREA_NAME, FORM_SELECT_NAME];

    registerBlockType(FORM_INPUT_NAME, {
        title: 'Form: Text Input',
        parent: [FORM_NAME],
        icon: 'edit-page',
        category: 'widgets',
        attributes: {
            label: { type: 'string', default: '' },
            name: { type: 'string', default: '' },
            placeholder: { type: 'string', default: '' },
            type: { type: 'string', default: 'text' },
            isRequired: { type: 'boolean', default: false },
            isNameManuallySet: { type: 'boolean', default: false },
            isReplyTo: { type: 'boolean', default: false }
        },
        edit: function (props) {
            var { attributes, setAttributes } = props;
            var { label, name, placeholder, type, isRequired, isNameManuallySet, isReplyTo } = attributes;
            var blockProps = useBlockProps({ className: 'form-field' });

            var onLabelChange = function(newLabel) {
                setAttributes({ label: newLabel });
                if (!isNameManuallySet) {
                    var newName = newLabel.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-_]/g, '');
                    setAttributes({ name: newName });
                }
            };

            var onNameChange = function(newName) {
                setAttributes({ name: newName.toLowerCase().replace(/[^a-z0-9-_]/g, ''), isNameManuallySet: true });
            };

            return createElement(Fragment, null,
                createElement(InspectorControls, null,
                    createElement(PanelBody, { title: 'Field Settings' },
                        createElement(TextControl, { label: 'Label', value: label, onChange: onLabelChange }),
                        createElement(TextControl, { label: 'Field Name', value: name, onChange: onNameChange, help: 'Unique name for this field.' }),
                        createElement(SelectControl, { label: 'Field Type', value: type, options: [{ label: 'Text', value: 'text' }, { label: 'Email', value: 'email' }, { label: 'Tel', value: 'tel' }], onChange: (val) => setAttributes({ type: val }) }),
                        createElement(TextControl, { label: 'Placeholder', value: placeholder, onChange: (val) => setAttributes({ placeholder: val }) }),
                        createElement(ToggleControl, { label: 'Required', checked: isRequired, onChange: (val) => setAttributes({ isRequired: val }) }),
                        type === 'email' && createElement(ToggleControl, { label: 'Use as Reply-To Email', checked: isReplyTo, onChange: (val) => setAttributes({ isReplyTo: val }), help: 'If checked, this email address will be used as the reply-to address for the notification email.' })
                    )
                ),
                createElement('div', blockProps,
                    createElement('label', null, label || 'Input Field'),
                    createElement('input', { type: 'text', disabled: true, placeholder: placeholder, style: { width: '100%', padding: '8px' } })
                )
            );
        },
        save: function (props) {
            var { attributes } = props;
            var { label, name, placeholder, type, isRequired, isReplyTo } = attributes;
            var inputProps = {
                id: name,
                name: name,
                type: type,
                placeholder: placeholder,
                required: isRequired,
                'data-is-reply-to': isReplyTo ? 'true' : undefined
            };

            return createElement('div', { className: 'form-field' },
                createElement('label', { htmlFor: name }, label),
                createElement('input', inputProps)
            );
        }
    });

    registerBlockType(FORM_TEXTAREA_NAME, {
        title: 'Form: Text Area',
        parent: [FORM_NAME],
        icon: 'edit-large',
        category: 'widgets',
        attributes: {
            label: { type: 'string', default: '' },
            name: { type: 'string', default: '' },
            placeholder: { type: 'string', default: '' },
            isRequired: { type: 'boolean', default: false },
            isNameManuallySet: { type: 'boolean', default: false }
        },
        edit: function (props) {
            var { attributes, setAttributes } = props;
            var { label, name, placeholder, isRequired, isNameManuallySet } = attributes;
            var blockProps = useBlockProps({ className: 'form-field' });

            var onLabelChange = function(newLabel) {
                setAttributes({ label: newLabel });
                if (!isNameManuallySet) {
                    var newName = newLabel.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-_]/g, '');
                    setAttributes({ name: newName });
                }
            };

            var onNameChange = function(newName) {
                setAttributes({ name: newName.toLowerCase().replace(/[^a-z0-9-_]/g, ''), isNameManuallySet: true });
            };

            return createElement(Fragment, null,
                createElement(InspectorControls, null,
                    createElement(PanelBody, { title: 'Field Settings' },
                        createElement(TextControl, { label: 'Label', value: label, onChange: onLabelChange }),
                        createElement(TextControl, { label: 'Field Name', value: name, onChange: onNameChange, help: 'Unique name for this field.' }),
                        createElement(TextControl, { label: 'Placeholder', value: placeholder, onChange: (val) => setAttributes({ placeholder: val }) }),
                        createElement(ToggleControl, { label: 'Required', checked: isRequired, onChange: (val) => setAttributes({ isRequired: val }) })
                    )
                ),
                createElement('div', blockProps,
                    createElement('label', null, label || 'Textarea Field'),
                    createElement('textarea', { disabled: true, placeholder: placeholder, style: { width: '100%', padding: '8px' } })
                )
            );
        },
        save: function (props) {
            var { attributes } = props;
            var { label, name, placeholder, isRequired } = attributes;
            return createElement('div', { className: 'form-field' },
                createElement('label', { htmlFor: name }, label),
                createElement('textarea', { id: name, name: name, placeholder: placeholder, required: isRequired, rows: 5 })
            );
        }
    });

    registerBlockType(FORM_SELECT_NAME, {
        title: 'Form: Select Field',
        parent: [FORM_NAME],
        icon: 'arrow-down-alt2',
        category: 'widgets',
        attributes: {
            label: { type: 'string', default: '' },
            name: { type: 'string', default: '' },
            isRequired: { type: 'boolean', default: false },
            isNameManuallySet: { type: 'boolean', default: false },
            options: { type: 'string', default: '' }
        },
        edit: function (props) {
            var { attributes, setAttributes } = props;
            var { label, name, isRequired, isNameManuallySet, options } = attributes;
            var blockProps = useBlockProps({ className: 'form-field' });

            var onLabelChange = function(newLabel) {
                setAttributes({ label: newLabel });
                if (!isNameManuallySet) {
                    var newName = newLabel.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-_]/g, '');
                    setAttributes({ name: newName });
                }
            };

            var onNameChange = function(newName) {
                setAttributes({ name: newName.toLowerCase().replace(/[^a-z0-9-_]/g, ''), isNameManuallySet: true });
            };

            var optionLines = options.split('\n').map(function(line, index) {
                return createElement('option', { key: index, disabled: true }, line.split('|')[0]);
            });

            return createElement(Fragment, null,
                createElement(InspectorControls, null,
                    createElement(PanelBody, { title: 'Field Settings' },
                        createElement(TextControl, { label: 'Label', value: label, onChange: onLabelChange }),
                        createElement(TextControl, { label: 'Field Name', value: name, onChange: onNameChange, help: 'Unique name for this field.' }),
                        createElement(TextareaControl, {
                            label: 'Options',
                            value: options,
                            onChange: (val) => setAttributes({ options: val }),
                            help: 'Enter one option per line. Use "value|Label" for different values.'
                        }),
                        createElement(ToggleControl, { label: 'Required', checked: isRequired, onChange: (val) => setAttributes({ isRequired: val }) })
                    )
                ),
                createElement('div', blockProps,
                    createElement('label', null, label || 'Select Field'),
                    createElement('select', { disabled: true, style: { width: '100%', maxWidth: '100%', padding: '8px' } }, optionLines)
                )
            );
        },
        save: function (props) {
            var { attributes } = props;
            var { label, name, isRequired, options } = attributes;
            
            var optionLines = options.split('\n').filter(line => line.trim() !== '').map(function(line) {
                var parts = line.split('|');
                var optionValue = parts.length > 1 ? parts[0].trim() : line.trim();
                var optionLabel = parts.length > 1 ? parts[1].trim() : line.trim();
                return createElement('option', { key: optionValue, value: optionValue }, optionLabel);
            });

            return createElement('div', { className: 'form-field' },
                createElement('label', { htmlFor: name }, label),
                createElement('select', { id: name, name: name, required: isRequired }, optionLines)
            );
        }
    });

    var TEMPLATE = [
        [FORM_INPUT_NAME, { label: 'Name', name: 'name', type: 'text', isRequired: true, isNameManuallySet: true }],
        [FORM_INPUT_NAME, { label: 'Email', name: 'email', type: 'email', isRequired: true, isNameManuallySet: true }],
        [FORM_TEXTAREA_NAME, { label: 'Message', name: 'message', isRequired: true, isNameManuallySet: true }]
    ];

    registerBlockType(FORM_NAME, {
        title: 'Form',
        icon: 'email',
        category: 'widgets',
        attributes: {
            formName: { type: 'string', default: '' },
            recipientEmail: { type: 'string', default: '' },
            emailSubject: { type: 'string', default: 'New Form Submission' },
            submitButtonText: { type: 'string', default: 'Submit' },
            thankYouPageId: { type: 'number', default: 0 },
            thankYouMessage: { type: 'string', default: 'Thank you for your submission!' },
            formId: { type: 'string', default: '' },
        },
        edit: function (props) {
            var { attributes, setAttributes, clientId } = props;
            var { formName, recipientEmail, emailSubject, submitButtonText, thankYouPageId, thankYouMessage, formId } = attributes;
            var blockProps = useBlockProps();

            if (!formId) {
                setAttributes({ formId: 'hcdigital-form-' + clientId });
            }

            var pages = useSelect(function (select) {
                return select('core').getEntityRecords('postType', 'page', { per_page: -1 });
            }, []);

            var pageOptions = !pages 
                ? [ { value: '', label: 'Loading...' } ] 
                : [ { value: 0, label: 'Show a message on this page' } ].concat(
                    pages.map(function (page) {
                        return { value: page.id, label: page.title.rendered };
                    })
                );

            return createElement(Fragment, null,
                createElement(InspectorControls, null,
                    createElement(PanelBody, { title: 'Form Settings' },
                        createElement(TextControl, {
                            label: 'Form Name (Required)',
                            value: formName,
                            onChange: (val) => setAttributes({ formName: val }),
                            help: 'Name for this form for easier identification (e.g., for analytics).'
                        }),
                        createElement(TextControl, {
                            label: 'Recipient Email(s) (Required)',
                            value: recipientEmail,
                            onChange: (val) => setAttributes({ recipientEmail: val }),
                            help: 'Separate multiple emails with a comma.'
                        }),
                        createElement(TextControl, {
                            label: 'Email Subject',
                            value: emailSubject,
                            onChange: (val) => setAttributes({ emailSubject: val })
                        }),
                        createElement(TextControl, {
                            label: 'Submit Button Text',
                            value: submitButtonText,
                            onChange: (val) => setAttributes({ submitButtonText: val })
                        })
                    ),
                    createElement(PanelBody, { title: 'After Submission' },
                        createElement(SelectControl, {
                            label: 'Action After Submission',
                            value: thankYouPageId,
                            options: pageOptions,
                            onChange: (val) => setAttributes({ thankYouPageId: parseInt(val, 10) })
                        }),
                        thankYouPageId === 0 && createElement(TextareaControl, {
                            label: 'Thank You Message',
                            value: thankYouMessage,
                            onChange: (val) => setAttributes({ thankYouMessage: val })
                        })
                    )
                ),
                createElement('div', blockProps,
                    !formName && createElement(Notice, { status: 'warning', isDismissible: false }, 'A form name is required. Please add one in the block settings.'),
                    !recipientEmail && createElement(Notice, { status: 'warning', isDismissible: false }, 'A recipient email is required. Please add one in the block settings.'),
                    createElement(InnerBlocks, { allowedBlocks: ALLOWED_BLOCKS, template: TEMPLATE }),
                    createElement('div', { className: 'form-submit' },
                        createElement('button', { type: 'submit', className: 'wp-block-button__link', disabled: true }, submitButtonText)
                    )
                )
            );
        },
        save: function () {
            return createElement(InnerBlocks.Content, null);
        },
    });

  

})(window.wp);