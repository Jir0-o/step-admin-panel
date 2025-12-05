// Side bar set
$(document).ready(function () {
    function checkWidth() {
        if ($(window).width() < 1024) {
            $('body').addClass('side-hide');
        } else {
            $('body').removeClass('side-hide');
        }
    }

    checkWidth();

    $(window).resize(function () {
        checkWidth();
    });

});

// sidebar function
function sidebar() {
    $("body").toggleClass("side-hide");
}

// dashboard 5 store sale item comparison chart

