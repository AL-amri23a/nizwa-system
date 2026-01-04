// تغيير الاتجاه تلقائياً عند تبديل اللغة
document.addEventListener('DOMContentLoaded', function () {
  const html = document.querySelector('html');
  if (window.location.href.includes('lang=ar')) {
    html.setAttribute('dir', 'rtl');
  } else {
    html.setAttribute('dir', 'ltr');
  }
});

// إضافة تأثيرات لطيفة للأزرار
$(document).ready(function() {
  $('button').hover(function() {
    $(this).addClass('shadow-lg');
  }, function() {
    $(this).removeClass('shadow-lg');
  });
});

// small helpers
document.addEventListener('DOMContentLoaded', ()=> {
  // auto focus first field in forms
  const firstInput = document.querySelector('form input');
  if (firstInput) firstInput.focus();
});

document.addEventListener("DOMContentLoaded", () => {
  console.log("Training System Loaded ✅");
});
