// Swiper JS on About/Company Profile Page
var swiper = new Swiper(".swiper", {
  // Optional parameters
  loop: true,
  freeMode: {
    enabled: true,
    sticky: true,
  },

  autoplay: {
    delay: 3000,
    waitForTransition: true,
  },

  slidesPerView: 2,
  spaceBetween: 10,
  breakpoints: {
    // when window width is >= 320px
    320: {
      slidesPerView: 2,
      spaceBetween: 10,
    },

    // when window width is >= 640px
    640: {
      slidesPerView: 3,
      spaceBetween: 10,
    },

    // when window width is >= 640px
    1000: {
      slidesPerView: 4,
      spaceBetween: 10,
    },
  },

  // If we need pagination
  pagination: {
    el: ".swiper-pagination",
  },
});
