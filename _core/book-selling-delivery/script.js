"use strict";

const pageInit = () => {
	const surcharge = document.getElementById('surcharge');
	const btn = document.getElementById('create-order');
	
	if (surcharge) {
		const cost = document.getElementById('cost');
		const amount = document.getElementById('amount');

		surcharge.addEventListener('input', (e) => {
			const needed = parseFloat(cost.value);
			const entered = parseFloat(amount.value) + parseFloat(e.target.value);
			if (entered >= needed) {
				btn.disabled = false;
			} else {
				btn.disabled = true;
			}
		});
	}

	setTimeout(paymentDateHandler, 100);
}