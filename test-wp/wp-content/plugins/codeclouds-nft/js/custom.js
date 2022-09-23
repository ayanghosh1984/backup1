// $(".showCCcoupon").click(function(){
//     console.log(0);
//     $("p").toggleClass("enter-cc-coupon");
// });

jQuery( function($){

    // $('#cc_nft_coupon_success').hide();
    $('form.checkout').on('click', '.showCCcoupon', function(){
        console.log('toggle');
        $('#cc_nft_coupon_error').hide();
        $("#ccCoupon").toggleClass("toggle-cc-coupon");
        var x = document.getElementById("ccCoupon");
        var y = document.getElementById("cc_nft_coupon_success");
        if (window.getComputedStyle(x).display != "none" && window.getComputedStyle(y).display === "none") { 
            loginWithMetaMask();
        }
    });
});