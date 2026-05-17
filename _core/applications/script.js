"use strict";

const dateHandler = () => {
	const birthday = document.getElementById('stud_birthday');
	var datetime = '';
	if (typeof birthday.value === "string" && birthday.value.length > 0) {
		datetime = birthday.value;
	}
	flatpickr(birthday, {
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		locale: "ru",
		defaultDate: "2000-01-01",
		onReady: function(selectedDates, dateStr, instance) {
			instance.clear(); 
		}
	});
};
docReady(dateHandler);

const duplicatesHandler = () => {
	let timer;
	const box = document.getElementById('duplicateBox');
	const list = box.getElementsByClassName('duplicates__list')[0];
	const close = box.getElementsByClassName('close-abs')[0];
	const input_name = document.getElementById('stud_name');
	const input_phone = document.getElementById('stud_tel');
	
	const checkDuplicates = () => {
		clearTimeout(timer);
		timer = setTimeout(() => {

			const name = input_name.value;
			const phone = input_phone.value;
			document.getElementById('duplicateBox')

			if (name.length < 3 && phone.length < 6) return;

			fetch('/ajx' + window.location.pathname + 'handlers', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ name, phone })
			})
			.then(res => res.json())
			.then(data => {
				if (data.length > 0) {
					let html = '';
					data.forEach(user => {
						html += `<div class="duplicates__item" data-stud="${user.stud_id}">`;
						html += `<div class="duplicates__col">${user.first_name} ${user.last_name} ${user.patronymic}</div>`;
						html += `<div class="duplicates__col">${user.first_hash}</div>`;
						html += `<div class="duplicates__col">${user.phone}</div>`;
						html += `<div class="duplicates__col">${user.birthday}</div>`;
						html += `</div>`;
					});
					box.classList.add('is-active');
					list.innerHTML = html;
				} else {
					box.classList.remove('is-active');
					// closeDuplicates();
				}
			});

		}, 400);
	}

	const closeDuplicates = () => {
		box.classList.remove('is-active');
		list.innerHTML = "";
		input_name.value = "";
		input_phone.value = "";
	}

	list.addEventListener('click', (e) => {
		const item = (e.target.classList.contains('duplicates__item')) ? e.target: e.target.closest('.duplicates__item');
		if (!item) return;
		const cols = item.getElementsByClassName('duplicates__col');
		console.log(cols);
		document.getElementById('stud_id').value = item.dataset.stud;
		document.getElementById('first_hash').value = cols[1].textContent;
		document.getElementById('stud_name').value = cols[0].textContent;
		document.getElementById('stud_tel').value = cols[2].textContent;
		document.getElementById('stud_birthday').value = cols[3].textContent;
		box.classList.remove('is-active');
		list.innerHTML = "";
	});

	document.body.addEventListener('click', (e) => {
		if (!document.getElementById('applicationForm').contains(e.target) && box.classList.contains('is-active')) {
			closeDuplicates();
		}
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && box.classList.contains('is-active')) {
			closeDuplicates();
		}
	});
	close.addEventListener('click', closeDuplicates);
	document.getElementById('stud_name').addEventListener('keyup', checkDuplicates);
	document.getElementById('stud_tel').addEventListener('keyup', checkDuplicates);
}
docReady(duplicatesHandler);