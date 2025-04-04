( function( blocks, element, blockEditor ) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ColorPicker = wp.components.ColorPicker;
    var PanelBody = wp.components.PanelBody;

    blocks.registerBlockType( 'jw-allround/featured-box', {
        title: 'Featured Box',
        icon: 'star-filled',
        category: 'design',
        attributes: {
            title: {
                type: 'string',
                default: 'Boks Titel'
            },
            text: {
                type: 'string',
                default: 'Indtast din tekst her.'
            },
            icon: {
                type: 'string',
                default: 'star'
            },
            link: {
                type: 'string',
                default: ''
            },
            linkText: {
                type: 'string',
                default: 'Læs mere'
            },
            backgroundColor: {
                type: 'string',
                default: '#ffffff'
            },
            textColor: {
                type: 'string',
                default: '#333333'
            }
        },
        
        edit: function( props ) {
            var attributes = props.attributes;
            
            // Opdater attributter
            function onChangeTitle( newTitle ) {
                props.setAttributes( { title: newTitle } );
            }
            
            function onChangeText( newText ) {
                props.setAttributes( { text: newText } );
            }
            
            function onChangeIcon( newIcon ) {
                props.setAttributes( { icon: newIcon } );
            }
            
            function onChangeLink( newLink ) {
                props.setAttributes( { link: newLink } );
            }
            
            function onChangeLinkText( newLinkText ) {
                props.setAttributes( { linkText: newLinkText } );
            }
            
            function onChangeBackgroundColor( newColor ) {
                props.setAttributes( { backgroundColor: newColor.hex } );
            }
            
            function onChangeTextColor( newColor ) {
                props.setAttributes( { textColor: newColor.hex } );
            }
            
            // Dashicons liste
            var iconOptions = [
                { label: 'Stjerne', value: 'star' },
                { label: 'Admin-brugere', value: 'admin-users' },
                { label: 'Kalender', value: 'calendar' },
                { label: 'Chart', value: 'chart-bar' },
                { label: 'Mail', value: 'email' },
                { label: 'Hjerte', value: 'heart' },
                { label: 'Lokation', value: 'location' },
                { label: 'Lås', value: 'lock' },
                { label: 'Mikroskop', value: 'search' },
                { label: 'Tag', value: 'tag' }
            ];
            
            return [
                el( InspectorControls, { key: 'controls' },
                    el( PanelBody, { title: 'Box Indstillinger' },
                        el( TextControl, {
                            label: 'Titel',
                            value: attributes.title,
                            onChange: onChangeTitle
                        }),
                        el( TextControl, {
                            label: 'Tekst',
                            value: attributes.text,
                            onChange: onChangeText
                        }),
                        el( SelectControl, {
                            label: 'Ikon',
                            value: attributes.icon,
                            options: iconOptions,
                            onChange: onChangeIcon
                        }),
                        el( TextControl, {
                            label: 'Link URL',
                            value: attributes.link,
                            onChange: onChangeLink
                        }),
                        el( TextControl, {
                            label: 'Link Tekst',
                            value: attributes.linkText,
                            onChange: onChangeLinkText
                        })
                    ),
                    el( PanelBody, { title: 'Farveindstillinger' },
                        el( 'p', {}, 'Baggrundsfarve:' ),
                        el( ColorPicker, {
                            color: attributes.backgroundColor,
                            onChangeComplete: onChangeBackgroundColor
                        }),
                        el( 'p', {}, 'Tekstfarve:' ),
                        el( ColorPicker, {
                            color: attributes.textColor,
                            onChangeComplete: onChangeTextColor
                        })
                    )
                ),
                el( 'div', { 
                    className: 'jw-featured-box-editor',
                    style: {
                        backgroundColor: attributes.backgroundColor,
                        color: attributes.textColor,
                        padding: '20px',
                        borderRadius: '4px',
                        boxShadow: '0px 4px 10px rgba(0,0,0,0.1)'
                    }
                },
                    el( 'div', { className: 'jw-featured-box-icon-editor' },
                        el( 'span', { className: 'dashicons dashicons-' + attributes.icon })
                    ),
                    el( 'h3', {}, attributes.title ),
                    el( 'p', {}, attributes.text ),
                    attributes.link && el( 'a', { 
                        href: '#',
                        className: 'jw-featured-box-link-editor'
                    }, attributes.linkText )
                )
            ];
        },
        
        save: function() {
            // Retur null da vi bruger PHP render_callback
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );