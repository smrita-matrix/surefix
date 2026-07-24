// Make sure DOM is loaded
window.addEventListener("load", () => {
  if (gsap && ScrollSmoother) {
    ScrollSmoother.create({
      wrapper: "#smooth-wrapper",
      content: "#smooth-content",
      smooth: 1.2,     
      effects: true,   
    });
  }
});

$(document).ready(function() {
  var owl = $('.bestseller');
  owl.owlCarousel({
    margin: 20,
    loop: true,
    dots: true,
    autoplay: false,
    autoplayTimeout: 4500,
    navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"], 
    responsive: {
      0: {
          items: 1
      },
      480: {
          items: 1
      },
      640: {
          items:1,
          nav: false,
      },
      992: {
          items: 2,
          nav: true,
      },
      1200: {
          items: 3,
          nav: true,
      },
      1440: {
          items: 3,
          nav: true,
      }
    }
  })
})

// service hover active 
  $('.service-hover-active .service-box-2').on("mouseover", function () {
    $(this).addClass('active').siblings().removeClass('active');
  });


jQuery(document).ready(function ($) {
    $('.counter-number').counterUp({
        delay: 10,
        time: 1000
    });
});


AOS.init({
  once: true
})


//wow js
    var wow = new WOW({
        animateClass: 'animated',
        offset: 100,
        mobile: false,
        duration: 1000,
    });
    wow.init();
    
  //scorl animation js
    var $single_portfolio_img = $('.overlay_effect');
    var $window = $(window);

    function scroll_addclass() {
        var window_height = $(window).height() - 200;
        var window_top_position = $window.scrollTop();
        var window_bottom_position = (window_top_position + window_height);

        $.each($single_portfolio_img, function () {
            var $element = $(this);
            var element_height = $element.outerHeight();
            var element_top_position = $element.offset().top;
            var element_bottom_position = (element_top_position + element_height);

            //check to see if this current container is within viewport
            if ((element_bottom_position >= window_top_position) &&
                (element_top_position <= window_bottom_position)) {
                $element.addClass('is_show');
            }
        });
    }

    $window.on('scroll resize', scroll_addclass);
    $window.trigger('scroll');



