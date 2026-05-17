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

const tabsHandler = () => {
	const tabs = document.getElementById('group_type_tab');
	const tabs_btn = tabs.getElementsByClassName('btn');
	const panes = document.getElementById('group_type_pane');
	const groups = panes.getElementsByClassName('group');

	const clearActiveClass = () => {
		const btn_activite = tabs.getElementsByClassName('active');
		for (let act of btn_activite) {
			act.classList.remove('active');
		}
	}
	const buildList = (btn_type) => {
		const btn_activite = tabs.getElementsByClassName('active');
		for (let group of groups) {
			if (btn_type === 'all') {
				group.classList.remove('hidden');
			} else {
				group.classList.add('hidden');
				if (group.dataset.groupType === btn_type) {
					group.classList.remove('hidden');
				}
			}
		}
	}

	for (let btn of tabs_btn) {
		btn.addEventListener('click', (e) => {
			const btn_type = btn.dataset.tabType;
			if (!btn.classList.contains('active')) {
				clearActiveClass();
				buildList(btn_type);
				btn.classList.add('active');
			}
			
		});
	}
}
docReady(tabsHandler);

const signupHandler = () => {
	const btns = document.getElementsByClassName('pp-sign');
	if (btns.length === 0) return;

	const popup = document.getElementById('popup_sign');
	const popup_title = popup.getElementsByClassName('popup__title')[0].getElementsByClassName('h4')[0];
	const form = document.getElementById('order_form');
	const steps_line = popup.getElementsByClassName('popup__steps-led')[0];
	const steps = popup.getElementsByClassName('popup__step');

	for (let btn of btns) {
		btn.addEventListener('click', (e) => {
			const lead_init = popup.getElementsByClassName('lead-init')[0];
			const lead_preview = popup.getElementsByClassName('lead-preview')[0];
			const age_input = document.getElementById('age');
			const age_label = age_input.closest('div').getElementsByClassName('placehold')[0];
			let groupType;

			form.reset();

			const group_id = (btn.dataset.groupId === undefined) ? 0: parseInt(btn.dataset.groupId);
			document.getElementById('group_id').value = group_id;
			gotoStep(1);
			
			if (group_id > 0) {
				popup_title.innerText = 'Запись в группу';
				const item = btn.closest('.groups-item');
				groupType = item.dataset.groupType;
				const groupPreviewHTML = item.getElementsByClassName('hidden-preview')[0].innerHTML;

				steps_line.style.display = 'none';
				lead_init.style.display = 'none';
				lead_preview.innerHTML = groupPreviewHTML;
				lead_preview.style.display = 'block';
				if (groupType === 'child') {
					age_input.min = "9";
					age_input.max = "16";
					age_label.innerHTML = "Возраст (9-16 лет)";
				} else {
					age_input.min = "16";
					age_input.removeAttribute('max');
					age_label.innerHTML = "Возраст (от 16 лет)";
				}
			} else {
				popup_title.innerText = 'Подбор группы';
				steps_line.style.display = 'flex';
				lead_preview.innerHTML = '';
				lead_preview.style.display = 'none';
				lead_init.style.display = 'block';	
				
				age_input.min = "9";
				age_input.removeAttribute('max');
				age_label.innerHTML = "Возраст";
			}

			popup.classList.add('active');
		});
	}
}
docReady(signupHandler);

const gotoStep = (n) => {
	const steps = document.getElementById('popup_sign').getElementsByClassName('popup__step');
	for (let step of steps) {
		if (parseInt(step.dataset.step) === n) {
			step.classList.add('active');
		} else {
			step.classList.remove('active');
		}
	}
	const ppleds = document.getElementById('step_leds').getElementsByTagName('DIV');
	for (let led of ppleds) {
		if (parseInt(led.dataset.led) === n) {
			led.classList.add('progress');
		}
	}
}

const stepsHandler = () => {
	const popup = document.getElementById('popup_sign');
	if (!popup) return;

	const form = document.getElementById('order_form');
	const steps = popup.getElementsByClassName('popup__step');

	for (let empty of form.getElementsByClassName('js-empty')) {
		setTimeout(() => {
			empty.classList.remove('js-empty');
		}, 100);
	}
	
	for (let step of steps) {
		const btn = step.getElementsByClassName('popup__footer')[0].getElementsByTagName('BUTTON')[0];

		btn.addEventListener('click', () => {
			const group_id = parseInt(document.getElementById('group_id').value);
			const curr_n = parseInt(step.dataset.step);
			let next_n = curr_n + 1;
			const next = document.getElementById('pp-step' + next_n);

			if (curr_n === 1) {
				if (group_id > 0) {
					next_n = next_n + 1;
				}
			}

			if (curr_n === 4) {
				const formData = new FormData(document.getElementById('order_form'));
				const dataSend = Object.fromEntries(formData.entries());
				dataSend['daysweek_arr'] = formData.getAll('daysweek[]');
				dataSend['coursetime_arr'] = formData.getAll('coursetime[]');

				fetch('/ajx/_pub' + window.location.pathname + 'application-handler', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json; charset=utf-8' },
					body: JSON.stringify(dataSend)
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === "success") {
						gotoStep(next_n);
						let tmpArr = getCookie('alreadyGroups');
						let alreadyGroups = tmpArr ? JSON.parse(tmpArr) : [];
						if (!alreadyGroups.includes(group_id)) { 
							alreadyGroups.push(group_id);
						}
						setCookie('alreadyGroups', JSON.stringify(alreadyGroups), {maxAge: 3600});
						appliedGroups();
					}
				});
				return;
			}
			if (curr_n === 5) {
				popup.classList.remove('active');
				step.classList.remove('active');
				return;
			}
			if (verifyStep(step)) {
				gotoStep(next_n);
			}
		});
	}

}
docReady(stepsHandler);

const appliedGroups = () => {
	const arr = getCookie('alreadyGroups');
	const alreadyGroups = arr ? JSON.parse(arr) : [];
	const btns = document.getElementsByClassName('pp-sign');
	for (let btn of btns) {
		const gid = parseInt(btn.dataset.groupId);
		if (alreadyGroups.includes(gid)) {
			const item = btn.closest('.groups-item');
			if (item) {
				if (!item.classList.contains('applied-group')) {
					item.classList.add('applied-group');
				}
			}
		}
	}
	
}
docReady(appliedGroups);