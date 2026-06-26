"use strict";

const getLSArray = (key) => {
	try {
		const data = localStorage.getItem(key);
		const parsed = data ? JSON.parse(data) : [];
		return Array.isArray(parsed) ? parsed : [];
	} catch {
		return [];
	}
}

const setCookie = (name, value, options = {}) => {
	options = { path: '/', ...options };
	
	if (options.expires instanceof Date) {
		options.expires = options.expires.toUTCString();
	}
	let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

	for (let key in options) {
		updatedCookie += "; " + key;
		let val = options[key];
		if (val !== true) {
			updatedCookie += "=" + val;
		}
	}
	
	document.cookie = updatedCookie;
}

const getCookie = (name) => {
	const matches = document.cookie.match(
		new RegExp(
			"(?:^|; )" +
			name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') +
			"=([^;]*)"
		)
	);
	return matches ? decodeURIComponent(matches[1]) : null;
}

const docReady = (fn) => {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', fn);
	} else {
		setTimeout(fn, 1);
	}
};


const navbarVerticalHighlight = () => {
	const nav = document.getElementById('navbarVerticalNav');
	if (nav === null) return
	const nav_links = nav.getElementsByClassName('nav__link');
	if (nav_links.length) {
		for (let item of nav_links) {
			if (item.href.split('/')[3] === pathlast) {
				item.classList.add('active');
			}
		}
	}

}
docReady(navbarVerticalHighlight);

const navbarToggleCollapse = () => {
	const btn = document.getElementById('nav-toggle');
	if (btn === null) return

	const body = document.body;
	const nav = document.getElementsByClassName('navspace')[0];
	let bodyClass = (window.innerWidth > 1023)? "": "nav-collapsed";
	//let bodyClass = "";
	let collapced = localStorage.getItem('navCollapsed');

	if (window.innerWidth > 1023) {
		if (collapced === "true") {
			body.classList.add(bodyClass);
		}
	} 

	body.addEventListener('click', (e) => {
		if (!nav.contains(e.target) && !btn.contains(e.target)) {
			body.classList.remove('nav-shown');
		}
	});

	btn.addEventListener('click', () => {
		if (body.classList.contains(bodyClass)) {
			body.classList.remove(bodyClass);
		} else {
			body.classList.add(bodyClass);
		}
		if (window.innerWidth > 1023) {
			localStorage.setItem('navCollapsed', body.classList.contains(bodyClass));
		}
	});
}
docReady(navbarToggleCollapse);

const infoHandler = () => {
	const info_box = document.getElementById('InfoInTop')
	if (info_box === null) return;
		document.body.addEventListener('click', () => {
		info_box.remove();
	});
}
docReady(infoHandler);

const removeTrHighlight = () => {
	const rows = document.getElementsByClassName('table-success');
	if (rows) {
		for (let tr of rows) {
			if (tr.classList.contains('rws')) {
				tr.addEventListener('mouseenter', (e) => {
					e.target.classList.remove('table-success')
				});
			}
		}
	}
}
docReady(removeTrHighlight);

const imagePreview = () => {
	const ava_wrap = document.getElementsByClassName('ava__file')[0];
	if (ava_wrap) {
		const ava_file = ava_wrap.getElementsByTagName('INPUT')[0];
		ava_file.addEventListener('input', (e) => {
			const imagefile = e.target.files[0];
			var imagetype = imagefile.type;
			var imageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
			if (imageTypes.indexOf(imagetype) == -1) {
				//display error
				e.target.empty();
				return false;
			} else {
				const reader = new FileReader();
				reader.onload = function(e) {
					document.getElementsByClassName('ava__image')[0].getElementsByTagName('IMG')[0].src = e.target.result;
				};
				reader.readAsDataURL(imagefile);
			}

		});
	}
}

