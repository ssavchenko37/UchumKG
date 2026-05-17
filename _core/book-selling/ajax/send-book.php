<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$payment = $DB->selectRow('SELECT r.reservation_id, r.qty, r.phone, r.created_at, r.expires_at
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
			<label class="col col-form-label text-end">Оплачено:</label>
			<div class="col">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['amount']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['phone']?>">
			</div>
		</div>
		<div class="row mb-2">
			<label for="comment" class="col-sm-3 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-9">
				<textarea readonly class="form-control-plaintext"><?php echo $payment['comment']?></textarea>
			</div>
		</div>
		<div class="row mb-2">
			<div class="offset-md-3 col">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="how_to" id="how_to2" value="hand" checked>
					<label class="form-check-label" for="how_to2"> Передать в руки </label>
				</div>
			</div>
		</div>

		<div class="row mb-2">
			<div class="offset-md-3 col">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="how_to" id="how_to1" value="delivery">
					<label class="form-check-label" for="how_to1"> Оформить доставку </label>
				</div>
			</div>
		</div>
		<div class="row mb-2">
			<label for="delivery_to" class="col-sm-3 col-form-label text-end">Адрес доставки:</label>
			<div class="col-sm-7">
				<input type="text" class="form-control" id="delivery_to" name="delivery_to" value="<?php echo $payment['delivery_to']?>">
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