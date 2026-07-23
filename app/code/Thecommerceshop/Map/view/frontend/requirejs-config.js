
var config = {
    map: {
        '*': {
            tcs_config: 'Thecommerceshop_Map/js/tcs_config',
            magnificPopup: 'Thecommerceshop_Map/js/jquery.magnific-popup.min',
            tcs_tocart: 'Thecommerceshop_Map/js/tcs_tocart'
        }
    },
    shim: {
        magnificPopup: {
            deps: ['jquery']
        }
    },
    config : {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Thecommerceshop_Map/js/add-to-cart-mixin': true
            },
            'Magento_Bundle/js/price-bundle': {
                'Thecommerceshop_Map/js/price-bundle-mixin': true
            }
        }
    }
};
