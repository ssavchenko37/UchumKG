"use strict";

const pageInit = () => {
	const inputBook = document.getElementById('book_id');
	const fromBranch = document.getElementById('branch_from');
	const toBranch = document.getElementById('branch_to');
	const qtyTransfer = document.getElementById('qty_transfer');

	fromBranch.addEventListener('change', (e) => {
		const selectedOption = fromBranch.options[fromBranch.selectedIndex];
		const max_qty = selectedOption.dataset.send;
		qtyTransfer.setAttribute("max", max_qty); 
		qtyTransfer.setAttribute("placeholder", 'максимально для трансфера: ' + max_qty); 

		const bookID = inputBook.value;
		const branch_from = fromBranch.value;
		const mode = 'transfer'
		fetch('/ajx' + window.location.pathname + 'handlers', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mode, bookID, branch_from})
		})
		.then(res => res.json())
		.then(data => {
			toBranch.innerHTML = '';
			const placeholderOption = document.createElement('option');
			placeholderOption.value = '';
			placeholderOption.text = '--';
			toBranch.appendChild(placeholderOption);
			for (const [id, title] of Object.entries(data)) {
				const newOption = document.createElement('option');
				newOption.value = id;
				newOption.text = title;
				toBranch.appendChild(newOption);
			}
			
		});
	});
}