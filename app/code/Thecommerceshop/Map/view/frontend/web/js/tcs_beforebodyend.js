define(
    [
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal'
],
    function ($) {
    $.widget(
        'tcs.tcs_beforebodyend',
        {
            _create: function () {
                var options = this.options;
                var showReview = options.removeReview;
                var showMoreInfo = options.removeMoreInfo;
                $(document).ready(
                    function () {

                        var documentPadding = 10;
                        var firstAttempt = true;
                        var lastHeight = 0, curHeight = 0;

                        var parentBody = window.parent.document.body;
                        $('.mfp-preloader', parentBody).css('display', 'none');
                        $('.mfp-iframe-holder .mfp-content', parentBody).css('width', '100%');

                        $('.mfp-iframe-scaler iframe', parentBody).animate({'opacity': 1}, 2000);
                        $('.reviews-actions a').attr('target', '_parent');
                        $('.product-social-links a').attr('target', '_parent');
                        // Allow scrolling for the popup content - removed overflow: hidden
                        $('body').css('overflow', 'auto');

                        // Function to scroll price to center of viewport
                        function scrollToCenterPrice() {
                            console.log('scrollToCenterPrice function called');
                            
                            // Target the product price section
                            var priceSelectors = [
                                '.product-info-price',
                                '.price-box',
                                '.product-info-main .price',
                                '.price-container',
                                '.product-price'
                            ];
                            
                            var priceElement = null;
                            
                            // Try each selector until we find the product price
                            for (var i = 0; i < priceSelectors.length; i++) {
                                priceElement = $(priceSelectors[i]);
                                if (priceElement.length > 0) {
                                    console.log('Found price element with selector:', priceSelectors[i]);
                                    break;
                                }
                            }
                            
                            if (priceElement && priceElement.length > 0) {
                                var scrollTarget = priceElement.first();
                                console.log('Found product price element:', scrollTarget[0]);
                                console.log('Element text:', scrollTarget.text());
                                
                                // Check if we should scroll (mobile device or price not visible)
                                var shouldScroll = false;
                                
                                // Check if mobile device
                                var isMobile = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                                console.log('Is mobile device:', isMobile);
                                
                                // Check if price element is visible in viewport
                                var elementRect = scrollTarget[0].getBoundingClientRect();
                                var windowHeight = window.innerHeight;
                                var isVisible = (elementRect.top >= 0 && elementRect.top <= windowHeight) || 
                                               (elementRect.bottom >= 0 && elementRect.bottom <= windowHeight) ||
                                               (elementRect.top < 0 && elementRect.bottom > windowHeight);
                                
                                console.log('Element position - top:', elementRect.top, 'bottom:', elementRect.bottom);
                                console.log('Window height:', windowHeight);
                                console.log('Price element is visible:', isVisible);
                                
                                // Scroll if mobile OR price not visible
                                shouldScroll = isMobile || !isVisible;
                                console.log('Should scroll:', shouldScroll, '(mobile:', isMobile, ', not visible:', !isVisible, ')');
                                
                                if (shouldScroll) {
                                    // Scroll price element to center of viewport
                                    setTimeout(function() {
                                        if (scrollTarget[0] && typeof scrollTarget[0].scrollIntoView === 'function') {
                                            scrollTarget[0].scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center',
                                                inline: 'nearest'
                                            });
                                            console.log('ScrollIntoView executed to center price element');
                                        }
                                    }, 800); // Delay to ensure content is stable
                                } else {
                                    console.log('Scroll skipped - price is already visible on desktop');
                                }
                                
                            } else {
                                console.log('Product price element not found');
                            }
                        }

                        // Listen for the custom event from the parent popup
                        $(document).on('popupContentReady', function() {
                            setTimeout(scrollToCenterPrice, 200);
                        });
                        
                        // Fallback: Scroll to center price after a delay
                        setTimeout(scrollToCenterPrice, 1500); // Wait 1.5 seconds for content to fully load

                        setInterval(
                            function ()
                            {
                                if (firstAttempt) {
                                    curHeight = $('.page-wrapper .columns').outerHeight(true) + documentPadding;
                                } else {
                                    curHeight = $('.page-wrapper .columns').outerHeight(true);
                                }
                                documentHeight = curHeight + "px";
                                if (curHeight != lastHeight) {
                                    $(
                                        '.mfp-iframe-holder .mfp-content',
                                        parentBody
                                    ).animate(
                                        {
                                            'height': documentHeight
                                        },
                                        500
                                    );
                                    lastHeight = curHeight;
                                    firstAttempt = false;
                                }
                            },
                            500
                        );

                        $('.reviews-actions a').attr('target', '_parent');
                        $('.product-social-links a').attr('target', '_parent');
                        $(document).on(
                            'click',
                            'a',
                            function (e) {
                                e.preventDefault();
                                if ($(this).attr('data-post')) {
                                    qty = 0;
                                    from_key = '';
                                    data_url = jQuery.parseJSON($(this).attr('data-post'));
                                    if ($(this).parents('#maincontent').find('input[name="form_key"]').val() != '') {
                                        from_key = $(this).parents('#maincontent').find('input[name="form_key"]').val();
                                    }
                                    if ($(this).parents('#maincontent').find('input[name="qty"]').val() > 0) {
                                        qty = $(this).parents('#maincontent').find('input[name="qty"]').val();
                                    }
                                    url = data_url.action + 'product/' + data_url.data.product + '/qty/' + qty + '/form_key/' + from_key + '/uenc/' + data_url.data.uenc + '/tcsquickview/1';
                                } else {
                                    url = $(this).attr('href');
                                    var myClass = $(this).attr('class');
                                }
                                if (showMoreInfo == 1 && showReview == 1) {
                                    if (url != '#additional' && url != '#reviews' && url != '#product.info.description' && myClass != 'action add' && url != '#description' && myClass != 'action view') {
                                        self.parent.location.href = url;
                                        return false;
                                    }
                                } else {
                                    if (url != '#additional' && url != '#reviews' && url != '#product.info.description' && url != '#description') {
                                        self.parent.location.href = url;
                                        return false;
                                    }
                                }
                            }
                        )
                    }
                );

                /** Events listener **/
                $(document).on(
                    'ajaxComplete',
                    function (event, xhr, settings) {
                        var parentBody = window.parent.document.body;
                        var cartMessage = false;
                        if (settings.type.match(/get/i) && _.isObject(xhr.responseJSON)) {
                            var result = xhr.responseJSON;
                            if (_.isObject(result.messages)) {
                                var messageLength = result.messages.messages.length;
                                var message = result.messages.messages[0];
                                if (messageLength && message.type == 'success') {
                                    cartMessage = message.text;
                                }
                            }
                            if (_.isObject(result.cart) && _.isObject(result.messages)) {
                                var messageLength = result.messages.messages.length;
                                var message = result.messages.messages[0];
                                if (messageLength && message.type == 'success') {
                                    cartMessage = message.text;
                                }
                            }
                        }
                    }
                );
            }
        }
    );
    return $.tcs.tcs_beforebodyend;
});
