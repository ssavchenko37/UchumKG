<?php
$id = $_POST['pid'];

$tmp_tutors = $DB->selectCol('SELECT tutor_id AS ARRAY_KEY, name FROM ?_tutors');
$tmp_admins = $DB->selectCol('SELECT admin_id AS ARRAY_KEY, name FROM ?_admins');
$employee = $tmp_tutors + $tmp_admins;

$order = $DB->selectRow('SELECT O.order_id, O.tutor_id, O.status, O.total_amount, O.delivery_to, O.created_at
	, I.qty
	, P.reservation_id, P.amount, P.method, P.paid_at, P.phone, P.comment
	, BR.branch_id, BR.name AS branch_name
	, B.book_id, B.title, B.author, B.price
	, R.for_courier
	FROM ?_bk_orders O
	JOIN ?_bk_order_items I ON O.order_id = I.order_id
	JOIN ?_bk_payments P ON O.payment_id = P.payment_id
	JOIN ?_bk_branches BR ON O.branch_id = BR.branch_id
	JOIN ?_bk_books B ON I.book_id = B.book_id
	LEFT JOIN ?_bk_reservations R ON P.reservation_id = R.reservation_id
	WHERE O.order_id=?'
	, $id
);
$order_method = ($order['reservation_id'] > 0) ? "Бронирование": "Прямая продажа"; 
$delivery_address = (empty($order['delivery_to'])) ? "На руки": $order['delivery_to']; 

?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Филиал:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['branch_name']?>">
			</div>
			<label class="col-sm-3 col-form-label text-end">Преподаватель:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $employee[$order['tutor_id']]?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Книга:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['title']?> / <?php echo $order['author']?>">
			</div>
		</div>

		<div class="row mb-2">
			<label class="col-sm-3 col-form-label text-end">Цена экземпляра:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $order['price']?>">
			</div>
			<label class="col-sm-2 col-form-label text-end">Кол-во:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php  echo $order['qty']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Сумма:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['total_amount']?>">
			</div>
			<label class="col-sm-2 col-form-label text-end">Время оплаты:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['comment']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Метод заказа:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order_method?>">
			</div>
			<label class="col-sm-2 col-form-label text-end">Время заказа:</label>
			<div class="col-sm-3">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['paid_at']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Адрес доставки:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $delivery_address?>">
			</div>
		</div>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Телефон покупателя:</label>
			<div class="col-sm-9">
				<input type="text" readonly class="form-control-plaintext" value="<?php echo $order['phone']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="for_courier" class="col-sm-3 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="for_courier" name="for_courier"><?php echo $order['for_courier']?></textarea>
			</div>
		</div>

		<div class="row mt-4">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Закрыть </button>
			</div>
		</div>

	</div>

</form>