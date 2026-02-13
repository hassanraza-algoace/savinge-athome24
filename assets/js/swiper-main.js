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
      slidesPerView: 1.5,
      centeredSlides: true,
      spaceBetween: 15,
    },
    640: {
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    1440: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
    2560: {
      slidesPerView: 6,
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
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    1280: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
    1440: {
      slidesPerView: 5,
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
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    1280: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
    1440: {
      slidesPerView: 5,
      spaceBetween: 20,
    },
  },
});

var swiperposts = new Swiper(".brandSwiper", {
  loop: true,


  slidesPerGroup: 4, // 4 slides ek sath move hongi

  navigation: {
    nextEl: "#product-brand-swiper-button-next",
    prevEl: "#product-brand-swiper-button-prev",
  },

  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

  breakpoints: {
    0: {
      slidesPerView: 2,
      slidesPerGroup: 2,
      spaceBetween: 10,
    },
    640: {
      slidesPerView: 4,
      slidesPerGroup: 4,
      spaceBetween: 15,
    },
    992: {
      slidesPerView: 6,
      slidesPerGroup: 4, // yahan bhi 4 scroll honge
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
  draggable: true,
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
    0: {
      slidesPerView: 1.5,
      centeredSlides: true,
      spaceBetween: 15,
    },
    640: {
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    1440: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
    2560: {
      slidesPerView: 6,
      spaceBetween: 20,
    },
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
      0: {
        slidesPerView: 1,
      },
      640: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      1440: {
        slidesPerView: 5,
        spaceBetween: 20,
      },
    },
  })
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
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      1440: {
        slidesPerView: 5,
        spaceBetween: 20,
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
    loop: false,
    allowTouchMove: true,
  });
}