const tlasideClose = () => {
	const tlaside = document.getElementById('tlaside');
	if (tlaside === null) return;
	const doClose = () => {
		if (tlaside.classList.contains('show-block1')) {
			if (tlaside.classList.contains('show-block2')) {
				tlaside.classList.remove('show-block2');
			} else {
				tlaside.classList.remove('show-block1');
				setTimeout(() => {
					tlaside.classList.remove('show');
					document.body.classList.remove('no-scroll');
				}, 500);
			}
		}
	}
	tlaside.addEventListener('click', (e) => {
		if (e.target.classList.contains('dismiss-tlaside') || e.target.classList.contains('tlaside__backdrop')) {
			doClose();
		}	
	});
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && tlaside.classList.contains('show-block1')) {
			doClose();
		}
	});
	// if (window.innerWidth <= 1024) {
	// 	const panel = tlaside.getElementsByClassName('tlaside__block')[0];
	// 	let startX = 0
	// 	let currentX = 0
	// 	let isDragging = false

	// 	panel.addEventListener('touchstart', e => {

	// 		startX = e.touches[0].clientX
	// 		isDragging = true

	// 	})

	// 	panel.addEventListener('touchmove', e => {

	// 		if (!isDragging)
	// 			return

	// 		currentX = e.touches[0].clientX

	// 		const diff = currentX - startX

	// 		if (diff > 0) {

	// 			panel.style.transform = `translateX(${diff}px)`

	// 		}

	// 	})

	// 	panel.addEventListener('touchend', () => {

	// 		if (!isDragging)
	// 			return

	// 		isDragging = false

	// 		const diff = currentX - startX

	// 		// если свайпнули достаточно
	// 		if (diff > 120) {
	// 			tlaside.classList.remove('show-block1');
	// 			setTimeout(() => {
	// 				tlaside.classList.remove('show');
	// 				document.body.classList.remove('no-scroll');
	// 			}, 500);
	// 			panel.style.transform = ''
	// 		} else {
	// 			panel.style.transform = ''
	// 		}

	// 	})
	// }
}
const getViewer = (formData) => {
	let pathname = window.location.pathname;
	const tlaside = document.getElementById('tlaside');
	const block1 = document.getElementById('tlaside-one');
	const block2 = document.getElementById('tlaside-two');
	let viewer_flag = tlaside.classList.contains('show-block1')? 2 : 1;
	let tlasideBody;

	if (viewer_flag === 1) {
		tlasideBody = block1.getElementsByClassName('tlaside__body')[0];
		tlaside.classList.add('show-block1');
		tlaside.classList.add('show');
		document.body.classList.add('no-scroll');
	}
	if (viewer_flag === 2) {
		tlasideBody = block2.getElementsByClassName('tlaside__body')[0];
		tlaside.classList.add('show-block2');
	}
	fetch('/ajx' + pathname + formData.get('page'), {
		method: 'POST',
		body: formData
	})
	.then(r => r.text())
	.then(html => {
		if (viewer_flag === 1) {
			tlasideBody.innerHTML = html;
			imagePreview();
		} else {
			tlasideBody.innerHTML = html;
		}

		if (typeof pageInit === 'function') {
			pageInit();
		}
	});
}
const ctrlBtn = () => {
	const workspace = document.getElementById('workspace');
	if (workspace === null) return;
	workspace.addEventListener('click', (e) => {
		let form = document.getElementById("frm0");
		if (!form) return;
		const formData = new FormData(form);
		let btn = e.target.closest('BUTTON');
		if (!btn) return;
		let ctrl = btn.parentNode;
		if (!ctrl.classList.contains('ctrlBtn')) return;
		formData.append('pid', ctrl.dataset.pid);
		for (let btn_key in btn.dataset) {
			formData.append(btn_key, btn.dataset[btn_key]);
		}
		getViewer(formData);
	});
}
docReady(ctrlBtn);
docReady(tlasideClose);


const resetFrom = () => {
	const form = document.getElementById('frm0');
	if (form === null) return;
	form.addEventListener('reset', function(e) {
		e.preventDefault();
		this.querySelectorAll('input[type="text"]').forEach(inp => inp.value = '');
		this.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);
		this.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(el => el.checked = false);
		this.submit();
	});
}
docReady(resetFrom);

const filtersHandler = () => {
	const frm = document.getElementById('frm0');
	const filter_names = [
		"filter_grm",
		"filter_grup",
		"filter_tutor",
		"filter_subject",
		"filter_sem",
		"filter_mdl",
		"filter_module",
		"filter_daterange",
		"filter_plan",
		"filter_code",
		"filter_semester",
		"filter_exams"
	];
	filter_names.forEach((f) => {
		const filter = document.getElementById(f);
		if (filter !== null) {
			filter.addEventListener('input', (e) => {
				frm.submit();
			});
		}
	});
	
	const statuses = document.querySelectorAll('[name="filter_status"]');
	statuses.forEach((s) => {
		s.addEventListener('input', () => {
			frm.submit();
		});
	});
}
docReady(filtersHandler);

