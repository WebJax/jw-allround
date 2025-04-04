( function( blocks, element, blockEditor ) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var Button = wp.components.Button;
    var PanelBody = wp.components.PanelBody;

    blocks.registerBlockType( 'jw-allround/testimonial-slider', {
        title: 'Testimonial Slider',
        icon: 'format-quote',
        category: 'widgets',
        attributes: {
            testimonials: {
                type: 'array',
                default: [
                    {
                        content: 'Dette er en fantastisk service!',
                        author: 'Peter Jensen',
                        company: 'Firma A/S'
                    },
                    {
                        content: 'Vi har været meget tilfredse med samarbejdet.',
                        author: 'Mette Nielsen',
                        company: 'Virksomhed ApS'
                    }
                ]
            }
        },
        
        edit: function( props ) {
            var attributes = props.attributes;
            var testimonials = attributes.testimonials;
            
            // Funktioner til at håndtere testimonials
            function addTestimonial() {
                var newTestimonials = testimonials.slice();
                newTestimonials.push({
                    content: '',
                    author: '',
                    company: ''
                });
                props.setAttributes( { testimonials: newTestimonials } );
            }
            
            function updateTestimonialContent( index, content ) {
                var newTestimonials = testimonials.slice();
                newTestimonials[index].content = content;
                props.setAttributes( { testimonials: newTestimonials } );
            }
            
            function updateTestimonialAuthor( index, author ) {
                var newTestimonials = testimonials.slice();
                newTestimonials[index].author = author;
                props.setAttributes( { testimonials: newTestimonials } );
            }
            
            function updateTestimonialCompany( index, company ) {
                var newTestimonials = testimonials.slice();
                newTestimonials[index].company = company;
                props.setAttributes( { testimonials: newTestimonials } );
            }
            
            function removeTestimonial( index ) {
                var newTestimonials = testimonials.slice();
                newTestimonials.splice( index, 1 );
                props.setAttributes( { testimonials: newTestimonials } );
            }
            
            // Opret testimonial indstillinger i sidepanelet
            var testimonialsSettings = testimonials.map( function( testimonial, index ) {
                return el( PanelBody, { 
                    title: 'Testimonial ' + ( index + 1 ),
                    initialOpen: index === 0
                },
                    el( TextareaControl, {
                        label: 'Udtalelse',
                        value: testimonial.content,
                        onChange: function( content ) {
                            updateTestimonialContent( index, content );
                        }
                    }),
                    el( TextControl, {
                        label: 'Forfatter',
                        value: testimonial.author,
                        onChange: function( author ) {
                            updateTestimonialAuthor( index, author );
                        }
                    }),
                    el( TextControl, {
                        label: 'Virksomhed',
                        value: testimonial.company,
                        onChange: function( company ) {
                            updateTestimonialCompany( index, company );
                        }
                    }),
                    el( Button, {
                        isDestructive: true,
                        onClick: function() {
                            removeTestimonial( index );
                        }
                    }, 'Fjern denne testimonial' )
                );
            });
            
            // Frontend visning af testimonials i editoren
            var testimonialList = testimonials.map( function( testimonial, index ) {
                return el( 'div', { 
                    className: 'jw-testimonial-editor',
                    key: index
                },
                    el( 'blockquote', { 
                        className: 'jw-testimonial-content-editor'
                    }, testimonial.content ),
                    el( 'cite', { 
                        className: 'jw-testimonial-author-editor'
                    }, testimonial.author ),
                    testimonial.company && el( 'span', { 
                        className: 'jw-testimonial-company-editor'
                    }, testimonial.company )
                );
            });
            
            return [
                // Tilføj indstillinger til sidepanelet
                el( InspectorControls, { key: 'testimonial-controls' },
                    testimonialsSettings,
                    el( Button, {
                        isPrimary: true,
                        onClick: addTestimonial
                    }, 'Tilføj ny testimonial' )
                ),
                
                // Frontend visning i editor
                el( 'div', { 
                    className: 'jw-testimonial-slider-editor',
                    style: {
                        backgroundColor: '#ffffff',
                        padding: '20px',
                        borderRadius: '4px',
                        boxShadow: '0px 4px 10px rgba(0,0,0,0.1)'
                    }
                },
                    el( 'h3', {}, 'Testimonials' ),
                    el( 'div', { className: 'jw-testimonial-slider-inner-editor' },
                        testimonialList
                    ),
                    el( 'p', { 
                        style: { 
                            fontStyle: 'italic', 
                            fontSize: '13px', 
                            marginTop: '10px' 
                        } 
                    }, '* I editoren vises alle testimonials sammen. På frontend vises de som en slider.' )
                )
            ];
        },
        
        save: function() {
            // Retur null da vi bruger PHP render_callback
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );