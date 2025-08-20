jQuery(document).ready(function($){
    $("nav.main-nav li:first-child a").addClass("active");
    $('.home-r4 .row-item').each(function () {
        let $carousel = $(this);
        let swiperEl = $carousel.find('.swiper')[0]; 
        let bulletPaginationEl = $carousel.find('.pv-swiper-pagination')[0]; 
        var swiper = new Swiper(swiperEl, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 0,
            pagination: {
                el: bulletPaginationEl,
            },
        });

    });

    var currentIndex = 1;
    var totalSlides = 3;
    var autoPlayDelay = 3000; 
    var reverse = true; // true = cung chieu, false = nguoc chieu
    var $image = $(".gallery .images");

    setInterval(function(){
        if (reverse) {
            currentIndex--;
            if (currentIndex < 1) currentIndex = totalSlides;
        } else {
            currentIndex++;
            if (currentIndex > totalSlides) currentIndex = 1;
        }
        
        $image.removeClass(function (index, className) {
            return (className.match(/(^|\s)type-\d+/g) || []).join(' ');
        });
        $image.addClass("type-" + currentIndex);
    }, autoPlayDelay);
});//end ready