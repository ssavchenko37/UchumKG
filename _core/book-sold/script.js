"use strict";

const pageInit = () => {
	const btn = document.getElementById('cancel_sale');
	const modalElm = document.getElementById('cancelSaleModal');

	btn.addEventListener('click', (e) => {
		const modal = bootstrap.Modal.getOrCreateInstance(modalElm);
		modal.show();
	});
}

for(let elm of document.getElementsByClassName('status_filter')) {
	elm.getElementsByTagName('SELECT')[0].addEventListener('change', (e) => {
		document.getElementById('frm0').submit();
	});
}