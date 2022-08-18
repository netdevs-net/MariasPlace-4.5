'use strict';

(function($){

    /**
     * Copied from WooCommerce for our enhanced search.
     */
    function getEnhancedSelectFormatString() {
        return {
            'language': {
                errorLoading: function() {
                    // Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
                    return wc_enhanced_select_params.i18n_searching;
                },
                inputTooLong: function( args ) {
                    var overChars = args.input.length - args.maximum;

                    if ( 1 === overChars ) {
                        return wc_enhanced_select_params.i18n_input_too_long_1;
                    }

                    return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
                },
                inputTooShort: function( args ) {
                    var remainingChars = args.minimum - args.input.length;

                    if ( 1 === remainingChars ) {
                        return wc_enhanced_select_params.i18n_input_too_short_1;
                    }

                    return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
                },
                loadingMore: function() {
                    return wc_enhanced_select_params.i18n_load_more;
                },
                maximumSelected: function( args ) {
                    if ( args.maximum === 1 ) {
                        return wc_enhanced_select_params.i18n_selection_too_long_1;
                    }

                    return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
                },
                noResults: function() {
                    return wc_enhanced_select_params.i18n_no_matches;
                },
                searching: function() {
                    return wc_enhanced_select_params.i18n_searching;
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded',function() {
        var types = document.getElementsByClassName('wc-redirect-type');
        function redirectChangeType(event) {

            if ( event.target.checked ) {
                for (var i = 0; i < types.length; i++) {
                    if ( types[i].parentNode.parentNode.nextElementSibling ) {
                        types[i].parentNode.parentNode.nextElementSibling.classList.add('hidden');
                    }
                }
                if ( event.target.parentNode.parentNode.nextElementSibling ) {
                    event.target.parentNode.parentNode.nextElementSibling.classList.remove('hidden');
                }
            }
        }
        if (types) {
            for (var i = 0; i < types.length; i++) {
                types[i].addEventListener('change', redirectChangeType);
                if ( types[i].checked ) {
                    if ( types[i].parentNode.parentNode.nextElementSibling ) {
                        types[i].parentNode.parentNode.nextElementSibling.classList.remove('hidden');
                    }
                }
            }
        }
    });

    $(function() {

        if( $('.wcrty-code-editor').length > 0 && typeof wp.codeEditor !== 'undefined' ) {
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2
                }
            );
            $('.wcrty-code-editor').each( function(){
                wp.codeEditor.initialize( $( this ), editorSettings );
            });
        }

        $( document.body ).on( 'wcrty-page-options-init', function(){
            $( '.wcrty-page-options' ).filter( ':not(.enhanced)' ).each( function(){
                var $this = $(this);
                $this.find('input[type=radio]').on( 'change', function(){
                    $this.find('.wcrty-page-option').addClass('hidden');
                    $(this).parents('li').find('.wcrty-page-option').removeClass('hidden');
                });
                $this.addClass('enhanced');
            });
        }).trigger('wcrty-page-options-init');

        /**
         * Reattach the URL options for new or loaded Variations.
         */
        $( document.body ).on( 'woocommerce_variations_loaded', function(){
            $( document.body ).trigger( 'wcrty-page-options-init');
        });

        $( document.body ).on( 'woocommerce_variations_added', function(){
            $( document.body ).trigger( 'wcrty-page-options-init');
        });


        /**
         * Created our own select init, copied from WooCommerce so our found categories are saved in IDs.
         */
        $( document.body ).on( 'wcrty-enhanced-select-init', function(){
            // Ajax category search boxes
            $( ':input.wcrty-category-search' ).filter( ':not(.enhanced)' ).each( function() {
                var select2_args = $.extend( {
                    allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                    placeholder       : $( this ).data( 'placeholder' ),
                    minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3,
                    escapeMarkup      : function( m ) {
                        return m;
                    },
                    ajax: {
                        url:         wc_enhanced_select_params.ajax_url,
                        dataType:    'json',
                        delay:       250,
                        data:        function( params ) {
                            return {
                                term:     params.term,
                                action:   'woocommerce_json_search_categories',
                                security: wc_enhanced_select_params.search_categories_nonce
                            };
                        },
                        processResults: function( data ) {
                            var terms = [];
                            if ( data ) {
                                $.each( data, function( id, term ) {
                                    terms.push({
                                        id:   id,
                                        text: term.formatted_name
                                    });
                                });
                            }
                            return {
                                results: terms
                            };
                        },
                        cache: true
                    }
                }, getEnhancedSelectFormatString() );

                $( this ).selectWoo( select2_args ).addClass( 'enhanced' );
            });
        }).trigger('wcrty-enhanced-select-init');

        $( document ).on( 'change', '.wcrty-completed-email-type', function(e){
            var $this = $(this),
                val   = $this.val(),
                row   = $this.parents('.wcrty-email-text'),
                input = row.find('select.enhanced'),
                sel   = input.select2();

            sel.select2('destroy');
            input.removeClass('enhanced');
            input.val(null);
            if ( val === 'category' ) {
                input.removeClass('wc-product-search');
                input.addClass('wcrty-category-search');
                input.attr('data-placeholder', wcrty.placeholders.categories );
                input.data( 'placeholder', wcrty.placeholders.categories );
            } else {
                input.addClass('wc-product-search');
                input.removeClass('wcrty-category-search');
                input.attr('data-placeholder', wcrty.placeholders.products );
                input.data('placeholder', wcrty.placeholders.products );
            }
            $(document.body).trigger('wc-enhanced-select-init');
            $(document.body).trigger('wcrty-enhanced-select-init');

        });

        $( '#wcrtyAddCompletedEmailText' ).on( 'click', function(){
           var count = $('#wcrty_completed_email_texts').children().length,
               data  = { index: count },
               tmpl  = wp.template('wcrty-completed-email'),
               html  = tmpl( data );

           $('#wcrty_completed_email_texts').append( html );
           $(document.body).trigger('wc-enhanced-select-init');
           $(document.body).trigger('wcrty-enhanced-select-init');
        });

        $( '#wcrtyAddGatewayURL' ).on( 'click', function(){
            var count = $('#wcrty_gateways_urls').children().length,
                data  = { index: count },
                tmpl  = wp.template('wcrty-gateway-url'),
                html  = tmpl( data );

            $('#wcrty_gateways_urls').append( html );
            $(document.body).trigger('wcrty-page-options-init');
        });

        $( document.body ).on( 'click', '.wcrty-delete-email-text', function(e){
            $(this).parent().remove();
        });

        $( document.body ).on( 'click', '.wcrty-delete-gateway', function(e){
           $(this).parent().remove();
        });

    });
})(jQuery);