"use strict";

const pageInit = () => {
	const fullPrice = document.getElementById('fullPrice');
	const partTotal = document.getElementById('part_total');
	
	const btn = document.getElementById('create-order');
	
	if (partTotal) {
		const amount = document.getElementById('amount');
		const comment = document.getElementById('comment');

		[amount, comment].forEach(item => {
			if (item) {
				btn.disabled = true;
				item.addEventListener('blur', (event) => {
					let sum = parseFloat(amount.value) + parseFloat(partTotal.value);
					if (sum === parseFloat(fullPrice.value) && comment.value) {
						btn.disabled = false;
					}		
				});
			}
		});
	}

	setTimeout(paymentDateHandler, 100);
}

const amountCheck = () => {
	const btn = document.getElementById('create-order');
	const amount = document.getElementById('amount');
	const comment = document.getElementById('comment');
	const partTotal = document.getElementById('part_total');
	btn.disabled = true;
	let sum = parseFloat(amount.value) + parseFloat(partTotal.value);
	if (sum === parseFloat(fullPrice.value) && comment.value) {
		btn.disabled = false;
	}
}