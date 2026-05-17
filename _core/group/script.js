"use strict"

if (window.innerWidth > 1023) {
	document.body.classList.add('nav-collapsed');
}


const iBookCtrl = () => {
	const group_id = document.getElementById('group_id').value;
	const enteredHandler = (col) => {
		const coldate = col.getElementsByTagName('INPUT')[0];
		const colitems = document.querySelectorAll('span[data-send="' + coldate.id + '"]');
		const colalt = col.getElementsByClassName('entered-alt')[0];
		const meta_id = col.dataset.meta_id;
		const dataSend = { group_id: group_id, meta_id: meta_id, meta_date: coldate.value, meta_uin: coldate.id }
		fetch('/ajx' + window.location.pathname + 'meta-entered', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(dataSend)
		})
		.then(response => response.json())
		.then(data => {
			console.log(data);
			col.dataset.meta_id = data.meta_id;
			colalt.innerHTML = `<span>${data.monthday}</span><small>${data.hoursec}</small>`;
			colitems.forEach((itm) => {
				if (itm.dataset.field !== undefined) {
					const b = itm.getElementsByTagName('B')[0];
					itm.closest('td').dataset.meta_id = data.meta_id;
					if (itm.dataset.field === "meta_class") {
						b.innerHTML = data.meta_class;
					}
					if (itm.dataset.field === "meta_hours") {
						b.innerHTML = data.meta_hours;
					}
				}
			});
		})
		.catch(error => console.error('Ошибка:', error));
	}
	const dateHandler = () => {
		const entered = document.getElementsByClassName('entered');
		for (let col of entered) {
			const coldate = col.getElementsByTagName('INPUT')[0];
			let datetime = '';
			if (typeof coldate.value === "string" && coldate.value.length > 0) {
				datetime = coldate.value;
			} else {
				const d = new Date();
				datetime =
					d.getFullYear() + '-' +
					String(d.getMonth()+1).padStart(2,'0') + '-' +
					String(d.getDate()).padStart(2,'0') + ' ' +
					'10:00';
			}
			flatpickr(coldate, {
				allowInput: true,
				enableTime: true,
				enableSeconds: false,
				minuteIncrement: 5,
				dateFormat: "Y-m-d H:i",
				defaultDate: datetime,
				locale: "ru",
				onChange: function() {
					enteredHandler(col);
				}
			});
			const fp = coldate._flatpickr;

			document.addEventListener("keydown", (e) => {
				if (e.key === "Escape") {
					if (fp.isOpen) fp.close();
				}
			});
		}
	};

	const editedHandler = () => {
		const editable = document.getElementsByClassName('edited');
		for (let col of editable) {
			col.addEventListener('click', (e) => {
				if (e.target.tagName !== "B") return;
				const elB = e.target;
				const tr = elB.closest('tr');
				const td = elB.closest('td');
				const span = elB.closest('span');                
				const entered = document.getElementById(span.dataset.send);
				if (entered.value === '') {
					entered._flatpickr.open();
					return;
				}

				const stud_id = tr.dataset.send;
				let val = elB.innerText;
				const abs = document.createElement('DIV');
				abs.className = 'abs';
				const input = document.createElement('input');
				input.type = 'text';
				input.name = 'e';
				input.value = val;
				span.replaceChildren(input);
				input.focus();
				input.addEventListener('blur', (e) =>{
					let newVal = e.target.value;
					let dataSend = {};
					
					if (newVal !== val) { 
						if (td.classList.contains('edited_val')) {
							dataSend = { group_id: group_id, mode: 'edited_val', 'rate': newVal, item_uin: span.dataset.send, stud_id: stud_id, item_id: td.dataset.item_id}
						}
						if (td.classList.contains('edited_meta')) {
							dataSend = { group_id: group_id, mode: 'edited_meta', 'rate': newVal, meta_id: td.dataset.meta_id, field: span.dataset.field}
						}
						fetch('/ajx' + window.location.pathname + 'save-edited', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json'
							},
							body: JSON.stringify(dataSend)
						})
						.then(response => response.text())
						.then(text => {
							const data = JSON.parse(text);
							if (data.status === 'success') {
								if (td.classList.contains('edited_val')) {
									if (data.item_id !== undefined) {
										td.dataset.item_id = data.item_id;
									}
								}
								span.innerHTML = '<b>' + data.value + '</b>';
							} else {
								span.innerHTML = '<b>&nbsp;</b>';
							}
						})
						.catch(error => {
							console.error('There was a problem with the fetch operation:', error);
						});
					} else {
						span.innerHTML = '<b>' + val +'</b>';
					}
					setTimeout(() => {
						abs.remove();
					}, 300);
				});
				if (td.classList.contains('edited_val')) {
					td.appendChild(abs);
					td.addEventListener('click', (e) => {
						if (e.target.className !== "abs") return;
						const dataSend = { group_id: group_id, mode: 'edited_val', 'rate': 'нб', item_uin: span.dataset.send, stud_id: stud_id, item_id: td.dataset.item_id}
						fetch('/ajx' + window.location.pathname + 'save-edited', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json'
							},
							body: JSON.stringify(dataSend)
						})
						.then(response => response.json())
						.then(data => {
							if (data.status === 'success') {
								if (td.classList.contains('edited_val')) {
									td.dataset.item_id = data.item_id;
									td.className = 'align-middle text-center bg-abs';
									td.innerHTML = data.value;
								}						
							} else {
								span.innerHTML = '<b>&nbsp;</b>';
							}
							abs.remove();
						})
					});
				}
			});
		}
	}

	const lessonСancel = () => {
		const buttons = document.getElementsByClassName('meta-cancel')[0].getElementsByTagName('BUTTON');
		buttons
		for (let btn of buttons) {
			btn.addEventListener('click', (e) => {
				const modal = new bootstrap.Modal(document.getElementById('lessonСancelModal'));
				const meta_id = btn.closest('td').dataset.meta_id;
				document.getElementById('meta_id').value = meta_id;
				modal.toggle();
			})
		}
	}
	dateHandler();
	editedHandler();
	lessonСancel();
}

docReady(iBookCtrl);