const restoreFilters = () => {
	let last;
	const viewButtons = document.getElementsByClassName('get_details');
	if (viewButtons) {
		for(let btn of viewButtons) {
			btn.addEventListener('click', (e) => {
				const data = {};
				document.getElementById('frm0').querySelectorAll('select').forEach(s => data[s.name] = s.value);
				localStorage.setItem('filterValues', JSON.stringify(data));
				localStorage.setItem('getDetails', e.currentTarget.href.split('?')[1]);
			});
		}	
	}

	const savedFilters = localStorage.getItem('filterValues');
	if (savedFilters) {
		const data = JSON.parse(savedFilters);
		document.querySelectorAll('select').forEach(s => {
			if (data[s.name]) {
				s.value = data[s.name];
				if (s.value > 0) {
					last = s;
				}
			}
		});
		if (last !== undefined) {
			last.dispatchEvent(new Event('input', { bubbles: true }));
		}
		localStorage.removeItem('filterValues');
	}

	if (last === undefined) {
		const savedDetails = localStorage.getItem('getDetails');
		if (savedDetails) {
			const details_link = Array.from(viewButtons).find(a => a.href.includes(savedDetails));
			if (details_link) {
				const from_tr = details_link.closest('.rws');
				if (!from_tr) return;
				from_tr.classList.add('table-success');
				from_tr.scrollIntoView({behavior: 'smooth', block: 'center'});
				from_tr.addEventListener('mouseenter', (e) => {
					e.target.classList.remove('table-success');
				});
			}
			localStorage.removeItem('getDetails');
		}
	}
}

const verifyStep = (step) => {
	const requires = step.getElementsByTagName('INPUT');
	let filled = true;

	for (let req of requires) {
		if (req.required) {
			if (req.type === 'radio') {
				const radioGroup = document.querySelectorAll(`input[name="${req.name}"]:checked`);
				if (radioGroup.length === 0) {
					filled = false;
				}
			}
			if (!req.value.trim() && req.type !== 'checkbox') {
				filled = false;
			} else if (req.type === 'checkbox' && !req.checked) {
				filled = false;
			}
			if (req.required && !req.value.trim().length) {
				filled = false;
			}
			if (!filled) {
				req.classList.add('is-invalid');
			}
		}
	}
	return filled;
}
const inputLabelToogle = () => {
	const inputLabels = document.getElementsByClassName('placehold');
	if (!inputLabels) return;
	
	for (let label of document.getElementsByClassName('placehold')) {
		const col = label.closest('DIV');
		const input = col.getElementsByTagName('INPUT')[0];

		if (!col.classList.contains('js-mode')) {
			col.classList.add('js-mode');
		}

		if (input.value.trim().length !== 0) {
			col.classList.add('js-empty');
		}

		input.addEventListener('focus', () => {
			col.classList.remove('js-mode');
			col.classList.remove('js-empty');
			input.classList.remove('is-invalid');
		});
		input.addEventListener('blur', () => {
			if (input.value.trim().length !== 0) {
				col.classList.add('js-empty');
			}
			col.classList.add('js-mode');
		});
	}
}
docReady(inputLabelToogle);

const popupHandler = () => {
	const popups = document.getElementsByClassName('popup');
	const closePopup = (popup) => {
		popup.classList.remove('active');
	}
	
	for (let popup of popups) {
		popup.getElementsByClassName('popup__close')[0].addEventListener('click', () => {
			closePopup(popup);
		});
		
		popup.addEventListener('click', (e) => {
			if (!popup.getElementsByClassName('popup__content')[0].contains(e.target) && 
				!e.target.closest('.child__close') &&
				popup.classList.contains('active')
			) {
				closePopup(popup);
			}
		});
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && popup.classList.contains('active')) {
				closePopup(popup);
			}
		});
	}
};
docReady(popupHandler);

const filterHandlers = () => {
	const by_phone = document.getElementById('by_phone');
	const by_branch = document.getElementById('by_branch');
	const by_available = document.getElementById('by_available');
	const by_tutor = document.getElementById('by_tutor');
	const by_delivery = document.getElementById('by_delivery');
	
	const rows = document.getElementsByClassName('rws');
	
	if (!rows) return;

	if (by_tutor) {
		by_tutor.addEventListener('change', (e) => {
			if (e.target.value === '0') {
				for(let row of rows) {
					row.style.display = "";
				}
				return;
			}
			for(let row of rows) {
				if (e.target.value === row.dataset.tutor) {
					row.style.display = "";
				} else {
					row.style.display = "none";
				}
			}
		});
	}

	if (by_delivery) {
		by_delivery.addEventListener('change', (e) => {
			if (e.target.value === '0') {
				for(let row of rows) {
					row.style.display = "";
				}
				return;
			}
			for(let row of rows) {
				if (e.target.value === row.dataset.delivery) {
					row.style.display = "";
				} else {
					row.style.display = "none";
				}
			}
		});
	}

	if (by_available) {
		by_available.addEventListener('change', (e) => {
			if (e.target.value === '0') {
				for(let row of rows) {
					row.style.display = "";
				}
				return;
			}
			for(let row of rows) {
				if (e.target.value === row.dataset.shortage) {
					row.style.display = "";
				} else {
					row.style.display = "none";
				}
			}
		});
	}

	if (by_branch) {
		by_branch.addEventListener('change', (e) => {
			if (e.target.value === '0') {
				for(let row of rows) {
					row.style.display = "";
				}
				return;
			}
			for(let row of rows) {
				if (e.target.value === row.dataset.branch) {
					row.style.display = "";
				} else {
					row.style.display = "none";
				}
			}
		});
	}

	if (by_phone) {
		by_phone.addEventListener('input', (e) => {
			const searchText = e.target.value;
			if (e.target.value.length >= 3) {
				for(let row of rows) {
					if (
						row.dataset.phone.toLowerCase().includes(searchText.toLowerCase()) ||
						row.dataset.address.toLowerCase().includes(searchText.toLowerCase())
					) {
						row.style.display = "";
					} else {
						row.style.display = "none";
					}
				}
			} else {
				for(let row of rows) {
					row.style.display = "";
				}
			}
		});
	}
}
docReady(filterHandlers);


