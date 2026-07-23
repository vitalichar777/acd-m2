define(
    [
        'jquery',
        'mage/mage',
        'Thecommerceshop_Map/js/jquery.magnific-popup.min'
    ],
    function ($) {
        "use strict";
        $.widget(
            'tcs.tcs_config',
            {
                options: {
                    productUrl: '',
                    buttonText: '',
                    isEnabled: false,
                    baseUrl: '',
                    productImageWrapper: '',
                    productItemInfo: ''
                },

                _create: function () {
                    //this.renderButton();
                    this._EventListener();
                },

                renderButton: function () {
                    var $widget = this,
                        id_product,
                        productImageWrapper = '.' + this.options.productImageWrapper,
                        productItemInfo = '.' + this.options.productItemInfo;
                    if ($widget.options.isEnabled == 1) {
                        $(productImageWrapper).each(
                            function () {

                                if ($(this).parents(productItemInfo).find('.actions-primary input[name="product"]').val() != '') {
                                    id_product = $(this).parents(productItemInfo).find('.actions-primary input[name="product"]').val();
                                }
                                if (!id_product) {
                                    id_product = $(this).parents(productItemInfo).find('.price-box').data('product-id');
                                }
                                if (id_product) {
                                    $(this).append('<div id="quickview-' + id_product + '" class="tcs-bt-quickview"><a class="tcs-quickview" data-quickview-url="' + $widget.options.productUrl + 'id/' + id_product + '" href="javascript:void(0);" ><span>View Lowest Price</span></a></div>');
                                }
                            }
                        )
                    }
                },

                _EventListener: function () {
                    var $widget = this;
                    if ($widget.options.isEnabled == 1) {

                        $('a.mailto').click(
                            function (e) {
                                e.preventDefault();
                                window.top.location.href = $(this).attr('href');
                                return true;
                            }
                        );

                        $('body, #layer-product-list').on(
                            'contentUpdated',
                            function () {
                                //$('.tcs-bt-quickview').remove();
                                //$widget.renderButton();
                            }
                        );

                        $(document).on(
                            'click',
                            '.tcs-quickview',
                            function () {
                                var prodUrl = $(this).attr('data-quickview-url');
                                if (prodUrl.length) {
                                    $widget.openPopup(prodUrl);
                                }
                            }
                        );
                    }
                },

                openPopup: function (prodUrl) {
                    var $widget = this,
                        url = $widget.options.baseUrl + 'tcs_quickview/index/updatecart';

                    if (!prodUrl.length) {
                        return false;
                    }

                    window.MAPGOTOP = 'false';
                    $.magnificPopup.open(
                        {
                            items: {
                                src: prodUrl
                            },
                            type: 'iframe',
                            //closeMarkup: '<button title="Close (Esc)" type="button" class="mfp-close" data-popup="mfp"></button>',
                            closeOnBgClick: false,
                            scrolling: false,
                            preloader: true,
                            tLoading: '',
                            callbacks: {
                                open: function () {
                                    $('.mfp-preloader').css('display', 'block');
                                    $("iframe.mfp-iframe").contents().find("html").addClass("tcs_loader");
                                    $("iframe.mfp-iframe").contents().find("html").addClass("tcs_map");
                                    
                                    // Monitor iframe loading to trigger scroll functionality
                                    var iframe = $("iframe.mfp-iframe")[0];
                                    if (iframe) {
                                        iframe.onload = function() {
                                            // Set a flag to indicate iframe is loaded for scroll functionality
                                            window.POPUP_IFRAME_LOADED = true;
                                            
                                            // Trigger a custom event that the beforebodyend script can listen to
                                            setTimeout(function() {
                                                $(iframe.contentWindow.document).trigger('popupContentReady');
                                            }, 500);
                                        };
                                    }
                                },
                                beforeClose: function () {
                                    $('[data-block="minicart"]').trigger('contentLoading');
                                    $.ajax(
                                        {
                                            url: url,
                                            method: "POST"
                                        }
                                    );
                                },
                                close: function () {
                                    if(window.MAPGOTOP == 'true'){
                                        $('html, body').animate({
                                            scrollTop: $(".page.messages").offset().top - 50
                                        }, 1000);
                                    }
                                    $('.mfp-preloader').css('display', 'none');
                                }
                            }
                        }
                    );
                    // Handle dropdown select options
                    var bundleList = $('.bundle-options-wrapper #product-options-wrapper .field.option select');
                    var BundleSelection = [];
                    if(bundleList.length > 0){
                        bundleList.each(function(){
                            BundleSelection[this.id] = this.selectedIndex;
                        });
                    }

                    // Handle radio button options
                    var radioOptions = $('.bundle-options-wrapper #product-options-wrapper .field.option .radio');
                    if(radioOptions.length > 0){
                        radioOptions.each(function(){
                            if($(this).is(':checked')) {
                                // Extract the option group ID from the name attribute (bundle_option[13030])
                                var nameAttr = $(this).attr('name');
                                var optionId = nameAttr.match(/\[(\d+)\]/)[1];
                                var optionKey = 'bundle-option-' + optionId;

                                // Store the selected value
                                if($(this).val()) {
                                    // If a specific option is selected (not "None")
                                    BundleSelection[optionKey] = $(this).val();
                                } else {
                                    // If "None" is selected
                                    BundleSelection[optionKey] = 0;
                                }
                            }
                        });
                    }

                    window.BundleList = BundleSelection;

                    // Add event listeners for radio button changes
                    $('.bundle-options-wrapper #product-options-wrapper .field.option .radio').on('change', function() {
                        // When a radio button changes, update the BundleList
                        var nameAttr = $(this).attr('name');
                        var optionId = nameAttr.match(/\[(\d+)\]/)[1];
                        var optionKey = 'bundle-option-' + optionId;
                        var selectionId = $(this).val();

                        if(selectionId) {
                            // If a specific option is selected
                            window.BundleList[optionKey] = selectionId;

                            // Update BundleListQty with the new selection
                            var qtyElement = $('#bundle-option-' + optionId + '-qty-input');

                            if(qtyElement.length > 0) {
                                // Find this option in the BundleListQty array
                                var updated = false;
                                for(var i = 0; i < window.BundleListQty.length; i++) {
                                    if(window.BundleListQty[i].optionid == optionId) {
                                        window.BundleListQty[i].selectionid = selectionId;
                                        updated = true;
                                        break;
                                    }
                                }

                                // If the option wasn't found in the array, add it
                                if(!updated) {
                                    window.BundleListQty.push({
                                        'optionid': optionId,
                                        'selectionid': selectionId,
                                        'value': qtyElement.val()
                                    });
                                }
                            }
                        } else {
                            // If "None" is selected
                            window.BundleList[optionKey] = 0;
                            
                            // Also update BundleListQty to remove this option or set selectionid to false
                            for(var i = 0; i < window.BundleListQty.length; i++) {
                                if(window.BundleListQty[i].optionid == optionId) {
                                    window.BundleListQty[i].selectionid = false;
                                    break;
                                }
                            }
                        }
                    });

                    // Initialize BundleListQty
                    var bundleListQty = $('.bundle-options-wrapper #product-options-wrapper .field.qty .input-text.qty');
                    var BundleOptions = [];
                    if(bundleListQty.length > 0){
                        bundleListQty.each(function(index){
                            var optionIdArray = this.id.split('-');
                            var optionId = optionIdArray[2];
                            var selectionId = false;

                            // Check for dropdown selection first
                            let selection = $('.bundle-options-wrapper #product-options-wrapper .field.option .bundle-option-'+optionId);
                            if(selection.length == 1){
                                selectionId = selection.val();
                            } else {
                                // Check for radio button selection
                                let radioSelection = $('.bundle-options-wrapper #product-options-wrapper .field.option input[name="bundle_option['+optionId+']"]:checked');
                                if(radioSelection.length == 1){
                                    selectionId = radioSelection.val();
                                }
                            }
                            BundleOptions[index] = {'optionid':optionId, 'selectionid': selectionId, 'value':$(this).val()};
                        });
                    }
                    window.BundleListQty = BundleOptions;

                    // Add event listener for quantity changes
                    $('.bundle-options-wrapper #product-options-wrapper .field.qty .input-text.qty').on('change', function() {
                        var optionIdArray = this.id.split('-');
                        var optionId = optionIdArray[2];
                        var qtyValue = $(this).val();

                        // Find this option in the BundleListQty array and update its value
                        var updated = false;
                        for(var i = 0; i < window.BundleListQty.length; i++) {
                            if(window.BundleListQty[i].optionid == optionId) {
                                window.BundleListQty[i].value = qtyValue;
                                updated = true;
                                break;
                            }
                        }

                        // If not found, check if there's a selection for this option and add it
                        if(!updated) {
                            // Check for dropdown selection first
                            let selection = $('.bundle-options-wrapper #product-options-wrapper .field.option .bundle-option-'+optionId);
                            let selectionId = false;
                            
                            if(selection.length == 1) {
                                selectionId = selection.val();
                            } else {
                                // Check for radio button selection
                                let radioSelection = $('.bundle-options-wrapper #product-options-wrapper .field.option input[name="bundle_option['+optionId+']"]:checked');
                                if(radioSelection.length == 1) {
                                    selectionId = radioSelection.val();
                                }
                            }

                            if(selectionId) {
                                window.BundleListQty.push({
                                    'optionid': optionId,
                                    'selectionid': selectionId,
                                    'value': qtyValue
                                });
                            }
                        }
                    });
                }
            }
        );
        return $.tcs.tcs_config;
    }
);
