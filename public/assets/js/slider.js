$(function () {
    $('.slide').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: true,
        dots: true,
        pauseOnHover: true,
        slidesToShow: 1,
        centerMode: true,
        centerPadding: '30%',
    });
});

$('.slide').on('swipe', function () {
	$(this).slick('slickPause');
});
