"use strict";

const dateHandler = () => {
	const birthday = document.getElementById('birthday');
	if (!birthday) return;
	var datetime = '';
	if (typeof birthday.value === "string" && birthday.value.length > 0) {
		datetime = birthday.value;
	}
	flatpickr(birthday, {
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		locale: "ru",
	});
};

const pageInit = () => {
	dateHandler();
}
