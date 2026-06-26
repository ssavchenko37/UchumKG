"use strict";

const findHandlers = () => {
	let timer;
	const box = document.getElementById('duplicateBox');
	const list = box.getElementsByClassName('duplicates__list')[0];
	const close = box.getElementsByClassName('close-abs')[0];
	const input_phone = document.getElementById('phone');
	const findApplication = (e) => {
		const phone = input_phone.value;

		clearTimeout(timer);
		timer = setTimeout(() => {
			if (phone.length < 5) return;

			fetch('/ajx/_pub' + window.location.pathname + 'find', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ phone })
			})
			.then(res => res.json())
			.then(data => {
				if (data.length > 0) {
					let html = '';
					data.forEach(user => {
						html += `<div class="duplicates__item" data-stud="${user.stud_id}" data-who="${user.whowill}">`;
						html += `<div class="duplicates__col">${user.last_name} ${user.first_name}</div>`;
						html += `<div class="duplicates__col">${user.phone}</div>`;
						html += `</div>`;
					});
					list.innerHTML = '';
					box.classList.add('is-active');
					list.innerHTML = html;
				} else {
					box.classList.remove('is-active');
				}
			});

		}, 400);

	}

	const closeDuplicates = () => {
		box.classList.remove('is-active');
		list.innerHTML = "";
		input_phone.value = "";
	}

	list.addEventListener('click', (e) => {
		const item = (e.target.classList.contains('duplicates__item')) ? e.target: e.target.closest('.duplicates__item');
		if (!item) return;
		const cols = item.getElementsByClassName('duplicates__col');

		document.getElementById('stud_id').value = item.dataset.stud;
		document.getElementById('phone').value = cols[1].textContent;
		let name = cols[0].textContent.split(" ");
		console.log(item.dataset.who);
		let lastName, firstName
		if (item.dataset.who === 'child') {
			document.getElementById('add_child').click();
			lastName = document.querySelectorAll('[id^="child_last_name"]')[1];
			firstName = document.querySelectorAll('[id^="child_first_name"]')[1];
		} else {
			lastName = document.getElementById('last_name');
			firstName = document.getElementById('first_name')
		}
		console.log(lastName);
		console.log(firstName);
		lastName.value = name[0];
		lastName.focus();
		lastName.blur();
		firstName.value = name[1];
		firstName.focus();
		firstName.blur();
		
		box.classList.remove('is-active');
	});

	close.addEventListener('click', closeDuplicates);
	document.body.addEventListener('click', (e) => {
		if (!document.getElementById('offer_form').contains(e.target) && box.classList.contains('is-active')) {
			closeDuplicates();
		}
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && box.classList.contains('is-active')) {
			closeDuplicates();
		}
	});

	document.getElementById('phone').addEventListener('keyup', findApplication);
}
docReady(findHandlers);

const calcAge = (dateString) => {
	const birthDate = new Date(dateString);
	const today = new Date();
	let age = today.getFullYear() - birthDate.getFullYear();
	const m = today.getMonth() - birthDate.getMonth();
	if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
		age--;
	}
	return age;
}
const getAge = () => {
	const age_inputs = document.getElementsByClassName('calc-age');
	if (!age_inputs.length) return;
	for (let age_input of age_inputs) {
		const res_input = age_input.closest('.popup__row').getElementsByClassName('form-control-age')[0];
		age_input.addEventListener('input', () => {
			res_input.value = calcAge(age_input.value)
			age_input.blur();
		});
	}
}

const dateHandler = () => {
	const birthday = document.getElementById('birthday');
	var datetime = '';
	if (typeof birthday.value === "string" && birthday.value.length > 0) {
		datetime = birthday.value;
	}
	flatpickr(birthday, {
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		locale: "ru",
		defaultDate: "1990-01-01",
		onReady: function(selectedDates, dateStr, instance) {
			instance.clear(); 
		}
	});
}
docReady(dateHandler);

const childDate = (birthday) => {
	var datetime = '';
	if (typeof birthday.value === "string" && birthday.value.length > 0) {
		datetime = birthday.value;
	}
	flatpickr(birthday, {
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		locale: "ru",
		defaultDate: "2010-01-01",
		onReady: function(selectedDates, dateStr, instance) {
			instance.clear(); 
		}
	});
}

