"use strict";

const calcAmount = () => {
	const inputQty = document.getElementById("qty");
	const inputPrice = document.getElementById("price");
	const inputAmount = document.getElementById("amount");
	inputAmount.value = (inputQty.value * inputPrice.value).toFixed(2);
}
const sellingHandlenrs = () => {
	const selectBranch = document.getElementById('branch_id');
	const selectBooks = document.getElementById('book_id');
	selectBranch.addEventListener('change', (event) => {
		const branchID = event.target.value;
		const mode = 'get-books'
		fetch('/ajx' + window.location.pathname + 'handlers', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mode, branchID })
		})
		.then(res => res.json())
		.then(data => {
			if (Object.keys(data).length > 1) {
				const placeholderOption = document.createElement('option');
				placeholderOption.value = '';
				placeholderOption.text = 'Выбирите книгу';
				selectBooks.appendChild(placeholderOption);
			}
			for (const [id, title] of Object.entries(data)) {
				const newOption = document.createElement('option');
				newOption.value = id;
				newOption.text = title;
				selectBooks.appendChild(newOption);
			}
			if (Object.keys(data).length === 1) {
				const event = new Event('change', { bubbles: true });
				selectBooks.dispatchEvent(event);
			}
			
		});
	});

	selectBooks.addEventListener('change', (event) => {
		const branchID = selectBranch.value;
		const bookID = event.target.value;
		const mode = 'get-book'
		fetch('/ajx' + window.location.pathname + 'handlers', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mode, branchID, bookID })
		})
		.then(res => res.json())
		.then(data => {
			const inputQty = document.getElementById("qty");
			inputQty.setAttribute("max", data.max);
			document.getElementById('max_qty').innerHTML = `макс: ${data.max}`;
			document.getElementById('price').value = data.price;
            
			inputQty.addEventListener("input", function() {
				const max = parseInt(this.max);
				if (parseInt(this.value) > max) {
					this.value = max; 
				}
				calcAmount();
			});
			calcAmount();
		});
	});

	// document.getElementById("qty").addEventListener('input', calcAmount);
}
docReady(sellingHandlenrs);