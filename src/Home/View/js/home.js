// Elements
var message = document.getElementById('message');
var modal = document.getElementById('popup');
var modalBlockContent = document.querySelector('#popup > div:first-of-type');
var modalShadow = document.querySelector('#popup > div:last-of-type');
var calendar = document.getElementById('calendar');

// Page initialization
message.style.visibility = 'hidden';
modal.style.display = 'none';

Modal.init(modal, modalBlockContent, modalShadow);
Calendar.init(calendar);

window.addEventListener('resize', function() { Calendar.initCalendarWindows() });
