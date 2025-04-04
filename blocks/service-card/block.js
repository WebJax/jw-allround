( function( blocks, element, blockEditor ) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var Button = wp.components.Button;
    var PanelBody = wp.components.PanelBody;

    blocks.registerBlockType( 'jw-allround/service-card', {
        title: 'Service Card',
        icon: 'portfolio',
        category: 'design',
        attributes: {
            title: {
                type: 'string',
                default: 'Service Titel'
            },
            description: {
                type: 'string',
                default: 'Beskrivelse af denne service eller ydelse.'
            },
            imageUrl: {
                type: 'string',
                default: ''
            },
            imageId: {
                type: 'number',
                default: 0
            },
            imageAlt: {
                type: 'string',
                default: ''
            },
            linkUrl: {
                type: 'string',
                default: ''
            },
            linkText: {
                type: 'string',
                default: 'Læs mere'
            }
        },
        
        edit: function( props ) {
            var attributes = props.attributes;
            
            // Opdater attributter
            function onChangeTitle( newTitle ) {
                props.setAttributes( { title: newTitle } );
            }
            
            function onChangeDescription( newDescription ) {
                props.setAttributes( { description: newDescription } );
            }
            
            function onSelectImage( media ) {
                props.setAttributes( { 
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || ''
                } );
            }
            
            function onRemoveImage() {
                props.setAttributes( { 
                    imageUrl: '',
                    imageId: 0,
                    imageAlt: ''
                } );
            }
            
            function onChangeImageAlt( newAlt ) {
                props.setAttributes( { imageAlt: newAlt } );
            }
            
            function onChangeLinkUrl( newUrl ) {
                props.setAttributes( { linkUrl: newUrl } );
            }
            
            function onChangeLinkText( newText ) {
                props.setAttributes( { linkText: newText } );
            }
            
            return [
                // Inspector Controls (sidepanel)
                el( InspectorControls, { key: 'controls' },
                    el( PanelBody, { title: 'Service Card Indstillinger' },
                        el( TextControl, {
                            label: 'Titel',
                            value: attributes.title,
                            onChange: onChangeTitle
                        }),
                        el( TextareaControl, {
                            label: 'Beskrivelse',
                            value: attributes.description,
                            onChange: onChangeDescription
                        }),
                        el( 'div', { className: 'editor-post-featured-image' },
                            el( 'div', { className: 'editor-post-featured-image__container' },
                                el( MediaUpload, {
                                    onSelect: onSelectImage,
                                    allowedTypes: [ 'image' ],
                                    value: attributes.imageId,
                                    render: function( obj ) {
                                        return el( Button, {
                                            className: attributes.imageId ? 'editor-post-featured-image__preview' : 'editor-post-featured-image__toggle',
                                            onClick: obj.open
                                        }, 
                                            attributes.imageId ? el( 'img', { src: attributes.imageUrl }) : 'Vælg et billede'
                                        );
                                    }
                                })
                            ),
                            attributes.imageId ? el( 'div', { className: 'editor-post-featured-image__actions' },
                                el( Button, {
                                    isLink: true,
                                    isDestructive: true,
                                    onClick: onRemoveImage
                                }, 'Fjern billede' )
                            ) : null
                        ),
                        attributes.imageUrl ? el( TextControl, {
                            label: 'Alt tekst',
                            value: attributes.imageAlt,
                            onChange: onChangeImageAlt
                        }) : null,
                        el( TextControl, {
                            label: 'Link URL',
                            value: attributes.linkUrl,
                            onChange: onChangeLinkUrl
                        }),
                        el( TextControl, {
                            label: 'Link tekst',
                            value: attributes.linkText,
                            onChange: onChangeLinkText
                        })
                    )
                ),
                
                // Editor visning
                el( 'div', { 
                    className: 'service-card',
                    style: {
                        backgroundColor: '#ffffff',
                        borderRadius: '4px',
                        boxShadow: '0px 4px 10px rgba(0,0,0,0.1)',
                        padding: '20px',
                        height: '100%',
                        display: 'flex',
                        flexDirection: 'column'
                    }
                },
                    attributes.imageUrl ? el( 'div', { 
                        className: 'service-card-image',
                        style: {
                            marginBottom: '15px'
                        }
                    },
                        el( 'img', { 
                            src: attributes.imageUrl, 
                            alt: attributes.imageAlt,
                            style: {
                                width: '100%',
                                borderRadius: '4px'
                            }
                        })
                    ) : el( 'div', { 
                        className: 'service-card-image-placeholder',
                        style: {
                            backgroundColor: '#f0f0f0',
                            height: '150px',
                            borderRadius: '4px',
                            marginBottom: '15px',
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center',
                            color: '#888'
                        }
                    }, 'Billede kommer her'),
                    
                    el( 'div', { className: 'service-card-content' },
                        el( 'h3', { 
                            className: 'service-card-title',
                            style: { 
                                margin: '0 0 10px 0'
                            } 
                        }, attributes.title ),
                        el( 'p', { 
                            className: 'service-card-description',
                            style: { 
                                margin: '0 0 20px 0',
                                flexGrow: 1
                            } 
                        }, attributes.description ),
                        
                        attributes.linkUrl ? el( 'a', { 
                            href: '#',
                            className: 'button service-card-link',
                            style: {
                                display: 'inline-block',
                                backgroundColor: '#005b9a',
                                color: '#ffffff',
                                padding: '8px 16px',
                                borderRadius: '4px',
                                textDecoration: 'none'
                            }
                        }, attributes.linkText ) : null
                    )
                )
            ];
        },
        
        save: function() {
            // Vi bruger PHP render callback
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );