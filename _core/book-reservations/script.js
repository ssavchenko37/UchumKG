"use strict";

const pageInit = () => {
	const selectBranch = document.getElementById('branch_id');
	const selectBooks = document.getElementById('book_id');
	const inputQty = document.getElementById('qty');
	const inputPrice = document.getElementById('price');
	const inputAmount = document.getElementById('amount');
	const inputNeede = document.getElementById('needed_amount');

	const mode = document.getElementById('mode');
	if (!mode) return;
	
	const findBranches = () => {
		const bookID = selectBooks.value.trim();
		const qtyReserv = inputQty.value.trim();

		if (bookID && bookID.length > 0 && qtyReserv && qtyReserv.length > 0) {
			const mode = 'book-branches';
			fetch('/ajx' + window.location.pathname + 'handlers', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ mode, bookID, qtyReserv })
			})
			.then(res => res.json())
			.then(data => {
				selectBranch.innerHTML = '';
				const placeholderOption = document.createElement('option');
				placeholderOption.value = '';
				placeholderOption.text = '- Доступные филиалы -';
				selectBranch.appendChild(placeholderOption);
				for (const [id, title] of Object.entries(data.branches)) {
					const newOption = document.createElement('option');
					newOption.value = id;
					newOption.text = title;
					selectBranch.appendChild(newOption);
				}
				inputPrice.value = data.book.price
				inputNeede.innerHTML = 'Необходимая сумма: ' + parseInt(qtyReserv) * parseFloat(data.book.price);
			});
		}
	}
	const recalcNeeded = () => {
		const qtyReserv = document.getElementById('qty').value.trim();
		const price = document.getElementById('price').value.trim();
		const partTotal = document.getElementById('part_total').value.trim();
		const inputNeede = document.getElementById('needed_amount');
		inputNeede.innerHTML = parseInt(qtyReserv) * parseFloat(price) - partTotal;
	}

	if ( mode.value === 'add' ) {
		selectBooks.addEventListener('change', findBranches);
		inputQty.addEventListener('blur', findBranches);
	} else {
		inputQty.addEventListener('blur', recalcNeeded);
	}

	const partTotal = document.getElementById('part_total');
	const btn = document.getElementById('create_reserve');
	
	if (btn) {
		const comment = document.getElementById('comment');

		[inputAmount, comment].forEach(item => {
			if (item) {
				item.addEventListener('blur', (event) => {
					if (inputAmount.value.trim().length > 0) {
						if (comment.value.trim().length > 0) {
							btn.disabled = false;
						} else {
							btn.disabled = true;
						}
					} else {
						setTimeout(() => {comment.value = '';}, 600);
						
					}				
				});
			}
		});
	}

	setTimeout(paymentDateHandler, 100);
}