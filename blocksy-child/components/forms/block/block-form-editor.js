(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var { InnerBlocks, InspectorControls, useBlockProps } = wp.blockEditor;
    var { PanelBody, TextControl, ToggleControl, SelectControl, TextareaControl, Spinner, Notice, Button, Popover } = wp.components;
    var { createElement, Fragment, createContext, useContext, useState, useRef } = wp.element;
    var { useSelect, useDispatch } = wp.data;

    
    const FormApiContext = createContext({
        apiContactFields: [],
        apiContactFieldsLoading: false,
        apiContactFieldsError: '',
        integrationStatus: null,
        integrationStatusLoading: true
    });

    function createFormInspectorPanels(props, panelsConfig) {
        const { attributes, setAttributes } = props;

        const onLabelChange = (newLabel) => {
            setAttributes({ label: newLabel });
            if (attributes.isNameManuallySet === false) {
                const newName = newLabel.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-_]/g, '');
                setAttributes({ name: newName });
            }
        };

        const onNameChange = (newName) => {
            setAttributes({ name: newName.toLowerCase().replace(/[^a-z0-9-_]/g, ''), isNameManuallySet: true });
        };

        const controlMap = {
            text: TextControl,
            toggle: ToggleControl,
            select: SelectControl,
            textarea: TextareaControl,
        };

        return createElement(InspectorControls, null,
            panelsConfig.map(panel => {
                return createElement(PanelBody, { title: panel.title, initialOpen: panel.initialOpen !== false },
                    panel.fields.map(field => {
                        if (field.condition && !field.condition(attributes)) {
                            return null;
                        }

                        if (field.type === 'custom' && typeof field.render === 'function') {
                            return field.render();
                        }

                        const Component = controlMap[field.type];
                        if (!Component) return null;

                        let onChange = (val) => setAttributes({ [field.attribute]: val });
                        if (field.onChange === 'onLabelChange') {
                            onChange = onLabelChange;
                        } else if (field.onChange === 'onNameChange') {
                            onChange = onNameChange;
                        } else if (typeof field.props?.onChange === 'function') {
                            const originalOnChange = field.props.onChange;
                            onChange = (val) => originalOnChange(val, setAttributes);
                        }
                        
                        const componentProps = typeof field.props === 'function'
                            ? field.props(attributes, setAttributes)
                            : { ...field.props };

                        componentProps.key = field.attribute;
                        componentProps.label = field.label;
                        componentProps.onChange = onChange;

                        if (field.type === 'toggle') {
                            componentProps.checked = attributes[field.attribute];
                        } else {
                            componentProps.value = attributes[field.attribute];
                        }

                        return createElement(Component, componentProps);
                    })
                );
            })
        );
    }

    function useApiData(ajaxAction, enabled, dataExtractor, notFoundMessage) {
        const [data, setData] = wp.element.useState([]);
        const [isLoading, setIsLoading] = wp.element.useState(false);
        const [error, setError] = wp.element.useState('');

        wp.element.useEffect(() => {
            if (enabled) {
                setIsLoading(true);
                setError('');
                fetch(`${ajaxurl}?action=${ajaxAction}`, { credentials: 'same-origin' })
                    .then(res => res.json())
                    .then(response => {
                        
                        if (response.success) {
                            
                            const extractedData = dataExtractor(response.data);
                            
                            setData(extractedData);
                            if (extractedData.length === 0) {
                                setError(notFoundMessage);
                            }
                        } else {
                            setData([]);
                            setError(response.data || 'An error occurred while fetching data.');
                        }
                    })
                    .catch(() => {
                        setData([]);
                        setError('Failed to load data. Check browser console for details.');
                    })
                    .finally(() => setIsLoading(false));
            } else {
                setData([]);
                setError('');
            }
        }, [enabled, ajaxAction]);

        return { data, isLoading, error };
    }

    var FORM_NAME = 'hcdigital/form';

    function getMapToApiField(attributes, setAttributes, apiContactFields, apiContactFieldsLoading, apiContactFieldsError) {
        
        return {
            type: 'custom',
            render: () => {
                if (apiContactFieldsLoading) return createElement(Spinner, null);
                if (apiContactFieldsError) return createElement(Notice, { status: 'error', isDismissible: false }, apiContactFieldsError);
                return createElement(SelectControl, {
                    label: 'Map to API Field',
                    value: attributes.mapToApiField,
                    options: [
                        { label: 'None', value: '' },
                        ...apiContactFields.map(field => ({ label: field.displayName,  value: field.id }))
                    ],
                    onChange: (val) => setAttributes({ mapToApiField: val })
                });
            }
        };
    }

    function renderApiMappingField(mapToApiField, name) {
        if (!mapToApiField) {
            return null;
        }
        return createElement('input', {
            type: 'hidden',
            name: `f24_mapping[fields][${name}]`,
            value: mapToApiField
        });
    }

    function createFormFieldEdit({ specificPanelFields = [], renderPreview, defaultLabel }) {
        return function (props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps({ className: 'form-field' });

            const { enableApiIntegration, apiContactFields, apiContactFieldsLoading, apiContactFieldsError, integrationStatus, integrationStatusLoading } = useContext(FormApiContext);

            const panelsConfig = wp.element.useMemo(() => {
                const commonPanelFields = [
                    { type: 'text', attribute: 'label', label: 'Label', onChange: 'onLabelChange' },
                    { type: 'text', attribute: 'name', label: 'Field Name', props: { help: 'Unique name for this field.' }, onChange: 'onNameChange' },
                ];

                return [{
                    title: 'Field Settings',
                    fields: [
                        ...commonPanelFields,
                        ...specificPanelFields,
                        { type: 'toggle', attribute: 'isRequired', label: 'Required' },

                        ...(enableApiIntegration && !integrationStatusLoading && integrationStatus?.enabled ? [getMapToApiField(attributes, setAttributes, apiContactFields, apiContactFieldsLoading, apiContactFieldsError)] : [])
                    ]
                }];
            }, [enableApiIntegration, apiContactFields, apiContactFieldsLoading, apiContactFieldsError, attributes, integrationStatus, integrationStatusLoading]);


            return createElement(Fragment, null,
                createFormInspectorPanels(props, panelsConfig),
                createElement('div', blockProps,
                    createElement('label', null, attributes.label || defaultLabel),
                    renderPreview(attributes)
                )
            );
        };
    }

    const formFieldBlocks = [
        {
            name: 'hcdigital/form-input',
            title: 'Form: Text Input',
            icon: 'edit-page',
            attributes: {
                label: { type: 'string', default: '' },
                name: { type: 'string', default: '' },
                placeholder: { type: 'string', default: '' },
                type: { type: 'string', default: 'text' },
                isRequired: { type: 'boolean', default: false },
                isNameManuallySet: { type: 'boolean', default: false },
                isReplyTo: { type: 'boolean', default: false },
                mapToApiField: { type: 'string', default: '' },
            },
            edit: createFormFieldEdit({
                specificPanelFields: [
                    { type: 'select', attribute: 'type', label: 'Field Type', props: { options: [{ label: 'Text', value: 'text' }, { label: 'Email', value: 'email' }, { label: 'Tel', value: 'tel' }] } },
                    { type: 'text', attribute: 'placeholder', label: 'Placeholder' },
                ],
                renderPreview: (attributes) => {
                    const { label, placeholder } = attributes;
                    return createElement('input', { type: 'text', disabled: true, placeholder: placeholder, style: { width: '100%', padding: '8px' } });
                },
                defaultLabel: 'Input Field'
            }),
            save: function (props) {
                var { attributes } = props;
                var { label, name, placeholder, type, isRequired, isReplyTo, mapToApiField } = attributes;
                var inputProps = {
                    id: name,
                    name: name,
                    type: type,
                    placeholder: placeholder,
                    required: isRequired,
                    'data-is-reply-to': isReplyTo ? 'true' : undefined,
                    'data-f24-field-id': mapToApiField || undefined
                };

                return createElement('div', { className: 'form-field' },
                    createElement('label', { htmlFor: name }, label),
                    createElement('input', inputProps),
                    renderApiMappingField(mapToApiField, name)
                );
            }
        },
        {
            name: 'hcdigital/form-textarea',
            title: 'Form: Text Area',
            icon: 'edit-large',
            attributes: {
                label: { type: 'string', default: '' },
                name: { type: 'string', default: '' },
                placeholder: { type: 'string', default: '' },
                isRequired: { type: 'boolean', default: false },
                isNameManuallySet: { type: 'boolean', default: false },
                mapToApiField: { type: 'string', default: '' }
            },
            edit: createFormFieldEdit({
                specificPanelFields: [],
                renderPreview: (attributes) => {
                    const { label, placeholder } = attributes;
                    return createElement('textarea', { disabled: true, placeholder: placeholder, style: { width: '100%', padding: '8px' } });
                },
                defaultLabel: 'Textarea Field'
            }),
            save: function (props) {
                var { attributes } = props;
                var { label, name, placeholder, isRequired, mapToApiField } = attributes;
                return createElement('div', { className: 'form-field' },
                    createElement('label', { htmlFor: name }, label),
                    createElement('textarea', { id: name, name: name, placeholder: placeholder, required: isRequired, rows: 5, 'data-f24-field-id': mapToApiField || undefined }),
                    renderApiMappingField(mapToApiField, name)
                );
            }
        },
        {
            name: 'hcdigital/form-file',
            title: 'Form: File Upload',
            icon: 'upload',
            attributes: {
                label: { type: 'string', default: '' },
                name: { type: 'string', default: '' },
                isRequired: { type: 'boolean', default: false },
                isNameManuallySet: { type: 'boolean', default: false },
                
            },
            edit: createFormFieldEdit({
                specificPanelFields: [],
                renderPreview: (attributes) => {
                    const { label } = attributes;
                    return createElement('input', { type: 'file', disabled: true, style: { width: '100%', padding: '8px' } });
                },
                defaultLabel: 'File Upload'
            }),
            save: function (props) {
                var { attributes } = props;
                var { label, name, isRequired } = attributes;
                return createElement('div', { className: 'form-field' },
                    createElement('label', { htmlFor: name }, label),
                    createElement('input', { id: name, name: name, type: 'file', required: isRequired })
                );
            }
        },
        {
            name: 'hcdigital/form-multichoice',
            title: 'Form: Multiple Choice',
            icon: 'list-view',
            attributes: {
                label: { type: 'string', default: '' },
                name: { type: 'string', default: '' },
                isRequired: { type: 'boolean', default: false },
                isNameManuallySet: { type: 'boolean', default: false },
                options: { type: 'string', default: '' },
                fieldType: { type: 'string', default: 'select' },
                isMultiple: { type: 'boolean', default: false },
                mapToApiField: { type: 'string', default: '' }
            },
            edit: createFormFieldEdit({
                specificPanelFields: [
                    { type: 'textarea', attribute: 'options', label: 'Options', props: { help: 'Enter one option per line. Use "value|Label" for different values.' } },
                    {
                        type: 'select',
                        attribute: 'fieldType',
                        label: 'Field Type',
                        props: (setAttributes) => ({
                            options: [
                                { label: 'Dropdown', value: 'select' },
                                { label: 'Checkboxes', value: 'checkbox' }
                            ],
                            onChange: (val) => {
                                setAttributes({ fieldType: val });
                                if (val === 'select') {
                                    setAttributes({ isMultiple: false });
                                }
                            }
                        })
                    },
                    {
                        type: 'toggle',
                        attribute: 'isMultiple',
                        label: 'Allow Multiple',
                        condition: (attrs) => attrs.fieldType === 'checkbox'

                    },
                ],
                renderPreview: (attributes) => {
                    const { label, options, fieldType, isMultiple } = attributes;
                    var optionLines = options.split('\n').map(function (line, index) {
                        var labelText = line.split('|')[1] || line.split('|')[0];
                        var optionId = name + '-' + index;
                        if (fieldType === 'select') {
                            return createElement('option', { key: index, disabled: true }, labelText);
                        } else {
                            return createElement('label', { key: index, htmlFor: optionId, style: { display: 'block' } },
                                createElement('input', { id: optionId, type: isMultiple ? 'checkbox' : 'radio', disabled: true }),
                                ' ',
                                labelText
                            );
                        }
                    });

                    return createElement('div', null,
                        createElement('label', null, label || 'Multiple Choice'),
                        fieldType === 'select'
                            ? createElement('select', { disabled: true, multiple: isMultiple, style: { width: '100%', maxWidth: '100%', padding: '8px' } }, optionLines)
                            : createElement('div', null, optionLines)
                    );
                },
                defaultLabel: 'Multiple Choice'
            }),
            save: function (props) {
                var { attributes } = props;
                var { label, name, isRequired, options, fieldType, isMultiple, mapToApiField } = attributes;
                var optionLines = options.split('\n').filter(line => line.trim() !== '').map(function (line, idx) {
                    var parts = line.split('|');
                    var optionValue = parts.length > 1 ? parts[0].trim() : line.trim();
                    var optionLabel = parts.length > 1 ? parts[1].trim() : line.trim();
                    if (fieldType === 'select') {
                        return createElement('option', { key: optionValue, value: optionValue }, optionLabel);
                    } else {
                        return createElement('label', { key: optionValue, style: { display: 'block' } },
                            createElement('input', {
                                type: isMultiple ? 'checkbox' : 'radio',
                                name: name + (isMultiple ? '[]' : ''),
                                value: optionValue,
                                required: isRequired && !isMultiple
                            }),
                            ' ',
                            optionLabel
                        );
                    }
                });

                return createElement('div', { className: 'form-field' },
                    createElement('label', { htmlFor: name }, label),
                    fieldType === 'select'
                        ? createElement('select', { id: name, name: name + (isMultiple ? '[]' : ''), required: isRequired, multiple: isMultiple, 'data-f24-field-id': mapToApiField || undefined }, optionLines)
                        : optionLines,
                    renderApiMappingField(mapToApiField, name)
                );
            }
        }
    ];

    function registerFormFieldBlocks(blocks) {
        blocks.forEach(block => {
            registerBlockType(block.name, {
                title: block.title,
                //parent: [FORM_NAME],
                icon: block.icon,
                category: 'widgets',
                attributes: block.attributes,
                edit: block.edit,
                save: block.save
            });
        });
    }

    registerFormFieldBlocks(formFieldBlocks);

    var ALLOWED_BLOCKS = formFieldBlocks.map(block => block.name);

    var TEMPLATE = [
        [formFieldBlocks[0].name, { label: 'Name', name: 'name', type: 'text', isRequired: true, isNameManuallySet: true }],
        [formFieldBlocks[0].name, { label: 'Email', name: 'email', type: 'email', isRequired: true, isNameManuallySet: true }],
        [formFieldBlocks[1].name, { label: 'Message', name: 'message', isRequired: true, isNameManuallySet: true }]
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
            showOptinCheckbox: { type: 'boolean', default: false },
            optinCheckboxLabel: { type: 'string', default: 'Opt in to our newsletter' },
            isOptinRequired: { type: 'boolean', default: false },
            integrationStatus: { type: 'string', default: '' },
            enableApiIntegration: { type: 'boolean', default: false },
            apiMarketingListId: { type: 'string', default: '' }
        },
        edit: function (props) {
            var { attributes, setAttributes, clientId } = props;
            var { formName, recipientEmail, emailSubject, submitButtonText, thankYouPageId, thankYouMessage, formId, showOptinCheckbox, optinCheckboxLabel, enableApiIntegration, apiMarketingListId, isOptinRequired } = attributes;
            var blockProps = useBlockProps();
            const [isAppenderPopoverOpen, setAppenderPopoverOpen] = useState(false);
            const appenderAnchorRef = useRef();
            const { insertBlock } = useDispatch('core/block-editor');

            if (!formId) {
                setAttributes({ formId: 'hcdigital-form-' + clientId });
            }

            const { data: integrationStatus, isLoading: integrationStatusLoading } = useApiData(
                'hcdigital_get_f24_integration_status',
                true,
                (data) => data,
                ''
            );

            wp.element.useEffect(() => {
                if (integrationStatus && integrationStatus.enabled) {
                    setAttributes({ integrationStatus: 'enabled' });
                } else if (integrationStatus && integrationStatus.enabled === false) {
                    setAttributes({ integrationStatus: 'disabled' });
                }
            }, [integrationStatus]);

            var pages = useSelect(function (select) {
                return select('core').getEntityRecords('postType', 'page', { per_page: -1 });
            }, []);

            var pageOptions = !pages
                ? [{ value: '', label: 'Loading...' }]
                : [{ value: 0, label: 'Show a message on this page' }].concat(
                    pages.map(function (page) {
                        return { value: page.id, label: page.title.rendered };
                    })
                );

            const { data: apiLists, isLoading: apiListsLoading, error: apiListsError } = useApiData(
                'hcdigital_get_f24_marketing_lists',
                enableApiIntegration,
                (data) => data?.items || [],
                'No marketing lists found in your Force24 account.'
            );

            const { data: apiContactFields, isLoading: apiContactFieldsLoading, error: apiContactFieldsError } = useApiData(
                'hcdigital_get_f24_contact_fields',
                enableApiIntegration,
                (data) => data || [], 
                'No contact fields found in your Force24 account.'
            );

            const panelsConfig = [
                {
                    title: 'Form Settings',
                    fields: [
                        { type: 'text', attribute: 'formName', label: 'Form Name (Required)', props: { help: 'Name for this form for easier identification (e.g., for analytics).' } },
                        { type: 'text', attribute: 'recipientEmail', label: 'Recipient Email(s) (Required)', props: { help: 'Separate multiple emails with a comma.' } },
                        { type: 'text', attribute: 'emailSubject', label: 'Email Subject' },
                        { type: 'text', attribute: 'submitButtonText', label: 'Submit Button Text' },
                        { type: 'toggle', attribute: 'showOptinCheckbox', label: 'Show Opt-in Checkbox' },
                        {
                            type: 'text',
                            attribute: 'optinCheckboxLabel',
                            label: 'Opt-in Checkbox Label',
                            condition: (attrs) => attrs.showOptinCheckbox
                        },
                        {
                            type: 'toggle',
                            attribute: 'isOptinRequired',
                            label: 'Opt-in Required',
                            condition: (attrs) => attrs.showOptinCheckbox
                        },
                       
                        { 
                            type: 'toggle', 
                            attribute: 'enableApiIntegration', 
                            label: 'Enable API Integration',
                            condition: () => !integrationStatusLoading && integrationStatus?.enabled
                        },
                        {
                            type: 'custom',
                            condition: (attrs) => attrs.enableApiIntegration && !integrationStatusLoading && integrationStatus?.enabled,
                            render: () => {
                                if (apiListsLoading) return createElement(Spinner, null);
                                if (apiListsError) return createElement(Notice, { status: 'error', isDismissible: false }, apiListsError);
                                return createElement(SelectControl, {
                                    label: 'API Marketing List',
                                    value: apiMarketingListId,
                                    options: [
                                        { label: '-- Select a list --', value: '' },
                                        ...apiLists.map(list => ({ label: list.name, value: list.id }))
                                    ],
                                    onChange: (val) => setAttributes({ apiMarketingListId: val })
                                });
                            }
                        }
                    ]
                },
                {
                    title: 'After Submission',
                    fields: [
                        {
                            type: 'select',
                            attribute: 'thankYouPageId',
                            label: 'Action After Submission',
                            props: {
                                options: pageOptions,
                                onChange: (val) => setAttributes({ thankYouPageId: parseInt(val, 10) })
                            }
                        },
                        {
                            type: 'textarea',
                            attribute: 'thankYouMessage',
                            label: 'Thank You Message',
                            condition: (attrs) => attrs.thankYouPageId === 0
                        }
                    ]
                }
            ];

            return createElement(Fragment, null,
                createFormInspectorPanels(props, panelsConfig),
                createElement('div', blockProps,
                    !formName && createElement(Notice, { status: 'warning', isDismissible: false }, 'A form name is required. Please add one in the block settings.'),
                    !recipientEmail && createElement(Notice, { status: 'warning', isDismissible: false }, 'A recipient email is required. Please add one in the block settings.'),
                    createElement(FormApiContext.Provider, {
                        value: { enableApiIntegration, apiContactFields, apiContactFieldsLoading, apiContactFieldsError, integrationStatus, integrationStatusLoading }
                    },
                        createElement(InnerBlocks, {
                            //allowedBlocks: ALLOWED_BLOCKS,
                            template: TEMPLATE,
                            templateLock: false,
                            renderAppender: false,
                            
                        })
                    ),
                    createElement('div', { style: { display: 'flex', justifyContent: 'center', marginTop: '1em' } },
                        createElement(Button, {
                            isPrimary: true,
                            onClick: () => setAppenderPopoverOpen(true),
                            ref: appenderAnchorRef
                        }, 'Add Form Field')
                    ),
                    isAppenderPopoverOpen && createElement(Popover, {
                        anchor: appenderAnchorRef.current,
                        onClose: () => setAppenderPopoverOpen(false),
                        position: 'top center',
                        focusOnMount: 'container',
                        offset: 10
                    },
                        createElement('div', { style: { padding: '16px', minWidth: '200px' } },
                            createElement('ul', { style: { listStyle: 'none', padding: 0, margin: 0 } },
                                formFieldBlocks.map(fieldBlock =>
                                    createElement('li', { key: fieldBlock.name, style: { marginBottom: '0.5em' } },
                                        createElement(Button, {
                                            isSecondary: true,
                                            style: { width: '100%', justifyContent: 'center' },
                                            onClick: () => {
                                                const newBlock = wp.blocks.createBlock(fieldBlock.name);
                                                insertBlock(newBlock, undefined, clientId);
                                                setAppenderPopoverOpen(false);
                                            }
                                        }, fieldBlock.title)
                                    )
                                )
                            )
                        )
                    ),
                    showOptinCheckbox && createElement('div', { className: 'form-field form-optin-checkbox' },
                        createElement('label', null,
                            createElement('input', { type: 'checkbox', disabled: true, required: isOptinRequired }),
                            ' ',
                            optinCheckboxLabel
                        )
                    ),
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