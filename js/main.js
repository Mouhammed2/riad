
// <-- image swiper for room and card swiper -->

document.addEventListener('DOMContentLoaded', function() {

  var swiper = new Swiper(".mySwiper2", {
    slidesPerView: 5,
    spaceBetween: 10,
    freeMode: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
   
    breakpoints: {
   
      320: {
        slidesPerView: 1,
        spaceBetween: 10
      },
    
      480: {
        slidesPerView: 2,
        spaceBetween: 10
      },
 
      768: {
        slidesPerView: 3,
        spaceBetween: 10
      },
      
      992: {
        slidesPerView: 4,
        spaceBetween: 10
      },
    
      1200: {
        slidesPerView: 5,
        spaceBetween: 10
      }
    }
  });
  

  var imageSliders = new Swiper(".mySwiper", {
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});


// avis swiper