const childHandler = () => {
	let index = 1;
	const child_area = document.getElementById('child_area');
	const template = document.getElementById('child_block_temp');
	const add_child = document.getElementById('add_child');

	add_child.addEventListener('click', (e) => {
		const clone = template.cloneNode(true);
		clone.classList.remove('child--template')
		clone.id = "child" + index;

		clone.querySelectorAll('[id]').forEach(el => {
			el.id = el.id.replace(/\d+/, index);
		});
		clone.querySelectorAll('label[for]').forEach(el => {
			el.htmlFor = el.htmlFor.replace(/\d+/, index);
		});
		child_area.appendChild(clone);

		const elements = clone.getElementsByTagName('INPUT');
		for (let elm of elements) {
			if (!elm.disabled) {
				elm.required = true;
			}
		}

		setTimeout(() => {
			childDate(clone.getElementsByClassName('child-birthday')[0]);
			inputLabelToogle();
			getAge();
		}, 100)
		index++;
	});
	
	child_area.addEventListener('click', (e) => {
		if (e.target.closest('.child__close')) {
			e.target.closest('.child').remove();
		}
	});
}
docReady(childHandler);

const offerHandler = () => {
	const btn = document.getElementById('offer_form_btn');
	const popup = document.getElementById('popup_offer');
	if (btn.length === 0) return;

	btn.addEventListener('click', (e) => {
		popup.classList.add('active');
		getAge();
	});
}
docReady(offerHandler);

const signHandler = () => {
	const btn = document.getElementById('sign_agreement');
	const popup = document.getElementById('popup_offer');
	const res_popup = document.getElementById('popup_offer_result');
	const res_title = res_popup.getElementsByClassName('h4')[0];
	const res_body = res_popup.getElementsByClassName('popup__body')[0];
	let pp_title, pp_body;
	btn.addEventListener('click', (e) => {
		if (verifyStep(document.getElementById('offer_form'))) {
			
			const formData = new FormData(document.getElementById('offer_form'));
			const dataSend = Object.fromEntries(formData.entries());
			dataSend['childfirst_arr'] = formData.getAll('child_first[]');
			dataSend['childlast_arr'] = formData.getAll('child_last[]');
			dataSend['child_birthday_arr'] = formData.getAll('child_birthday[]');

			fetch('/ajx/_pub' + window.location.pathname + 'offer-handler', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json; charset=utf-8' },
				body: JSON.stringify(dataSend)
			})
			.then(res => res.json())
			.then(data => {
				if (data.status === "signing") {
					res_title.innerHTML = "Оферта подписана";
					const child_txt = (data.children === 1)
						? `<p>Также в личном кабинете вы сможете определить доступ для детей</p>`
						: ""
					;
					const pp_body = `
						<div class="user-card">
							<p>Пользователь <strong>${data.first_name} ${data.last_name}</strong></p>
							<p>Оферта успешно подписана, теперь вы можете войти в ваш <a href="/${window.location.hostname}/login/">личный кабинет</a>, для входа используйте ваш номер телефона.</p>
							${child_txt}
						</div>
					`;
					res_body.innerHTML = pp_body;
				}
				if (data.status === "signed") {
					res_title.innerHTML = "Оферта уже подписывалась";
					const pp_body = `
						<div class="user-card">
							<p>Оферта уже подписывалась вами</p>
							<p>Пользователь <strong>${data.first_name} ${data.last_name}</strong><br>
							Оферта подписана <strong>${data.sign_date}</strong></p>
							<p>Вы можете войте в ваш <a href="/login/">личный кабинет</a>, для входа используйте ваш номер телефона.</p>
						</div>
					`;
					res_body.innerHTML = pp_body;
				}
				if (data.status === "impossible") {
					res_title.innerHTML = "Ваша оферта не готова";
					const pp_body = `
						<div class="user-card">
							<p>В настоящий момент вы не можете подписать оферту, ваша заявка еще не рассмотренна или вы еще не подавали заявку.</p>
						</div>
					`;
					res_body.innerHTML = pp_body;
				}
				popup.classList.remove('active');
				res_popup.classList.add('active');
			});
		}

	});
}
docReady(signHandler);
