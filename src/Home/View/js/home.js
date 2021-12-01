// Elements
var message = document.getElementById('message');
var modal = document.getElementById('popup');
var calendar = document.getElementById('calendar');

// Page initialization
message.style.visibility = 'hidden';
modal.style.display = 'none';

Modal.init(modal);
Calendar.init(calendar);

window.addEventListener('resize', function() { Calendar.initCalendarWindows() });
