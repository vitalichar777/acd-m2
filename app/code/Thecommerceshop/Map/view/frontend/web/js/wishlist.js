define(
    [
        "jquery",
    ],
    function ($) {
        $(".product-addto-links .towishlist").hover(
            function (e) {
               var dataPost=$(this).attr("data-post");
                var urlWishList="wishlist\\/index\\/add";
                var urlTcsWistList="tcs_quickview\\/wishlist\\/add";
                dataPost=dataPost.replace(urlWishList,urlTcsWistList);
                urlWishList="wishlist/index/add";
                urlTcsWistList="tcs_quickview/wishlist/add";
                dataPost=dataPost.replace(urlWishList,urlTcsWistList);
                $(this).attr("data-post",dataPost);
            }
        );
    }
);
