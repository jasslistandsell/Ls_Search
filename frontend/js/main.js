$(document).ready(function(){
    $('.ls-search-slider:not(.autoplay)').slick({ 
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 1000,
        speed:500, 
        arrows: true,
        dots: true,
        fade:true,
        lazyLoad: 'ondemand',
        pauseOnHover: false,
        prevArrow: $('.prev'),
        nextArrow: $('.next'),
        appendDots:"#mainSlider__dots1"
    });
    $('.ls-search-slider.autoplay').slick({ 
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: $(this).attr("apspeed"),
        speed:500, 
        arrows: true,
        dots: true,
        fade:true,
        lazyLoad: 'ondemand',
        pauseOnHover: false,
        prevArrow: $('.prev'),
        nextArrow: $('.next'),
        appendDots:"#mainSlider__dots1"
    });


    $(document).on('beforeChange', '.ls-search-outer .ls-search-slider', function(event, slick, currentSlide, nextSlide){
        var elem = $(this);
        var color = elem.find('div[data-slick-index='+nextSlide+']').attr('backcolor');
        if(!color){
            color = elem.find('div[data-slick-index='+nextSlide+'] [backcolor]').attr('backcolor');
        }
        elem.closest('.ls-search-outer').css({backgroundColor:color});
    });

    $(document).ready(function(){
        $(document).on("change", ".lscatportlet-selectcatids", function(){
            // console.log($(this).attr("catid"));
        }); 
    });  

});




/**
 * 
 * UTIL FUNCTIONS
 * 
 */
(function ($) {
    /**
     * 
     * CHECK IF HAS HORIZONTAL SCROLLBAR
     * 
     */
    $.fn.hasScrollBar = function(direction, offset = 0)
    {
        if (direction == 'vertical')
        {
            return this.get(0).scrollHeight > this.innerHeight() + offset;
        }
        else if (direction == 'horizontal')
        {
            // console.log(this.get(0).scrollWidth + " "+this.innerWidth());
            return this.get(0).scrollWidth > this.innerWidth() + offset;
        }
        return false;
    }

}(jQuery));