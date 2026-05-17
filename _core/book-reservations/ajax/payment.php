<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

if ($mode == 'payment') {
	$reservation = $DB->selectRow('SELECT r.reservation_id, r.book_id, r.qty, r.phone, r.created_at, r.expires_at,
		b.title, b.author, b.price,
		br.name AS branch_name,
		r.qty*b.price AS amount
		FROM tl_bk_reservations r
		JOIN tl_bk_books b ON b.book_id = r.book_id
		JOIN tl_bk_branches br ON br.branch_id = r.branch_id
		WHERE r.reservation_id=?'
		, $id
	);
	$sTTL = "Подтвердить платеж";
}
if ($mode == 'payment-edit') {
	$reservation = $DB->selectRow('SELECT r.reservation_id, r.qty, r.phone, r.created_at, r.expires_at
		, b.title, b.author, b.price
		, br.name AS branch_name
		, p.payment_id, p.amount, p.comment
		FROM ?_bk_payments p
		JOIN ?_bk_reservations r ON r.reservation_id = p.reservation_id
		JOIN ?_bk_books b ON b.book_id = r.book_id
		JOIN ?_bk_branches br ON br.branch_id = r.branch_id
		WHERE p.payment_id=?'
		, $id
	);
	$reservation['surcharge'] = $reservation['qty']*$reservation['price'] - $reservation['amount'];
	$sTTL = "Провести доплату";
}
// p($reservation);
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" id="mode" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Филиал:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $reservation['branch_name']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Книга:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $reservation['title']?> / <?php echo $reservation['author']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" id="phone" name="phone" value="<?php echo $reservation['phone']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Количество:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $reservation['qty']?>">
				<small class="text-secondary" id="max_qty"></small>
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Цена экземпляра:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $reservation['price']?>">
			</div>
		</div>

		<?php
		if ($mode == 'payment') { ?>
		<div class="row mb-3">
			<label for="total_amount" class="col-sm-3 col-form-label text-end">Сумма оплаты:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo $reservation['amount']?>">
			</div>
		</div>
		<?php }
		?>

		<?php
		if ($mode == 'payment-edit') { ?>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Оплачено:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $reservation['amount']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="surcharge" class="col-sm-3 col-form-label text-end">Сумма доплаты:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="surcharge" name="surcharge" value="<?php echo $reservation['surcharge']?>">
			</div>
		</div>
		<?php }
		?>

		<div class="row mb-3">
			<label for="comment" class="col-sm-3 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="comment" name="comment" placeholder="Например: MBank"></textarea>
			</div>
		</div>

		<div class="row mt-4">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" class="btn btn-primary w-50"> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>