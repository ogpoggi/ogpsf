$(window).on('scroll', function () {
    var scrollTop     = $(window).scrollTop(),
        elementOffset = $('#navogp').offset().top,
        distance      = (elementOffset - scrollTop);

    if(distance == 0){
        //Fair eles changements CSS
    }
});