let slides = document.querySelectorAll('.hero-banner .slides img');
let index = 0;

slides[index].classList.add('active');

setInterval(() => {
  slides[index].classList.remove('active');
  index = (index + 1) % slides.length;
  slides[index].classList.add('active');
}, 3000); // هر 3 ثانیه تغییر تصویر