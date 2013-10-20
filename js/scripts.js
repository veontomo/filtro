$(document).ready(function(){
	var datePickerFormat = {"locale": "it", "todayButton": true, "dateFormat": "YY-MM-DD hh:mm", "dateOnly": false, "firstDayOfWeek": 1};
	$('*[name=timeMin]').appendDtpicker(datePickerFormat);
	$('*[name=timeMax]').appendDtpicker(datePickerFormat);


});