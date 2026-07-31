/* ============================================================
   PORTFOLIO MAIN JS
   Plain JavaScript - no jQuery needed
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ---------- Navbar shadow on scroll ----------
  var nav = document.querySelector('.main-nav');
  var backToTop = document.querySelector('.back-to-top');
  function onScroll() {
    if (window.scrollY > 40) {
      nav && nav.classList.add('scrolled');
      backToTop && backToTop.classList.add('show');
    } else {
      nav && nav.classList.remove('scrolled');
      backToTop && backToTop.classList.remove('show');
    }
  }
  window.addEventListener('scroll', onScroll);
  onScroll();

  // ---------- Typewriter effect ----------
  var typingEl = document.querySelector('.typing');
  if (typingEl) {
    var words = [];
    try {
      words = JSON.parse(typingEl.getAttribute('data-words') || '[]');
    } catch (err) { words = []; }
    if (!words.length) words = ['Web Developer'];

    var wordIndex = 0, charIndex = 0, deleting = false;
    function type() {
      var current = words[wordIndex];
      typingEl.textContent = current.substring(0, charIndex);
      var delay = deleting ? 60 : 110;

      if (!deleting && charIndex === current.length) {
        delay = 1800;
        deleting = true;
      } else if (deleting && charIndex === 0) {
        deleting = false;
        wordIndex = (wordIndex + 1) % words.length;
        delay = 400;
      }
      charIndex += deleting ? -1 : 1;
      setTimeout(type, delay);
    }
    setTimeout(type, 800);
  }

  // ---------- Animate skill bars on scroll ----------
  var skillBars = document.querySelectorAll('.progress-bar[data-width]');
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.style.width = entry.target.getAttribute('data-width');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.4 });
  skillBars.forEach(function (bar) { observer.observe(bar); });

  // ---------- Project filtering ----------
  var filterButtons = document.querySelectorAll('.filter-btn');
  var projectItems = document.querySelectorAll('.project-item');
  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterButtons.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var filter = btn.getAttribute('data-filter');

      projectItems.forEach(function (item) {
        var show = filter === 'all' || item.getAttribute('data-category') === filter;
        item.style.display = show ? '' : 'none';
      });
    });
  });

  // ---------- Contact form client-side validation styling ----------
  var contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', function () {
      var fields = contactForm.querySelectorAll('[required]');
      fields.forEach(function (f) {
        var isValid = f.checkValidity();
        f.classList.toggle('is-invalid', !isValid);
      });
    });
  }

});
