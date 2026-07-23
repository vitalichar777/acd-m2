define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox'
], function ($, _, mageTemplate, utils) {
    'use strict';

    return function (priceBundle) {

        $.widget('mage.priceBundle', priceBundle, {
            // function which you want to overwrite
            /**
             * @private
             */
            _create: function createPriceBundle() {
                var form = this.element,
                    options = $(this.options.productBundleSelector, form),
                    priceBox = $(this.options.priceBoxSelector, form),
                    qty = $(this.options.qtyFieldSelector, form);
                    if($("body").hasClass("tcs_quickview-catalog_product-view")) {
                        var selectionList = parent.window.BundleListQty;
                        for (let i = 0; i < selectionList.length; ++i) {
                            if((selectionList[i].optionid != '') && (selectionList[i].selectionid != '')){
                                this.options.optionConfig.options[selectionList[i].optionid].selections[selectionList[i].selectionid].qty = selectionList[i].value;
                            }
                        }
                    }
                this._updatePriceBox();
                priceBox.on('price-box-initialized', this._updatePriceBox.bind(this));
                options.on('change', this._onBundleOptionChanged.bind(this));
                qty.on('change', this._onQtyFieldChanged.bind(this));
            },

            /**
             * Handle change on bundle option inputs
             * @param {jQuery.Event} event
             * @private
             */
            _onBundleOptionChanged: function onBundleOptionChanged(event) {
                var changes, selectedIds,
                    bundleOption = $(event.target),
                    priceBox = $(this.options.priceBoxSelector, this.element),
                    handler = this.options.optionHandlers[bundleOption.data('role')];

                bundleOption.data('optionContainer', bundleOption.closest(this.options.controlContainer));
                bundleOption.data('qtyField', bundleOption.data('optionContainer').find(this.options.qtyFieldSelector));

                if (handler && handler instanceof Function) {
                    changes = handler(bundleOption, this.options.optionConfig, this);
                } else {
                    //TCS - customization
                    let OptionData = defaultGetOptionValue(bundleOption, this.options.optionConfig);//eslint-disable-line
                    if (OptionData && typeof OptionData === 'object' && 'changes' in OptionData) {
                        changes = OptionData['changes'];
                        selectedIds = OptionData['selectedIds'];
                    }
                }

                // eslint-disable-next-line no-use-before-define
                if (isValidQty(bundleOption)) {
                    if (changes) {
                        priceBox.trigger('updatePrice', changes);
                        
                        //TCS - customization Over ride added to hide the customized section price for bundle products to avoid price mismatch error on Gfeed
                        let formOptions = $(this.options.productBundleSelector, '#product_addtocart_form');
                        let changeContainer = $('.change-container-classname');
                        let priceConfigureContainer = $('.price-as-configured');
                        let priceBoxProduct = $('.price-as-configured .price-configured_price span').attr('id');
                        let pid = priceBoxProduct.replace('product-price-','');
                        let selectedCount = $.map(selectedIds, function(n, i) { return i; }).length;
                        if(changeContainer.length > 0 && formOptions.length == selectedCount){
                            if(priceConfigureContainer.hasClass('hide')){
                                priceConfigureContainer.removeClass('hide');
                                priceConfigureContainer.addClass('show');
                            }
                        }else if(changeContainer.length > 0 && formOptions.length != selectedCount){
                            changeContainer.one('click',function(){
                                if(priceConfigureContainer.hasClass('hide')){
                                    priceConfigureContainer.removeClass('hide');
                                    priceConfigureContainer.addClass('show');
                                }
                            });
                        }else{
                            if(priceConfigureContainer.hasClass('hide')){
                                priceConfigureContainer.removeClass('hide');
                                priceConfigureContainer.addClass('show');
                            }
                        }
                        updateSavePrice(pid);
                    }
                    
                    if($("body").hasClass("tcs_quickview-catalog_product-view") && $(".tcs_quickview-catalog_product-view .product-info-price .map-price span.mPrice").length > 0) {
                        // Get base price from the configured price display (without radio buttons)
                        var oPrice = $(".tcs_quickview-catalog_product-view .price-as-configured span.price").html();
                        var mPriceObj = $(".product-info-price .map-price span.mPrice");

                        // First set the base price (which doesn't include radio button prices)
                        mPriceObj.html(oPrice);

                        // Extract the base price as a number for calculations
                        var basePrice = parseFloat((mPriceObj.html().substring(1)).replace(/,/g, ''));
                        if (isNaN(basePrice)) {
                            basePrice = 0;
                        }

                        // Add radio button selection prices
                        var totalPrice = basePrice;
                        var radioSelections = parent.window.BundleList || {};
                        var radioOptions = this.options.optionConfig.options || {};

                        // Process each selected radio option
                        // for (var key in radioSelections) {
                        //     if (radioSelections.hasOwnProperty(key) && radioSelections[key] !== 0) {
                        //         var optionIdMatch = key.match(/bundle-option-(\d+)/);
                        //         if (optionIdMatch && optionIdMatch[1]) {
                        //             var optionId = optionIdMatch[1];
                        //             var selectionId = radioSelections[key];

                        //             // Find the price for this option in the config
                        //             if (radioOptions[optionId] && 
                        //                 radioOptions[optionId].selections && 
                        //                 radioOptions[optionId].selections[selectionId]) {

                        //                 var selectionPrice = radioOptions[optionId].selections[selectionId].prices || {};
                        //                 if (selectionPrice.finalPrice && selectionPrice.finalPrice.amount) {
                        //                     // Get quantity
                        //                     var qty = 1;
                        //                     for (var i = 0; i < parent.window.BundleListQty.length; i++) {
                        //                         if (parent.window.BundleListQty[i].optionid == optionId && 
                        //                             parent.window.BundleListQty[i].selectionid == selectionId) {
                        //                             qty = parseFloat(parent.window.BundleListQty[i].value) || 1;
                        //                             break;
                        //                         }
                        //                     }
                        //                     totalPrice += selectionPrice.finalPrice.amount * qty;
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }

                        // Format and update the total price
                        var config = this.options;
                        var format = config.priceFormat;
                        mPriceObj.html(utils.formatPrice(totalPrice, format));

                        // Collect warranty option prices from .warranty_options
                        var warrantyPrices = 0;
                        $('.warranty_options input[type="radio"]:checked, .warranty_options input[type="checkbox"]:checked').each(function() {
                            var priceContainer = $(this).closest('.field.choice').find('.price-container [data-price-amount]');
                            if (priceContainer.length) {
                                var price = parseFloat(priceContainer.attr('data-price-amount'));
                                if (!isNaN(price)) {
                                    warrantyPrices += price;
                                }
                            }
                        });

                        // Continue with discount calculations
                        var mPrice = mPriceObj.html();
                        mPrice = (mPrice.substring(1, mPrice.length)).replace(",",'');
                        var basePriceForDiscount = parseFloat(mPrice) - warrantyPrices;
                        var disvalue = $(".product-info-price .map-price span.mPrice").data('disvalue');
                        var distype = $(".product-info-price .map-price span.mPrice").data('distype');
                        var youSavePrice = 0;
                        var specialPrice = 0;
                        if(distype == 1){
                            youSavePrice = (basePriceForDiscount*disvalue)/100;
                        }else{
                            youSavePrice = basePriceForDiscount - disvalue;
                        }
                        specialPrice = basePriceForDiscount - youSavePrice + warrantyPrices;

                        var config = this.options,
                        format = config.priceFormat;

                        $(".product-info-price .map-price span.mSpecialPrice").html(utils.formatPrice(specialPrice, format));
                        $(".product-info-price .map-price span.SavePrice").html(utils.formatPrice(youSavePrice, format));
                        $(".product-info-price").scrollTop();
                    }
                    this._displayTierPriceBlock(bundleOption);
                    this.updateProductSummary();
                }
            }
        });

        // don't forget to add a function which is used by your  
        // functions, else it will show undefined function error

        return $.mage.priceBundle;

        /**
         * Converts option value to priceBox object
         *
         * @param   {jQuery} element
         * @param   {Object} config
         * @returns {Object|null} - priceBox object with additional prices
         */
        function defaultGetOptionValue(element, config) {
            var changes = {},
                optionHash,
                tempChanges,
                qtyField,
                optionId = utils.findOptionId(element[0]),
                optionValue = element.val() || null,
                optionName = element.prop('name'),
                optionType = element.prop('type'),
                optionConfig = config.options[optionId].selections,
                optionQty = 0,
                canQtyCustomize = false,
                selectedIds = config.selected;

            switch (optionType) {
                case 'radio':
                case 'select-one':

                    if (optionType === 'radio' && !element.is(':checked')) {
                        return null;
                    }

                    qtyField = element.data('qtyField');
                    qtyField.data('option', element);

                    if (optionValue) {
                        optionQty = optionConfig[optionValue].qty || 0;
                        canQtyCustomize = optionConfig[optionValue].customQty === '1';
                        toggleQtyField(qtyField, optionQty, optionId, optionValue, canQtyCustomize);//eslint-disable-line
                        tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                        tempChanges = applyTierPrice(//eslint-disable-line
                            tempChanges,
                            optionQty,
                            optionConfig[optionValue]
                        );
                        tempChanges = applyQty(tempChanges, optionQty);//eslint-disable-line
                    } else {
                        tempChanges = {};
                        toggleQtyField(qtyField, '0', optionId, optionValue, false);//eslint-disable-line
                    }
                    optionHash = 'bundle-option-' + optionName;
                    changes[optionHash] = tempChanges;
                    selectedIds[optionId] = [optionValue];
                    break;

                case 'select-multiple':
                    optionValue = _.compact(optionValue);

                    _.each(optionConfig, function (row, optionValueCode) {
                        optionHash = 'bundle-option-' + optionName + '##' + optionValueCode;
                        optionQty = row.qty || 0;
                        tempChanges = utils.deepClone(row.prices);
                        tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);//eslint-disable-line
                        tempChanges = applyQty(tempChanges, optionQty);//eslint-disable-line
                        changes[optionHash] = _.contains(optionValue, optionValueCode) ? tempChanges : {};
                    });

                    selectedIds[optionId] = optionValue || [];
                    break;

                case 'checkbox':
                    optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                    optionQty = optionConfig[optionValue].qty || 0;
                    tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                    tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);//eslint-disable-line
                    tempChanges = applyQty(tempChanges, optionQty);//eslint-disable-line
                    changes[optionHash] = element.is(':checked') ? tempChanges : {};

                    selectedIds[optionId] = selectedIds[optionId] || [];

                    if (!_.contains(selectedIds[optionId], optionValue) && element.is(':checked')) {
                        selectedIds[optionId].push(optionValue);
                    } else if (!element.is(':checked')) {
                        selectedIds[optionId] = _.without(selectedIds[optionId], optionValue);
                    }
                    break;

                case 'hidden':
                    optionHash = 'bundle-option-' + optionName + '##' + optionValue;
                    optionQty = optionConfig[optionValue].qty || 0;
                    canQtyCustomize = optionConfig[optionValue].customQty === '1';
                    qtyField = element.data('qtyField');
                    qtyField.data('option', element);
                    toggleQtyField(qtyField, optionQty, optionId, optionValue, canQtyCustomize);//eslint-disable-line
                    tempChanges = utils.deepClone(optionConfig[optionValue].prices);
                    tempChanges = applyTierPrice(tempChanges, optionQty, optionConfig);//eslint-disable-line
                    tempChanges = applyQty(tempChanges, optionQty);//eslint-disable-line

                    optionHash = 'bundle-option-' + optionName;
                    changes[optionHash] = tempChanges;
                    selectedIds[optionId] = [optionValue];
                    break;
            }
            //TCS - customization
            let optionsData =[];
            optionsData['changes'] = changes;
            optionsData['selectedIds'] = selectedIds;
            return optionsData;
        }

        /**
         * Check the quantity field if negative value occurs.
         *
         * @param {Object} bundleOption
         */
        function isValidQty(bundleOption) {
            var isValid = true,
                qtyElem = bundleOption.data('qtyField'),
                bundleOptionType = bundleOption.prop('type'),
                qtyValidator = qtyElem.data('validate') &&
                    typeof qtyElem.data('validate')['validate-item-quantity'] === 'object' ?
                    qtyElem.data('validate')['validate-item-quantity'] : null;

            if (['radio', 'select-one'].includes(bundleOptionType) &&
                qtyValidator &&
                (qtyElem.val() < qtyValidator.minAllowed || qtyElem.val() > qtyValidator.maxAllowed)
            ) {
                isValid = false;
            }

            return isValid;
        }

        /**
         * Helper to toggle qty field
         * @param {jQuery} element
         * @param {String|Number} value
         * @param {String|Number} optionId
         * @param {String|Number} optionValueId
         * @param {Boolean} canEdit
         */
        function toggleQtyField(element, value, optionId, optionValueId, canEdit) {
            element
                .val(value)
                .data('optionId', optionId)
                .data('optionValueId', optionValueId)
                .attr('disabled', !canEdit);

            if (canEdit) {
                element.removeClass('qty-disabled');
            } else {
                element.addClass('qty-disabled');
            }
        }

        /**
         * Helper to multiply on qty
         *
         * @param   {Object} prices
         * @param   {Number} qty
         * @returns {Object}
         */
        function applyQty(prices, qty) {
            _.each(prices, function (everyPrice) {
                everyPrice.amount *= qty;
                _.each(everyPrice.adjustments, function (el, index) {
                    everyPrice.adjustments[index] *= qty;
                });
            });

            return prices;
        }

        /**
         * Helper to limit price with tier price
         *
         * @param {Object} oneItemPrice
         * @param {Number} qty
         * @param {Object} optionConfig
         * @returns {Object}
         */
        function applyTierPrice(oneItemPrice, qty, optionConfig) {
            var tiers = optionConfig.tierPrice,
                magicKey = _.keys(oneItemPrice)[0],
                tiersFirstKey = _.keys(optionConfig)[0],
                lowest = false;

            if (!tiers) {//tiers is undefined when options has only one option
                tiers = optionConfig[tiersFirstKey].tierPrice;
            }

            tiers.sort(function (a, b) {//sorting based on "price_qty"
                return a['price_qty'] - b['price_qty'];
            });

            _.each(tiers, function (tier, index) {
                if (tier['price_qty'] > qty) {
                    return;
                }

                if (tier.prices[magicKey].amount < oneItemPrice[magicKey].amount) {
                    lowest = index;
                }
            });

            if (lowest !== false) {
                oneItemPrice = utils.deepClone(tiers[lowest].prices);
            }

            return oneItemPrice;
        }

        function updateSavePrice(pid){
            if(document.getElementById('old-price-configured-'+pid) != null || document.getElementById('old-price-configured-'+pid) != undefined){
                var newPrice = document.getElementById('product-price-'+pid).firstChild;
                var oldPrice = document.getElementById('old-price-configured-'+pid).firstChild;
                var savingPrice =  Number(oldPrice.innerHTML.replace(/[^0-9.-]+/g,"")) - Number(newPrice.innerHTML.replace(/[^0-9.-]+/g,""));
                var savingText = 'You Save: $'+parseFloat(savingPrice).toFixed(2)+'!';
                document.getElementById('saving-price-label-configured-'+pid).innerHTML = savingText;
            }
        }

    };
});