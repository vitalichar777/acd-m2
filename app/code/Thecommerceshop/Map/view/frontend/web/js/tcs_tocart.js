define(
    [
        'jquery',
        'Thecommerceshop_Map/js/tcs_tocart',
        'mage/mage',
        'Magento_Catalog/product/view/validation',
        'Magento_Catalog/js/catalog-add-to-cart',
        'Thecommerceshop_Map/js/jquery.magnific-popup.min'
    ],
    function ($) {
        'use strict';

        $.widget(
            'tcs.tcs_tocart',
            {
                _create: function () {
                    'use strict';
                    $('#product_addtocart_form').mage(
                        'validation',
                        {
                            radioCheckboxClosest: '.nested',
                            submitHandler: function (form) {
                                var widget = $(form).catalogAddToCart(
                                    {
                                        bindSubmit: false
                                    }
                                );
                                widget.catalogAddToCart('submitForm', $(form));
                                $("#product-buynow-button").addClass("disabled");
                                //$('.page.messages').css('display','none');
                                setTimeout(function() {
                                    // var childmsgDiv = $('.page.messages');
                                    // var childmsgEl = childmsgDiv.find('.message');
                                    // if(childmsgEl.length > 0 && childmsgEl.hasClass('message-success') && childmsgEl.children().html() != ''){
                                    //     var childmsg = $('.page.messages').html();
                                    //     $('.page.messages', window.parent.document).html(childmsg);
                                    // }else{console.log('else');
                                    //     var Parentmsg = $('.page.messages', window.parent.document);
                                    //     var ParentchildmsgEl = Parentmsg.find('.message');
                                    //     if(ParentchildmsgEl.hasClass('message-success') && childmsgEl.children().html() != ''){
                                    //         ParentchildmsgEl.children().html('You added '+$(".page-title-wrapper.product .page-title span").html()+' to your <a href="'+window.location.origin+'/checkout/cart/">shopping cart</a>.');
                                    //     }else{
                                    //         Parentmsg.append('<div class="messages"><div class="message-success success message"><div>You added '+$(".page-title-wrapper.product .page-title span").html()+' to your <a href="'+window.location.origin+'/checkout/cart/">shopping cart</a>.</div></div></div>');
                                    //     }
                                    // }
                                    // $('.page.messages', window.parent.document).css('dispaly','block');
                                    window.parent.window.MAPGOTOP = 'true';
                                    //$('.mfp-close', window.parent.document).trigger('click');
                                    window.top.location.href = window.top.location.origin+'/checkout/cart/';
                                }, 4000);
                                return false;
                            }
                        }
                    );
                    $('#ajax-goto a').click(
                        function (e) {
                            e.preventDefault();
                            window.top.location.href = $(this).attr('href');

                            return false;
                        }
                    );

                    $("#product-ctn-shopping-button").click(function(e) {
                        $.magnificPopup.close();
                        window.top.location.reload(true);
                    });

                    $("#product-buynow-button").unbind('click').bind('click', function(e) {
                        e.preventDefault();
                        $('#product-addtocart-button').trigger('click');
                    });
                }
            }
        );
        return $.tcs.tcs_tocart;
    }
);