const searchBY = () => {
	const by_phone = document.getElementById('by_phone');
	console.log(by_phone);
	if (by_phone) {
		const cells = document.getElementsByClassName('filter_phone');
		by_phone.addEventListener('input', (e) => {
			const searchText = e.target.value;
			if (e.target.value.length >= 3) {
				for(let cell of cells) {
					const row = cell.closest('tr');
					if (cell.textContent.toLowerCase().includes(searchText.toLowerCase())) {
						row.style.display = "";
					} else {
						row.style.display = "none";
					}
				}
			} else {
				for(let cell of cells) {
					const row = cell.closest('tr');
					row.style.display = "";
				}
			}
		});
	}
}
//docReady(searchBY);

const verifyPayment = (dateStr) => {
	const verify_elm = document.getElementById('verify_duplicate');
	const has_elm = document.getElementById('has_duplicate');
	const entry = document.getElementById('duplicate_entry');

	verify_elm.style.display = "block";
	has_elm.style.display = "none";
	entry.innerHTML = '';

	const modalEl = document.getElementById('datesMatchModal');
	const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

	verify_elm.style.display = "block";
	has_elm.style.display = "none";

	modal.show();

	const mode = 'date-verification';
	fetch('/ajx' + window.location.pathname + 'handlers', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ mode, dateStr })
	})
	.then(res => res.json())
	.then(data => {
		if (Object.keys(data).length > 0) {
			verify_elm.style.display = "none";
			has_elm.style.display = "block";
			let row = '';
			data.forEach((item)=> {
				row += `
				<div class="exist-row pb-3">
					Дата: ${item.comment}<br>
					Телефон: ${item.phone}<br>
					Сумма: ${item.amount}<br>
					Адрес: ${item.delivery_to}<br>
					Статус: ${item.status}
				</div>
				`.trim();
				if (item.part_amount) {
					row += `
					<div class="exist-row pb-3">
						Частичный платеж:<br>
						Дата: ${item.part_comment}<br>
						Телефон: ${item.phone}<br>
						Сумма: ${item.part_amount}<br>
					</div>
					`.trim();
				}
			});
			entry.innerHTML = row;
		} else {
			modalEl.addEventListener('shown.bs.modal', () => {
				setTimeout(() => {
					if (modalEl.contains(document.activeElement)) {
						document.activeElement.blur();
					}
					modal.hide();
				}, 400);
			}, { once: true });
		}
	});
}

const paymentDateHandler = () => {
	const paydate = document.getElementById('comment');
	if (!paydate) return;
	let datetime = '';
	if (typeof paydate.value === "string" && paydate.value.length > 0) {
		datetime = paydate.value;
	}

	flatpickr(paydate, {
		allowInput: true,
		enableTime: true,
		time_24hr: true,
		dateFormat: "Y-m-d H:i",
		locale: "ru",
		defaultDate: datetime,
		minuteIncrement: 1,
		onClose: function(selectedDates, dateStr, instance) {
			if (dateStr) {
				verifyPayment(dateStr);
				if (typeof amountCheck === 'function') {
					amountCheck();
				}
			}
		}
	});
};

const mobileTableMode = () => {
	const buttons = document.querySelectorAll('.view-btn')
	const btable = document.getElementsByClassName('books-table')[0]

	if (!btable) return;

	buttons.forEach(button => {
		button.addEventListener('click', () => {
			const view = button.dataset.view
			// buttons
			buttons.forEach(btn => {
				btn.classList.remove('is-active')
			})
			button.classList.add('is-active')
			// content
			console.log(view);
			if (view === "table") {
				btable.classList.remove('books-table')
			} else {
				btable.classList.add('books-table')
			}
		})
	})
}
docReady(mobileTableMode);