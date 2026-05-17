"use strict";

const dateHandler = () => {
	const created = document.getElementById('created');
	if (!created) return;
	const startime = document.getElementById('startime');

	let dategroup = '';
	let timegroup = '';
	if (typeof created.value === "string" && created.value.length > 0) {
		dategroup = created.value;
	}
	flatpickr(created, {
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		locale: "ru",
	});

	if (typeof startime.value === "string" && startime.value.length > 0) {
		timegroup = startime.value;
	}
	flatpickr(startime, {
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		defaultDate: timegroup,
		time_24hr: true
	});
};

const codeGenerator = () => {
	const hash_box = document.getElementById('sess_hash'); 
	if (!hash_box) return;
	const created_box = document.getElementById('created'); 
	const startime = document.getElementById('startime');

	const checkVerifying = () => {
		let is = false;
		['created', 'startime', 'tutor_id', 'schedule_id', 'format_id', 'age_id', 'address_id'].forEach(id => {
			const val = document.getElementById(id).value;
			is = (val === '') ? false: true;
		});
		if (is) {
			document.getElementById('group_btn').disabled = false;
		}
	}

	const buildCode = () => {
		const tutor_id = document.getElementById('tutor_id').value;
		const schedule_id = document.getElementById('schedule_id').value;
		const age_id = document.getElementById('age_id').value;
		const format_id = document.getElementById('format_id').value;

		let date_path = 0;
		let time_path = 0;
		if (created_box.value !== "") {
			const [y, m, d] = created_box.value.split('-');
			date_path = `${d}${m}${y.slice(-2)}`;
		}
		if (startime.value !== "") {
			const [h, i] = startime.value.split(':');
			time_path = `${h}${i}`;
		}
		const dataSend = {
			mode: 'build_code',
			date_path: date_path,
			time_path: time_path,
			format_id: format_id,
			age_id: age_id,
			tutor_id: tutor_id,
			schedule_id: schedule_id
		};
		fetch('/ajx' + window.location.pathname + 'handlers', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(dataSend)
		})
		.then(response => response.json())
		.then(data => {
			if (data.status === "success") {
				hash_box.value = data.code;
				checkVerifying();
			}
			
		})
		.catch(error => console.error('Ошибка:', error));
	}
	created_box.addEventListener('input', () => {
		buildCode();
	});
	startime.addEventListener('input', () => {
		buildCode();
	});
	document.getElementById('address_id').addEventListener('change', function () {
		checkVerifying();
	});
	['tutor_id', 'schedule_id', 'format_id', 'age_id'].forEach(id => {
		document.getElementById(id).addEventListener('change', function () {
			buildCode();
		});
	});
	checkVerifying();
}

const pageInit = () => {
	dateHandler();
	codeGenerator();
}