"use strict";

const pageInit = () => {
	setTimeout(defectHandlenrs, 100);
}

const defectHandlenrs = () => {
	const selectBooks = document.getElementById('book_id');
	const selectBranch = document.getElementById('branch_id');

	selectBranch.addEventListener('change', (event) => {
		const branchID = event.target.value;
		const bookID = selectBooks.value;
		const mode = 'get-max'
		fetch('/ajx' + window.location.pathname + 'handlers', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mode, branchID, bookID })
		})
		.then(res => res.json())
		.then(data => {
			const inputQty = document.getElementById("qty");
			inputQty.setAttribute("max", data.max);
			document.getElementById('max_qty').innerHTML = `всего: ${data.max}`;		
		});
	});
}