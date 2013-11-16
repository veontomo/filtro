$(document).ready(function(){
	var datePickerFormat = {"locale": "it", "todayButton": true, 
		"dateFormat": "YYYY-MM-DD hh:mm", "dateOnly": false, "firstDayOfWeek": 1};

	$('*[name=timeMin]').appendDtpicker(datePickerFormat);
	$('*[name=timeMax]').appendDtpicker(datePickerFormat);

	$('#timeMin, #timeMax').css('background-color', 'rgba(255, 232, 190, 20)');


});