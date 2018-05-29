
$(document).ready(function() {

    $(".def-nav,.info-i").hover(function() {
        $(this).find(".pulldown-nav").addClass("hover");
        $(this).find(".pulldown").show();
    }, function() {
        $(this).find(".pulldown").hide();
        $(this).find(".pulldown-nav").removeClass("hover");
    });

});
      