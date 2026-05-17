<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$payment = $DB->selectRow('SELECT r.*
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

$check_delivery = "";
$check_hand = " checked";
if (!empty($payment['delivery_to'])) {
	$check_delivery = " checked";
	$check_hand = "";
}

$cost = $payment['qty']*$payment['price'];
$surcharge = 0;
$not_enough = false;
$amount_color = 'text-success';
if ($payment['amount'] < $cost) {
	$surcharge = $cost - $payment['amount'];
	$not_enough = true;
	$amount_color = 'text-danger';
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="order">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-4">Закрыть продажу</h5>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Филиал:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['branch_name']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Книга:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['title']?> / <?php echo $payment['author']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Цена экземпляра:</label>
			<div class="col">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $payment['price']?>">
			</div>
			<label class="col col-form-label text-end">Кол-во:</label>
			<div class="col">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $payment['qty']?>">
			</div>
			<label class="col col-form-label text-end">Стоимость:</label>
			<div class="col">
				<input type="text" readonly class="form-control-plaintext" id="cost" value="<?php echo $cost?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end <?php echo $amount_color?>">Оплачено:</label>
			<div class="col">
				<input type="text" readonly class="form-control-plaintext <?php echo $amount_color?>" id="amount" value="<?php  echo $payment['amount']?>">
			</div>
			<?php if ($not_enough) { ?>
			<label for="surcharge" class="col col-form-label text-end <?php echo $amount_color?>">Доплата:</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="surcharge" name="surcharge" value="">
				<small class="text-danger">Необходима доплата: <?php echo number_format($surcharge, 2)?></small>
			</div>
			<?php } ?>
		</div>

		<div class="row mb-3">
			<label for="comment" class="col-sm-4 col-form-label text-end">Дата и время платежа:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control form-control-sm" id="comment" name="comment" value="<?php echo $payment['comment']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['phone']?>">
			</div>
		</div>
		
		<div class="row mb-2">
			<label for="delivery_to" class="col-sm-3 col-form-label text-end">Адрес доставки:</label>
			<div class="col-sm-7">
				<input type="text" class="form-control" id="delivery_to" name="delivery_to" value="<?php echo $payment['delivery_to']?>">
			</div>
		</div>
		<div class="row mb-2">
			<label for="for_courier" class="col-sm-3 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-7">
				<textarea readonly class="form-control-plaintext"><?php echo $payment['for_courier']?></textarea>
			</div>
		</div>
		
		<div class="row mt-4">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" id="create-order" class="btn btn-primary w-50" <?php if ($not_enough) echo "disabled"?>> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>