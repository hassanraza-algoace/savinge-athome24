// var swiper = new Swiper(".main-swiper", {
//   loop: true,
//   pagination: {
//     el: ".swiper-pagination",
//   },
//   autoplay: {
//     delay: 2000,
//   },
// });

var swiperposts = new Swiper(".productPopularSwiper", {
  draggable: true,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
  },
  navigation: {
    nextEl: "#product-popular-swiper-button-next",
    prevEl: "#product-popular-swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 2,
    },
    992: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
  },
});

var swiperposts = new Swiper(".productNewSwiper", {
  draggable: true,
  loop: true,
  navigation: {
    nextEl: "#product-popular-swiper-button-next",
    prevEl: "#product-popular-swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    640: {
      slidesPerView: 3,
    },
    992: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
  },
});

var swiperposts = new Swiper(".productRelatedSwiper", {
  draggable: true,
  loop: true,
  navigation: {
    nextEl: "#product-popular-swiper-button-next",
    prevEl: "#product-popular-swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    640: {
      slidesPerView: 3,
    },
    992: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
  },
});

var swiperposts = new Swiper(".brandSwiper", {
  draggable: true,
  pagination: {
    el: ".swiper-pagination",
  },
  loop: true,
  navigation: {
    nextEl: "#product-brand-swiper-button-next",
    prevEl: "#product-brand-swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 2,
      spaceBetween: 0,
    },
    640: {
      slidesPerView: 3,
    },
    992: {
      slidesPerView: 6,
      spaceBetween: 20,
    },
  },
});

var swiper = new Swiper(".productGallery", {
  pagination: {
    el: ".swiper-pagination",
  },
  loop: true,
});
var swiper = new Swiper(".hassanSwiperOne", {
  slidesPerView: 4, // Desktop view
  spaceBetween: 20,
  loop: true,
  navigation: {
    nextEl: "#hassan_swiper_one_next_arrow",
    prevEl: "#hassan_swiper_one_prev_arrow",
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    0: { slidesPerView: 1 }, // Mobile
    576: { slidesPerView: 2 }, // Small devices
    768: { slidesPerView: 3 }, // Medium
    992: { slidesPerView: 4 }, // Large
  },
});
document.addEventListener("DOMContentLoaded", function () {
  var swiper = new Swiper(".related-products-swiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      640: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 25,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 30,
      },
      1280: {
        slidesPerView: 5,
        spaceBetween: 30,
      },
    },
  });

  // Single product reviews slider
  var reviewsSwiperEl = document.querySelector(".productReviewsSwiper");
  if (reviewsSwiperEl) {
    var reviewsSwiper = new Swiper(".productReviewsSwiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      pagination: {
        el: ".productReviewsSwiper .swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".productReviewsSwiper .swiper-button-next",
        prevEl: ".productReviewsSwiper .swiper-button-prev",
      },
      breakpoints: {
        768: {
          slidesPerView: 2,
          spaceBetween: 24,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
      },
    });
  }

  // Navigation links swiper - auto-looping slider without arrows or pagination
  var navLinksSwiperEl = document.querySelector(".nav-links-swiper");
  if (navLinksSwiperEl) {
    var navLinksSwiper = new Swiper(".nav-links-swiper", {
      slidesPerView: "auto",
      spaceBetween: 0,
      loop: true,
      autoplay: {
        delay: 2000,
        disableOnInteraction: false,
      },
      speed: 1000,
      allowTouchMove: false,
    });
  }
});
