<?php
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

$id = $_POST['pid'];
$mode = $_POST['mod'];

$reservation_id = $DB->selectCell('SELECT reservation_id FROM ?_bk_payments WHERE payment_id=?', $id);
$payment = $BS->getPayment($reservation_id);

if (!empty($payment['payment_id']) && $payment['expected_amount'] === null) {

	$payment['history'][] = [
		'amount' => $payment['amount'],
		'method' => $payment['method'],
		'comment' => $payment['comment'],
		'type' => 'single',
		'created' => $payment['created']
	];
}
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
if ($payment['paid_amount'] < $cost) {
	$surcharge = $cost - $payment['paid_amount'];
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
			<div class="col-4 col-form-label text-md-end">Филиал:</div>
			<div class="col-8">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['branch_name']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-4 col-form-label text-md-end">Книга:</label>
			<div class="col-8">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['title']?> / <?php echo $payment['author']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-4 col-md-4 col-form-label text-md-end">Цена экземпляра:</label>
			<div class="col-8 col-md-2">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $payment['price']?>">
			</div>
			<label class="col-4 col-md-2 col-form-label text-md-end">Кол-во:</label>
			<div class="col-2 col-md-1">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $payment['qty']?>">
			</div>
			<label class="col-3 col-md-2 col-form-label text-md-end">Стоимость:</label>
			<div class="col-3 col-md-1">
				<input type="text" readonly class="form-control-plaintext" id="cost" value="<?php echo $cost?>">
			</div>
		</div>

		<?php
		$remain_amount = ($payment['remain_amount'] > 0) ? $payment['remain_amount']: $fullPrice;
		if (is_array($payment['history'])) {
			foreach ($payment['history'] as $part) {
				$part_total = $part_total + $part['amount'];
				?>
				<div class="row mb-3 border-bottom border-top">
					<label class="col-5 col-sm-3 col-form-label text-md-end text-success">Оплачено:</label>
					<div class="col-7 col-sm-2">
						<input type="text" readonly class="form-control-plaintext text-success" value="<?php echo $part['amount']?>">
					</div>
					<label for="amount" class="col-5 col-sm-3 col-form-label text-md-end text-success">Дата платежа:</label>
					<div class="col-7 col-sm-3">
						<input type="text" readonly class="form-control-plaintext text-success" value="<?php echo $part['comment']?>">
					</div>
				</div>
				<?php
			}
		}
		?>
		<!-- <div class="row mb-3 border-bottom border-top">
			<label class="col-4 col-sm-3 col-form-label text-md-end text-success">Оплачено:</label>
			<div class="col-8 col-sm-2">
				<input type="text" readonly class="form-control-plaintext text-success" value="<?php echo $payment['amount']?>">
			</div>
			<label for="amount" class="col-4 col-sm-3 col-form-label text-md-end text-success">Дата платежа:</label>
			<div class="col-8 col-sm-3">
				<input type="text" readonly class="form-control-plaintext text-success" value="<?php echo $payment['comment']?>">
			</div>
		</div> -->

		<div class="row mb-2">
			<label class="col-4 col-form-label text-md-end">Телефон:</label>
			<div class="col-8">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $payment['phone']?>">
			</div>
		</div>
		
		<div class="row mb-2">
			<label for="delivery_to" class="col-12 col-md-4 col-form-label text-md-end">Адрес доставки:</label>
			<div class="col-12 col-md-8">
				<input type="text" class="form-control" id="delivery_to" name="delivery_to" value="<?php echo $payment['delivery_to']?>">
			</div>
		</div>
		<div class="row mb-2">
			<label for="for_courier" class="col-sm-4 col-form-label text-md-end">Комментарий:</label>
			<div class="col-8">
				<textarea readonly class="form-control-plaintext"><?php echo $payment['for_courier']?></textarea>
			</div>
		</div>
		
		<div class="row mt-4">
			<div class="offset-sm-3 col-sm-8 d-flex justify-content-between">
				<button type="submit" id="create-order" class="btn btn-primary w-50" <?php if ($not_enough) echo "disabled"?>> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>