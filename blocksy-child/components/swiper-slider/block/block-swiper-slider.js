(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, RangeControl, SelectControl, Placeholder, TabPanel, Button, ButtonGroup, __experimentalNumberControl: NumberControl, TextareaControl, Icon, Popover, ColorPalette } = wp.components;
    const { createElement, Fragment, useState, useRef } = wp.element;
    const { withSelect } = wp.data;
    registerBlockType('hcdigital/slide', {
        title: 'Slide',
        icon: 'index-card',
        category: 'media',
        parent: ['hcdigital/swiper-slider'],
        attributes: {
            noSwiping: {
                type: 'boolean',
                default: false,
            },
            videoControls: {
                type: 'boolean',
                default: false,
            },
            customSlideDelay: {
                type: 'boolean',
                default: false,
            },
            slideDelay: {
                type: 'number',
                default: 3000,
            },
        },
        supports: {
            html: false,
            reusable: false,
            inserter: true,
        },
        edit: function (props) {
            const { attributes, setAttributes } = props;
            return createElement(
                Fragment,
                null,
                createElement(InspectorControls, null,
                    createElement(PanelBody, { title: 'Slide Options', initialOpen: true },
                        createElement(ToggleControl, {
                            label: 'No swiping',
                            checked: attributes.noSwiping,
                            onChange: (value) => setAttributes({ noSwiping: value }),
                        }),
                        createElement(ToggleControl, {
                            label: 'Video controls',
                            checked: attributes.videoControls,
                            onChange: (value) => setAttributes({ videoControls: value }),
                        }),
                        createElement(ToggleControl, {
                            label: 'Custom slide delay',
                            checked: attributes.customSlideDelay,
                            onChange: (value) => setAttributes({ customSlideDelay: value }),
                        }),
                        attributes.customSlideDelay && createElement(RangeControl, {
                            label: 'Slide delay (ms)',
                            value: attributes.slideDelay ?? '',
                            onChange: (value) => setAttributes({ slideDelay: value }),
                            min: 500,
                            max: 10000,
                            step: 100,
                            allowReset: true,
                            resetFallbackValue: 3000,
                        })
                    )
                ),
                createElement(
                    'div',
                    {
                        className: 'swiper-slide',
                        style: { borderBottom: '2px dashed #ccc', paddingBottom: '1em', marginBottom: '1em' }
                    },
                    createElement(InnerBlocks, {
                        templateLock: false,
                        renderAppender: InnerBlocks.ButtonBlockAppender,
                    })
                )
            );
        },
        save: function (props) {
            const { attributes } = props;
            return createElement(
                'div',
                {
                    className: `swiper-slide ${attributes.noSwiping ? 'swiper-no-swiping' : ''}`,
                    'data-videocontrols': attributes.videoControls ? 'true' : 'false',
                    'data-customslidedelay': attributes.customSlideDelay ? 'true' : 'false',
                    'data-swiper-autoplay': attributes.customSlideDelay ? attributes.slideDelay : ''
                },
                createElement(InnerBlocks.Content, null)
            );
        },
    });

    // const TEMPLATE = [
    //     ['hcdigital/slide', {}, [
    //         ['core/paragraph', { placeholder: 'Enter slide content...' }]
    //     ]],
    //     ['hcdigital/slide', {}, [
    //         ['core/paragraph', { placeholder: 'Enter slide content...' }]
    //     ]]
    // ];

    // let patternTemplate = TEMPLATE;

    registerBlockType('hcdigital/swiper-slider', {
        title: 'Slider',
        icon: 'slides',
        category: 'widgets',
        attributes: {
            vertical: {
                type: 'boolean',
                default: false,
            },
            useGutenbergEditor: {
                type: 'boolean',
                default: true,
            },
            dataSource: {
                type: 'string',
                default: 'latest',
            },
            selectedPosts: {
                type: 'array',
                default: [],
            },
            selectedPostType: {
                type: 'string',
                default: 'post',
            },
            metaQuery: {
                type: 'string',
                default: '',
            },
            taxQuery: {
                type: 'string',
                default: '',
            },
            slidesPerView: {
                type: 'object',
                default: {
                    desktop: 1,
                    tablet: 1,
                    mobile: 1,
                },
            },
            slidesPerGroupOn: {
                type: 'boolean',
                default: false,
            },
            slidesPerGroup: {
                type: 'object',
                default: {
                    desktop: 1,
                    tablet: 1,
                    mobile: 1,
                },
            },
            spaceBetween: {
                type: 'object',
                default: {
                    desktop: 20,
                    tablet: 20,
                    mobile: 20,
                },
            },
            overflow: {
                type: 'object',
                default: {
                    desktop: false,
                    tablet: false,
                    mobile: false,
                },
            },
            outOfRangeSlidesOpacity: {
                type: 'object',
                default: {
                    desktop: 1,
                    tablet: 1,
                    mobile: 1,
                },
            },
            loop: {
                type: 'boolean',
                default: true,
            },
            centeredSlides: {
                type: 'boolean',
                default: false,
            },
            pagination: {
                type: 'object',
                default: {
                    desktop: true,
                    tablet: true,
                    mobile: true,
                },
            },
            paginationBulletSize: {
                type: 'object',
                default: {
                    desktop: 8,
                    tablet: 8,
                    mobile: 8,
                },
            },
            paginationActiveBulletSize: {
                type: 'object',
                default: {
                    desktop: 24,
                    tablet: 24,
                    mobile: 24,
                },
            },
            navigation: {
                type: 'object',
                default: {
                    desktop: true,
                    tablet: true,
                    mobile: true,
                },
            },
            navigationContentContainer: {
                type: 'object',
                default: {
                    desktop: false,
                    tablet: false,
                    mobile: false,
                },
            },
            navigationButtonsHorizontalOffset: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            navigationAlign: {
                type: 'object',
                default: {
                    desktop: 'center',
                    tablet: 'center',
                    mobile: 'center',
                },
            },
            navigationButtonOffset: {
                type: 'object',
                default: {
                    desktop: 24,
                    tablet: 24,
                    mobile: 24,
                },
            },
            navigationJustify: {
                type: 'object',
                default: {
                    desktop: 'sides',
                    tablet: 'sides',
                    mobile: 'sides',
                },
            },
            navigationButtonSize: {
                type: 'object',
                default: {
                    desktop: 40,
                    tablet: 40,
                    mobile: 40,
                },
            },
            // Pagination justify (left, center, right, sides)
            paginationJustify: {
                type: 'object',
                default: {
                    desktop: 'center',
                    tablet: 'center',
                    mobile: 'center',
                },
            },
            navigationArrowSize: {
                type: 'object',
                default: {
                    desktop: 20,
                    tablet: 20,
                    mobile: 20,
                },
            },
            navigationButtonBorderWidth: {
                type: 'number',
                default: 0,
            },
            navigationButtonBorderColor: {
                type: 'string',
                default: '',
            },
            navigationArrowBorderRadius: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            navigationArrowsPosition: {
                type: 'object',
                default: {
                    desktop: 'inside',
                    tablet: 'inside',
                    mobile: 'inside',
                },
            },
            navigationArrowsSpacing: {
                type: 'object',
                default: {
                    desktop: 12,
                    tablet: 12,
                    mobile: 12,
                },
            },
            dynamicBullets: {
                type: 'object',
                default: {
                    desktop: false,
                    tablet: false,
                    mobile: false,
                },
            },
            dynamicMainBullets: {
                type: 'object',
                default: {
                    desktop: 1,
                    tablet: 1,
                    mobile: 1,
                },
            },
            selectedContentBlockId: {
                type: 'string',
                default: '',
            },
            postsToShow: {
                type: 'number',
                default: 12,
            },
            effect: {
                type: 'string',
                default: 'slide',
            },
            autoplay: {
                type: 'boolean',
                default: true,
            },
            autoscroll: {
                type: 'boolean',
                default: false,
            },
            speed: {
                type: 'number',
                default: 5000,
            },
            paddingLeft: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            paddingRight: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            paddingTop: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            paddingBottom: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            breakpoints: {
                type: 'object',
                default: {
                    tablet: 690,
                    desktop: 1000,
                },
            },
            paginationOffset: {
                type: 'object',
                default: {
                    desktop: 0,
                    tablet: 0,
                    mobile: 0,
                },
            },
            paginationAttachedToArrows: {
                type: 'object',
                default: {
                    desktop: false,
                    tablet: false,
                    mobile: false,
                },
            },
            scrollbar: {
                type: 'boolean',
                default: false,
            },
            scrollbarPosition: {
                type: 'string',
                default: 'bottom',
            },
            scrollbarColor: {
                type: 'string',
                default: 'rgba(0, 0, 0, 0.1)',
            },
            scrollbarDragColor: {
                type: 'string',
                default: 'rgba(0, 0, 0, 0.25)',
            },
            paginationBulletColor: {
                type: 'string',
                default: '#ccc',
            },
            paginationActiveBulletColor: {
                type: 'string',
                default: '#000',
            },
            navigationArrowColor: {
                type: 'string',
                default: '',
            },
            navigationArrowBgColor: {
                type: 'string',
                default: '',
            },
            customNavigation: {
                type: 'boolean',
                default: false,
            },
            customNavigationSVG: {
                type: 'string',
                default: '',
            },
            equaliseColumns: {
                type: 'boolean',
                default: true,
            },
            customRenderBullet: {
                type: 'boolean',
                default: false,
            },
            customRenderBulletHtml: {
                type: 'string',
                default: '',
            },
        },
        edit: withSelect((select, ownProps) => {
            const { getPostTypes, getEntityRecords } = select('core');
            const { attributes } = ownProps;
            const { useGutenbergEditor, selectedPostType, selectedPosts } = attributes;
            const postTypes = getPostTypes({ per_page: -1 });
            const excluded = ['attachment', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation', 'ct_content_block'];
            const filteredPostTypes = postTypes ? postTypes.filter(pt => pt.viewable && !excluded.includes(pt.slug)) : [];
            const allPosts = !useGutenbergEditor ? (getEntityRecords('postType', selectedPostType, { per_page: -1, orderby: 'title', order: 'asc' }) || []) : [];
            let orderedSelectedPosts = [];
            if (allPosts.length > 0 && selectedPosts.length > 0) {
                const postsById = allPosts.reduce((acc, post) => {
                    acc[post.id] = post;
                    return acc;
                }, {});
                orderedSelectedPosts = selectedPosts.map(id => postsById[id]).filter(Boolean); 
            }
            const contentBlocks = getEntityRecords('postType', 'ct_content_block', { per_page: -1 }) || [];
            const filteredContentBlocks = contentBlocks.filter(
                block => !block.blocksy_meta || !block.blocksy_meta.template_subtype
            );
            
            //const registeredPatterns = select('core')?.getBlockPatterns?.() || [];
            const userPatterns = getEntityRecords('postType', 'wp_block', { per_page: -1 }) || [];

            const allPatterns = [
                //...registeredPatterns.map(p => ({ title: p.title, name: p.name, source: 'registered', blocks: p.blocks })),
                ...userPatterns.map(p => ({ title: p.title.raw, name: p.id, source: 'user', content: p.content.raw }))
            ];

            return {
                allPostsForSuggestions: allPosts,
                orderedSelectedPosts: orderedSelectedPosts,
                postTypes: filteredPostTypes.map(pt => ({
                    label: pt.name && typeof pt.name === 'string'
                        ? (new window.DOMParser()).parseFromString(pt.name, 'text/html').body.textContent
                        : pt.name,
                    value: pt.slug
                })),
                filteredContentBlocks: filteredContentBlocks.map(block => ({
                    label: block.title && block.title.rendered
                        ? (new window.DOMParser()).parseFromString(block.title.rendered, 'text/html').body.textContent
                        : `ID ${block.id}`,
                    value: block.id
                })),
                patterns: allPatterns
            };
        })(function (props) {
            const { attributes, setAttributes, postTypes, filteredContentBlocks, allPostsForSuggestions, orderedSelectedPosts, patterns } = props;
            const { useGutenbergEditor, selectedPostType, metaQuery, taxQuery, slidesPerView, spaceBetween, overflow, outOfRangeSlidesOpacity, loop, pagination, paginationBulletSize, paginationActiveBulletSize, navigation, navigationContentContainer, navigationButtonsHorizontalOffset, navigationAlign, navigationButtonOffset, navigationJustify, navigationButtonSize, navigationArrowSize, navigationButtonBorderWidth, navigationButtonBorderColor, navigationArrowBorderRadius, navigationArrowsPosition, navigationArrowsSpacing, dynamicBullets, dynamicMainBullets, selectedContentBlockId, postsToShow, effect, autoplay, autoscroll, speed, breakpoints, centeredSlides, paddingLeft, paddingRight, paddingTop, paddingBottom, paginationAttachedToArrows, scrollbar, dataSource, selectedPosts, slidesPerGroupOn, slidesPerGroup, customNavigation, customNavigationSVG, scrollbarPosition, scrollbarColor, scrollbarDragColor, paginationBulletColor, paginationActiveBulletColor, navigationArrowColor, navigationArrowBgColor } = attributes;
            const [deviceType, setDeviceType] = useState('desktop');
            const { colors } = wp.data.useSelect((select) => {
                const settings = select('core/block-editor').getSettings();
                return {
                    colors: settings.colors,
                };
            }, []);
            const [postToAdd, setPostToAdd] = useState('');
            const [isPatternPopoverOpen, setPatternPopoverOpen] = useState(false);
            const anchorRef = useRef();
            const movePost = (index, direction) => {
                const newSelectedPosts = [...selectedPosts];
                const item = newSelectedPosts[index];
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= newSelectedPosts.length) {
                    return;
                }
                newSelectedPosts.splice(index, 1);
                newSelectedPosts.splice(newIndex, 0, item);
                setAttributes({ selectedPosts: newSelectedPosts });
            };
            const removePost = (index) => {
                const newSelectedPosts = [...selectedPosts];
                newSelectedPosts.splice(index, 1);
                setAttributes({ selectedPosts: newSelectedPosts });
            };
            const addPost = () => {
                const postId = parseInt(postToAdd, 10);
                if (postId && !selectedPosts.includes(postId)) {
                    setAttributes({ selectedPosts: [...selectedPosts, postId] });
                    setPostToAdd('');
                }
            };
            const availablePostsForSelect = allPostsForSuggestions
                .filter(p => !selectedPosts.includes(p.id))
                .map(p => ({
                    label: (new window.DOMParser()).parseFromString(p.title.rendered, 'text/html').body.textContent || `ID: ${p.id}`,
                    value: p.id
                }));
            const listStyle = { listStyle: 'none', marginTop: '1em', padding: 0, border: '1px solid #ddd', borderRadius: '2px' };
            const itemStyle = { display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '8px 12px', borderBottom: '1px solid #eee' };
            const titleStyle = { flexGrow: 1, marginRight: '1em', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' };
            const controlsStyle = { display: 'flex', flexShrink: 0, gap: '4px' };

            const decodeTitle = (title) => {
                if (!title) return '';
                try {
                    return (new window.DOMParser()).parseFromString(title, 'text/html').body.textContent;
                } catch (e) {
                    return title;
                }
            };

            const openPatternModal = () => setPatternPopoverOpen(true);

            const insertPattern = (pattern) => {
                let newBlocks;
                if (pattern.source === 'user') {
                    newBlocks = wp.blocks.parse(pattern.content);
                } else { // registered
                    newBlocks = wp.blocks.createBlocksFromInnerBlocksTemplate(pattern.blocks);
                }

                const hasSlideBlock = newBlocks.some(block => block.name === 'hcdigital/slide');

                if (hasSlideBlock) {
                    // If the pattern already contains slide blocks, just insert them.
                    wp.data.dispatch('core/block-editor').insertBlocks(newBlocks, undefined, props.clientId);
                } else {
                    // If not, wrap the content in a new slide block.
                    const newSlide = wp.blocks.createBlock('hcdigital/slide', {}, newBlocks);
                    wp.data.dispatch('core/block-editor').insertBlocks(newSlide, undefined, props.clientId);
                }
            };

            const addBlankSlide = () => {
                const newSlide = wp.blocks.createBlock('hcdigital/slide', {});
                wp.data.dispatch('core/block-editor').insertBlocks(newSlide, undefined, props.clientId);
                setPatternPopoverOpen(false);
            };
            return createElement(
                Fragment,
                null,
                createElement(
                    InspectorControls,
                    null,
                    createElement(Fragment, null,
                        createElement(
                            PanelBody, { title: 'Source Settings', initialOpen: true },
                            createElement(ToggleControl, {
                                label: 'Use Gutenberg Editor',
                                help: useGutenbergEditor ? 'Manually add slides in the editor.' : 'Automatically generate slides from a post type.',
                                checked: useGutenbergEditor,
                                onChange: (value) => setAttributes({ useGutenbergEditor: value }),
                            }),
                            !useGutenbergEditor && createElement(Fragment, null,
                                createElement(SelectControl, {
                                    label: 'Select Post Type',
                                    value: selectedPostType,
                                    options: postTypes,
                                    onChange: (value) => setAttributes({ selectedPostType: value, selectedPosts: [] }),
                                }),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, { isPrimary: dataSource === 'latest', isSecondary: dataSource !== 'latest', onClick: () => setAttributes({ dataSource: 'latest' }), style: { flex: 1, justifyContent: 'center' } }, 'Latest Posts'),
                                    createElement(Button, { isPrimary: dataSource === 'specific', isSecondary: dataSource !== 'specific', onClick: () => setAttributes({ dataSource: 'specific' }), style: { flex: 1, justifyContent: 'center' } }, 'Specific Posts')
                                ),
                                dataSource === 'latest' && createElement(Fragment, null,
                                    createElement(TextareaControl, {
                                        label: 'Meta Query',
                                        value: metaQuery,
                                        onChange: (value) => setAttributes({ metaQuery: value }),
                                        help: 'Enter meta query in format: key=value&key2=value2'
                                    }),
                                    createElement(TextareaControl, {
                                        label: 'Taxonomy Query',
                                        value: taxQuery,
                                        onChange: (value) => setAttributes({ taxQuery: value }),
                                        help: 'Enter taxonomy query in format: taxonomy=term_slug&taxonomy2=term_slug2'
                                    }),
                                    createElement(RangeControl, {
                                        label: 'Number of Posts',
                                        value: postsToShow,
                                        onChange: (value) => setAttributes({ postsToShow: value }),
                                        min: 1,
                                        max: 99,
                                    })
                                ),
                                dataSource === 'specific' && createElement(
                                    'div',
                                    { style: { marginTop: '1em' } },
                                    createElement('p', { style: { marginBottom: '0.5em' } }, 'Manage & Order Posts'),
                                    createElement(
                                        'div',
                                        { style: { display: 'flex', gap: '8px', alignItems: 'flex-start' } },
                                        createElement(SelectControl, {
                                            label: 'Add a post',
                                            hideLabelFromVision: true,
                                            value: postToAdd,
                                            options: [{ label: 'Select a post to add...', value: '' }, ...availablePostsForSelect],
                                            onChange: (value) => setPostToAdd(value),
                                            style: { flex: 1, margin: 0 }
                                        }),
                                        createElement(Button, { isPrimary: true, onClick: addPost, disabled: !postToAdd }, 'Add')
                                    ),
                                    orderedSelectedPosts.length > 0 ?
                                        createElement(
                                            'ul',
                                            { style: listStyle },
                                            orderedSelectedPosts.map((post, index) =>
                                                createElement(
                                                    'li',
                                                    { key: post.id, style: { ...itemStyle, ...(index === orderedSelectedPosts.length - 1 && { borderBottom: 'none' }) } },
                                                    createElement('span', { style: titleStyle, title: decodeTitle(post.title.rendered) }, decodeTitle(post.title.rendered) || `ID: ${post.id}`),
                                                    createElement(
                                                        'div',
                                                        { style: controlsStyle },
                                                        createElement(Button, {
                                                            icon: 'arrow-up-alt2',
                                                            label: 'Move up',
                                                            onClick: () => movePost(index, -1),
                                                            disabled: index === 0,
                                                            isSmall: true
                                                        }),
                                                        createElement(Button, {
                                                            icon: 'arrow-down-alt2',
                                                            label: 'Move down',
                                                            onClick: () => movePost(index, 1),
                                                            disabled: index === orderedSelectedPosts.length - 1,
                                                            isSmall: true
                                                        }),
                                                        createElement(Button, {
                                                            icon: 'trash',
                                                            label: 'Remove',
                                                            onClick: () => removePost(index),
                                                            isDestructive: true,
                                                            isSmall: true
                                                        })
                                                    )
                                                )
                                            )
                                        ) :
                                        createElement('p', { style: { fontStyle: 'italic', color: '#555', marginTop: '1em' } }, 'No posts selected yet.')
                                ),
                                createElement(SelectControl, {
                                    label: 'Render card',
                                    value: selectedContentBlockId || '',
                                    options: [{ label: 'Based on customizer', value: '' }, ...filteredContentBlocks],
                                    onChange: (value) => setAttributes({ selectedContentBlockId: value }),
                                    style: {}
                                })
                            )
                        ),
                        createElement(
                            PanelBody,
                            { title: 'Slider Settings', initialOpen: true },
                            createElement(SelectControl, {
                                label: 'Effect',
                                value: effect,
                                options: [
                                    { label: 'Slide', value: 'slide' },
                                    { label: 'Fade', value: 'fade' },
                                    { label: 'Cube', value: 'cube' },
                                    { label: 'Coverflow', value: 'coverflow' },
                                    { label: 'Flip', value: 'flip' },
                                    { label: 'Creative', value: 'creative' },
                                    { label: 'Cards', value: 'cards' },
                                ],
                                onChange: (value) => setAttributes({ effect: value }),
                            }),
                            createElement(ToggleControl, {
                                label: 'Infinite Scrolling (Loop)',
                                checked: loop,
                                onChange: (value) => setAttributes({ loop: value }),
                            }),
                            createElement(ToggleControl, {
                                label: 'Centered Slides',
                                help: 'If true, then active slide will be centered, not always on the left side.',
                                checked: centeredSlides,
                                onChange: (value) => setAttributes({ centeredSlides: value }),
                            }),
                            createElement(ToggleControl, {
                                label: 'Autoplay',
                                checked: autoplay,
                                onChange: (value) => setAttributes({ autoplay: value }),
                            }),
                            autoplay && createElement(RangeControl, {
                                label: 'Speed (ms)',
                                value: speed,
                                onChange: (value) => setAttributes({ speed: value }),
                                min: 500,
                                max: 10000,
                                step: 100,
                                allowReset: true,
                                resetFallbackValue: 5000,
                            }),
                            autoplay && createElement(ToggleControl, {
                                label: 'Auto Scroll',
                                checked: autoscroll,
                                onChange: (value) => setAttributes({ autoscroll: value }),
                            }),
                            createElement(NumberControl, {
                                label: 'Tablet Breakpoint (px)',
                                value: breakpoints.tablet,
                                onChange: (value) => setAttributes({ breakpoints: { ...breakpoints, tablet: parseInt(value, 10) || 0 } }),
                                min: 320,
                            }),
                            createElement(NumberControl, {
                                label: 'Desktop Breakpoint (px)',
                                value: breakpoints.desktop,
                                onChange: (value) => setAttributes({ breakpoints: { ...breakpoints, desktop: parseInt(value, 10) || 0 } }),
                                min: 690,
                                style: { marginBottom: '1em' }
                            }),
                            createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                createElement(Button, { isPrimary: deviceType === 'desktop', isSecondary: deviceType !== 'desktop', onClick: () => setDeviceType('desktop'), style: { flex: 1, justifyContent: 'center' } }, 'Desktop'),
                                createElement(Button, { isPrimary: deviceType === 'tablet', isSecondary: deviceType !== 'tablet', onClick: () => setDeviceType('tablet'), style: { flex: 1, justifyContent: 'center' } }, 'Tablet'),
                                createElement(Button, { isPrimary: deviceType === 'mobile', isSecondary: deviceType !== 'mobile', onClick: () => setDeviceType('mobile'), style: { flex: 1, justifyContent: 'center' } }, 'Mobile')
                            ),

                            createElement(RangeControl, {
                                label: 'Slides Per View',
                                value: slidesPerView[deviceType],
                                onChange: (value) => setAttributes({ slidesPerView: { ...slidesPerView, [deviceType]: value } }),
                                min: 1,
                                max: 12,
                                step: 0.1,
                                allowReset: true,
                                resetFallbackValue: 1,
                            }),
                            createElement(ToggleControl, {
                                label: 'Set Slides Per Group',
                                checked: slidesPerGroupOn,
                                onChange: (value) => setAttributes({ slidesPerGroupOn: value }),
                            }),
                            slidesPerGroupOn && createElement(RangeControl, {
                                label: 'Slides Per Group',
                                value: slidesPerGroup[deviceType],
                                onChange: (value) => setAttributes({ slidesPerGroup: { ...slidesPerGroup, [deviceType]: value } }),
                                min: 1,
                                max: 12,
                                step: 1,
                                allowReset: true,
                                resetFallbackValue: 1,
                            }),
                            createElement(RangeControl, {
                                label: 'Space between slides (px)',
                                value: spaceBetween[deviceType],
                                onChange: (value) => setAttributes({ spaceBetween: { ...spaceBetween, [deviceType]: value } }),
                                min: 0,
                                max: 100,
                                allowReset: true,
                                resetFallbackValue: 20,
                            }),
                            createElement(ToggleControl, {
                                label: 'Overflow Visible',
                                checked: overflow[deviceType],
                                onChange: (value) => setAttributes({ overflow: { ...overflow, [deviceType]: value } }),
                            }),
                            overflow[deviceType] && createElement(RangeControl, {
                                label: 'Out of range slides opacity',
                                value: outOfRangeSlidesOpacity[deviceType] ?? 1,
                                onChange: (value) => setAttributes({ outOfRangeSlidesOpacity: { ...(outOfRangeSlidesOpacity || {}), [deviceType]: value } }),
                                min: 0,
                                max: 1,
                                step: 0.01,
                                allowReset: true,
                                resetFallbackValue: 1
                            }),
                            createElement(ToggleControl, {
                                label: 'Show navigation arrows',
                                checked: navigation[deviceType],
                                onChange: (value) => {
                                    setAttributes({
                                        navigation: { ...navigation, [deviceType]: value },
                                        paginationAttachedToArrows: {
                                            ...paginationAttachedToArrows,
                                            [deviceType]: value ? paginationAttachedToArrows[deviceType] : false
                                        }
                                    });
                                },
                            }),
                            createElement(ToggleControl, {
                                label: 'Show pagination',
                                checked: pagination[deviceType],
                                onChange: (value) => setAttributes({ pagination: { ...pagination, [deviceType]: value } }),
                            }),
                            createElement(ToggleControl, {
                                label: 'Show scrollbar',
                                checked: attributes.scrollbar ?? false,
                                onChange: (value) => setAttributes({ scrollbar: value }),
                            }),
                            createElement(ToggleControl, {
                                label: 'Equalise columns',
                                checked: attributes.equaliseColumns,
                                onChange: (value) => setAttributes({ equaliseColumns: value }),
                            }),
                        ),
                        navigation[deviceType] && createElement(
                            PanelBody, { title: 'Navigation Settings', initialOpen: true },
                            createElement(
                                Fragment,
                                null,
                                createElement('p', { style: { marginTop: '1em', marginBottom: '0.5em' } }, 'Arrows Align'),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, {
                                        isPrimary: navigationAlign[deviceType] === 'top',
                                        isSecondary: navigationAlign[deviceType] !== 'top',
                                        onClick: () => setAttributes({ navigationAlign: { ...navigationAlign, [deviceType]: 'top' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('rect', { width: "4", height: "12", rx: "1", transform: "matrix(1 0 0 -1 6 15)" }),
                                                createElement('path', { d: "M1.5 2a.5.5 0 0 1 0-1zm13-1a.5.5 0 0 1 0 1zm-13 0h13v1h-13z" })
                                            )
                                        })
                                    ),
                                    createElement(Button, {
                                        isPrimary: navigationAlign[deviceType] === 'center',
                                        isSecondary: navigationAlign[deviceType] !== 'center',
                                        onClick: () => setAttributes({ navigationAlign: { ...navigationAlign, [deviceType]: 'center' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('path', { d: "M6 13a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zM1 8a.5.5 0 0 0 .5.5H6v-1H1.5A.5.5 0 0 0 1 8m14 0a.5.5 0 0 1-.5.5H10v-1h4.5a.5.5 0 0 1 .5.5" })
                                            )
                                        })
                                    ),
                                    createElement(Button, {
                                        isPrimary: navigationAlign[deviceType] === 'bottom',
                                        isSecondary: navigationAlign[deviceType] !== 'bottom',
                                        onClick: () => setAttributes({ navigationAlign: { ...navigationAlign, [deviceType]: 'bottom' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('rect', { width: "4", height: "12", x: "6", y: "1", rx: "1" }),
                                                createElement('path', { d: "M1.5 14a.5.5 0 0 0 0 1zm13 1a.5.5 0 0 0 0-1zm-13 0h13v-1h-13z" })
                                            )
                                        })
                                    )
                                ),
                                createElement('p', { style: { marginTop: '1em', marginBottom: '0.5em' } }, 'Arrows Justify'),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, {
                                        isPrimary: navigationJustify[deviceType] === 'sides',
                                        isSecondary: navigationJustify[deviceType] !== 'sides',
                                        onClick: () => setAttributes({ navigationJustify: { ...navigationJustify, [deviceType]: 'sides' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('path', { fillRule: "evenodd", d: "M14.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5m-13 0a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5" }),
                                                createElement('path', { d: "M6 13a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z" })
                                            )
                                        })
                                    ),
                                    createElement(Button, {
                                        isPrimary: navigationJustify[deviceType] === 'left',
                                        isSecondary: navigationJustify[deviceType] !== 'left',
                                        onClick: () => setAttributes({ navigationJustify: { ...navigationJustify, [deviceType]: 'left' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('path', { fillRule: "evenodd", d: "M1.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13a.5.5 0 0 1 .5-.5" }),
                                                createElement('path', { d: "M3 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z" })
                                            )
                                        })
                                    ),
                                    createElement(Button, {
                                        isPrimary: navigationJustify[deviceType] === 'center',
                                        isSecondary: navigationJustify[deviceType] !== 'center',
                                        onClick: () => setAttributes({ navigationJustify: { ...navigationJustify, [deviceType]: 'center' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('path', { d: "M8 1a.5.5 0 0 1 .5.5V6h-1V1.5A.5.5 0 0 1 8 1m0 14a.5.5 0 0 1-.5-.5V10h1v4.5a.5.5 0 0 1-.5.5M2 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" })
                                            )
                                        })
                                    ),
                                    createElement(Button, {
                                        isPrimary: navigationJustify[deviceType] === 'right',
                                        isSecondary: navigationJustify[deviceType] !== 'right',
                                        onClick: () => setAttributes({ navigationJustify: { ...navigationJustify, [deviceType]: 'right' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    },
                                        createElement(Icon, {
                                            icon: () => createElement('svg', {
                                                xmlns: "http://www.w3.org/2000/svg",
                                                width: 20,
                                                height: 20,
                                                fill: "currentColor",
                                                viewBox: "0 0 16 16"
                                            },
                                                createElement('path', { fillRule: "evenodd", d: "M14.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5" }),
                                                createElement('path', { d: "M13 7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1z" })
                                            )
                                        })
                                    )
                                ),
                                createElement(ToggleControl, {
                                    label: 'Align to content container',
                                    checked: navigationContentContainer[deviceType],
                                    onChange: (value) => setAttributes({ navigationContentContainer: { ...navigationContentContainer, [deviceType]: value } }),
                                }),
                                navigationContentContainer[deviceType] && createElement(RangeControl, {
                                    label: 'Buttons Horizontal Offset (px)',
                                    value: attributes.navigationButtonsHorizontalOffset?.[deviceType] ?? 0,
                                    onChange: (value) => setAttributes({
                                        navigationButtonsHorizontalOffset: {
                                            ...attributes.navigationButtonsHorizontalOffset,
                                            [deviceType]: value
                                        }
                                    }),
                                    min: -100,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 0
                                }),
                                createElement(RangeControl, {
                                    label: 'Navigation Vertical Offset (px)',
                                    value: attributes.navigationButtonOffset?.[deviceType] ?? 0,
                                    onChange: (value) => setAttributes({
                                        navigationButtonOffset: {
                                            ...attributes.navigationButtonOffset,
                                            [deviceType]: value
                                        }
                                    }),
                                    min: -100,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 24
                                }),
                                createElement('p', { style: { marginTop: '1em', marginBottom: '0.5em' } }, 'Buttons Position'),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, {
                                        isPrimary: navigationArrowsPosition[deviceType] === 'inside',
                                        isSecondary: navigationArrowsPosition[deviceType] !== 'inside',
                                        onClick: () => setAttributes({ navigationArrowsPosition: { ...navigationArrowsPosition, [deviceType]: 'inside' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Inside'),
                                    createElement(Button, {
                                        isPrimary: navigationArrowsPosition[deviceType] === 'contained',
                                        isSecondary: navigationArrowsPosition[deviceType] !== 'contained',
                                        onClick: () => setAttributes({ navigationArrowsPosition: { ...navigationArrowsPosition, [deviceType]: 'contained' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Contained'),
                                    createElement(Button, {
                                        isPrimary: navigationArrowsPosition[deviceType] === 'outside',
                                        isSecondary: navigationArrowsPosition[deviceType] !== 'outside',
                                        onClick: () => setAttributes({ navigationArrowsPosition: { ...navigationArrowsPosition, [deviceType]: 'outside' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Outside')
                                ),
                                createElement(RangeControl, {
                                    label: 'Buttons size (px)',
                                    value: navigationButtonSize[deviceType],
                                    onChange: (value) => setAttributes({ navigationButtonSize: { ...navigationButtonSize, [deviceType]: value } }),
                                    min: 20,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 40
                                }),
                                createElement(RangeControl, {
                                    label: 'Icon Size (px)',
                                    value: navigationArrowSize[deviceType],
                                    onChange: (value) => setAttributes({ navigationArrowSize: { ...navigationArrowSize, [deviceType]: value } }),
                                    min: 10,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 20
                                }),
                                createElement(RangeControl, {
                                    label: 'Button Border Width (px)',
                                    value: navigationButtonBorderWidth,
                                    onChange: (value) => setAttributes({ navigationButtonBorderWidth: value }),
                                    min: 0,
                                    max: 5,
                                    allowReset: true,
                                    resetFallbackValue: 0
                                }),
                               
                                createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                    createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Navigation Border Color'),
                                    createElement(ColorPalette, {
                                        colors: colors,
                                        value: navigationButtonBorderColor,
                                        onChange: (value) => setAttributes({ navigationButtonBorderColor: value }),
                                        clearable: true,
                                    })),
                                createElement(RangeControl, {
                                    label: 'Button Border Radius (px)',
                                    value: navigationArrowBorderRadius[deviceType],
                                    onChange: (value) => setAttributes({ navigationArrowBorderRadius: { ...navigationArrowBorderRadius, [deviceType]: value } }),
                                    min: 0,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 0
                                }),
                                createElement(RangeControl, {
                                    label: 'Buttons spacing (px)',
                                    value: navigationArrowsSpacing[deviceType],
                                    onChange: (value) => setAttributes({ navigationArrowsSpacing: { ...navigationArrowsSpacing, [deviceType]: value } }),
                                    min: 0,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 12
                                }),
                                createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                    createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Navigation Arrow Color'),
                                    createElement(ColorPalette, {
                                        colors: colors,
                                        value: navigationArrowColor,
                                        onChange: (value) => setAttributes({ navigationArrowColor: value }),
                                        clearable: true,
                                    })
                                ),
                                createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                    createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Navigation Arrow Background Color'),
                                    createElement(ColorPalette, {
                                        colors: colors,
                                        value: navigationArrowBgColor ?? '',
                                        onChange: (value) => setAttributes({ navigationArrowBgColor: value }),
                                        clearable: true,
                                    })
                                ),
                                createElement(ToggleControl, {
                                    label: 'Custom Navigation',
                                    checked: customNavigation,
                                    onChange: (value) => setAttributes({ customNavigation: value }),
                                }),
                                customNavigation && createElement(TextareaControl, {
                                    label: 'Custom Navigation SVG',
                                    value: customNavigationSVG,
                                    onChange: (value) => setAttributes({ customNavigationSVG: value }),
                                    help: 'Enter the SVG code for the next arrow. The previous arrow will be rotated.'
                                }),
                            )
                        ),
                        pagination[deviceType] && createElement(
                            PanelBody, { title: 'Pagination Settings', initialOpen: true },
                            navigation[deviceType] && createElement(ToggleControl, {
                                label: 'Pagination attached to arrows',
                                checked: paginationAttachedToArrows[deviceType],
                                onChange: (value) => setAttributes({ paginationAttachedToArrows: { ...paginationAttachedToArrows, [deviceType]: value } }),
                            }),
                            !paginationAttachedToArrows[deviceType] && createElement(
                                Fragment, null,
                                createElement('p', { style: { marginTop: '1em', marginBottom: '0.5em' } }, 'Pagination Align'),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, {
                                        isPrimary: attributes.paginationJustify?.[deviceType] === 'left',
                                        isSecondary: attributes.paginationJustify?.[deviceType] !== 'left',
                                        onClick: () => setAttributes({ paginationJustify: { ...attributes.paginationJustify, [deviceType]: 'left' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Left'),
                                    createElement(Button, {
                                        isPrimary: attributes.paginationJustify?.[deviceType] === 'center',
                                        isSecondary: attributes.paginationJustify?.[deviceType] !== 'center',
                                        onClick: () => setAttributes({ paginationJustify: { ...attributes.paginationJustify, [deviceType]: 'center' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Center'),
                                    createElement(Button, {
                                        isPrimary: attributes.paginationJustify?.[deviceType] === 'right',
                                        isSecondary: attributes.paginationJustify?.[deviceType] !== 'right',
                                        onClick: () => setAttributes({ paginationJustify: { ...attributes.paginationJustify, [deviceType]: 'right' } }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Right')
                                ),
                                createElement(RangeControl, {
                                    label: 'Pagination offset (px)',
                                    value: attributes.paginationOffset?.[deviceType] ?? 0,
                                    onChange: (value) => setAttributes({ paginationOffset: { ...attributes.paginationOffset, [deviceType]: value } }),
                                    min: -100,
                                    max: 100,
                                    allowReset: true,
                                    resetFallbackValue: 0
                                }),
                            ),
                            createElement(RangeControl, {
                                label: 'Pagination Bullet Size (px)',
                                value: attributes.paginationBulletSize?.[deviceType] ?? 8,
                                onChange: (value) => setAttributes({ paginationBulletSize: { ...attributes.paginationBulletSize, [deviceType]: value } }),
                                min: 4,
                                max: 32,
                                allowReset: true,
                                resetFallbackValue: 8
                            }),
                            createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Pagination Bullet Color'),
                                createElement(ColorPalette, {
                                    colors: colors,
                                    value: paginationBulletColor ?? '#ccc',
                                    onChange: (value) => setAttributes({ paginationBulletColor: value }),
                                    clearable: true,
                                })
                            ),
                            createElement(RangeControl, {
                                label: 'Pagination Active Bullet Size (px)',
                                value: paginationActiveBulletSize?.[deviceType] ?? 8,
                                onChange: (value) => setAttributes({ paginationActiveBulletSize: { ...paginationActiveBulletSize, [deviceType]: value } }),
                                min: 4,
                                max: 64,
                                allowReset: true,
                                resetFallbackValue: 24
                            }),
                            createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Active Bullet Color'),
                                createElement(ColorPalette, {
                                    colors: colors,
                                    value: paginationActiveBulletColor ?? '#000',
                                    onChange: (value) => setAttributes({ paginationActiveBulletColor: value }),
                                    clearable: true,
                                })
                            ),
          
                            createElement(ToggleControl, {
                                label: 'Custom Render Bullet',
                                checked: attributes.customRenderBullet,
                                onChange: (value) => setAttributes({ customRenderBullet: value }),
                            }),
                            attributes.customRenderBullet && createElement(TextareaControl, {
                                label: 'Custom Bullet HTML (use {index} for slide number, {className} for bullet class)',
                                value: attributes.customRenderBulletHtml,
                                onChange: (value) => setAttributes({ customRenderBulletHtml: value }),
                                help: 'Example: <div class="{className}"><span class="bullet"></span><span class="number">{index}</span></div>'
                            }),
                        ),
                        attributes.scrollbar && createElement(
                            PanelBody, { title: 'Scrollbar Settings', initialOpen: true },
                            createElement(
                                Fragment,
                                null,
                                createElement('div', { style: { marginTop: '1em', marginBottom: '0.5em' } }, 'Scrollbar Position'),
                                createElement(ButtonGroup, { style: { marginBottom: '1em', display: 'flex' } },
                                    createElement(Button, { // Top
                                        isPrimary: (scrollbarPosition || 'bottom') === 'top',
                                        isSecondary: (scrollbarPosition || 'bottom') !== 'top',
                                        onClick: () => setAttributes({ scrollbarPosition: 'top' }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Top'),
                                    createElement(Button, { // Bottom
                                        isPrimary: (scrollbarPosition || 'bottom') === 'bottom',
                                        isSecondary: (scrollbarPosition || 'bottom') !== 'bottom',
                                        onClick: () => setAttributes({ scrollbarPosition: 'bottom' }),
                                        style: { flex: 1, justifyContent: 'center' }
                                    }, 'Bottom')
                                ),
                                createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                    createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Scrollbar Color'),
                                    createElement(ColorPalette, {
                                        colors: colors,
                                        value: scrollbarColor ?? 'rgba(0, 0, 0, 0.1)',
                                        onChange: (value) => setAttributes({ scrollbarColor: value }),
                                        clearable: true,
                                    })
                                ),
                                createElement('fieldset', { style: { border: 0, padding: 0, margin: 0, marginBottom: '1em' } },
                                    createElement('legend', { style: { fontWeight: 500, marginBottom: 4 } }, 'Scrollbar Active Color'),
                                    createElement(ColorPalette, {
                                        colors: colors,
                                        value: scrollbarDragColor ?? 'rgba(0, 0, 0, 0.25)',
                                        onChange: (value) => setAttributes({ scrollbarDragColor: value }),
                                        clearable: true,
                                    })
                                ),
                            ),
                        )
                    )
                ),
                createElement(
                    'div',
                    { className: 'hcdigital-swiper-slider-editor' },
                    useGutenbergEditor
                        ? createElement(Fragment, null,
                            createElement(InnerBlocks, {
                                //template: patternTemplate,
                                templateLock: false,
                                allowedBlocks: ['hcdigital/slide'],
                                renderAppender: false
                            }),
                            createElement('div', { style: { display: 'flex', justifyContent: 'center', marginTop: '1em' } },
                                createElement(Button, {
                                    isPrimary: true,
                                    onClick: openPatternModal,
                                    ref: anchorRef
                                }, 'Add new slide')
                            ),
                            isPatternPopoverOpen && createElement(
                                Popover,
                                {
                                    anchor: anchorRef.current,
                                    onClose: () => setPatternPopoverOpen(false),
                                    position: 'top center',
                                    focusOnMount: 'container',
                                    offset: 10
                                },
                                createElement(
                                    'div',
                                    { style: { padding: '16px', minWidth: '250px' } },
                                    
                                    createElement(
                                        'ul',
                                        { style: { listStyle: 'none', padding: 0, margin: 0 } },
                                        createElement(
                                            'li',
                                            { style: { marginBottom: '0.5em' } },
                                            createElement(Button, { isSecondary: true, onClick: addBlankSlide, style: { width: '100%', justifyContent: 'center' } }, 'Add Blank Slide')
                                        ),
                                        patterns && patterns.map(pattern =>
                                            createElement(
                                                'li',
                                                { key: pattern.name, style: { marginBottom: '0.5em' } },
                                                createElement(
                                                    Button,
                                                    {
                                                        isSecondary: true,
                                                        style: { width: '100%', justifyContent: 'center' },
                                                        onClick: () => {
                                                            insertPattern(pattern);
                                                            setPatternPopoverOpen(false);
                                                        }
                                                    },
                                                    decodeTitle(pattern.title)
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                        : createElement(Placeholder, {
                            icon: 'slides',
                            label: 'Dynamic Slider',
                            instructions: `Slides will be generated from the '${selectedPostType}' post type on the front-end.`
                        })
                )
            );
        }),
        save: function (props) {
            const { attributes } = props;
            if (attributes.useGutenbergEditor) {
                return createElement(InnerBlocks.Content, null);
            }
            return null;
        },
    });
})(window.wp);