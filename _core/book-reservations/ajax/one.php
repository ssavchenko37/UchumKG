<?php
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

$id = $_POST['pid'];
$mode = $_POST['mod'];
$reservation = $books = $branches = array();
$part_total = 0;

if ($mode == "add") {
	$sTTL = "Добавить бронь";
	$books = $DB->selectCol('SELECT book_id AS ARRAY_KEY, CONCAT(title, " / ", author) FROM ?_bk_books ORDER BY title');
	$reservation['where_go'] = 'hand';
}
if ($mode == "edit") {
	$reservation = $BS->getPayment($id);

	$books[$reservation['book_id']] = $reservation['title'] . " / " . $reservation['author'];

	$tmp_branches = $DB->select('SELECT (ST.qty_total - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
		, BR.branch_id, BR.name, BR.is_store
		FROM ?_bk_book_stock ST
		JOIN ?_bk_branches BR ON ST.branch_id=BR.branch_id
		WHERE book_id=?'
		, $reservation['book_id']
	);

	foreach ($tmp_branches as $br) {
		if ($br['available'] >= $reservation['qty']) {
			$branches[$br['branch_id']] = $br['name'] . "; доступно: " . $br['available'];
		} else {
			$diff = $reservation['qty'] - $br['available'];
			$branches[$br['branch_id']] = $br['name'] . "; доступно: 0; дефицит: " . $diff;
		}		
	}

	$sTTL = "Редактировать бронь";
}
$check_hand = " checked";
$check_capital = "";
$check_region = "";
if ($reservation['where_go'] !== 'hand') {
	$check_hand = "";
	if ($reservation['where_go'] === 'capital') {
		$check_capital = " checked";
	} else {
		$check_region = " checked";
	}
}
$remain_amount = $reservation['qty'] * $reservation['price'];
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" id="mode" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>
		<hr>
		<div class="row mb-3">
			<div class="col">Основные данные брони</div>
		</div>
		<div class="row mb-3">
			<label for="phone" class="col-sm-4 col-form-label text-md-end">Телефон:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="phone" name="phone" value="<?php if (isset($reservation['phone'])) echo $reservation['phone']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="book_id" class="col-sm-4 col-form-label text-md-end">Книга:</label>
			<div class="col-sm-8">
				<select class="form-select" id="book_id" name="book_id">
					<?php if (count($books) > 1) { ?>
					<option value=""> -- </option>
					<?php } ?>
					<?php echo getOptionsK($reservation['book_id'], $books)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="qty" class="col-sm-4 col-form-label text-md-end">Количество:</label>
			<div class="col-sm-8">
				<input type="number" class="form-control" id="qty" name="qty" value="<?php if (isset($reservation['qty'])) echo $reservation['qty']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="branch_id" class="col-sm-4 col-form-label text-md-end">Филиал:</label>
			<div class="col-sm-8">
				<select class="form-select" id="branch_id" name="branch_id">
					<?php if ($mode == "edit") { ?>
					<?php echo getOptionsK($reservation['branch_id'], $branches)?>
					<?php } ?>
				</select>
			</div>
		</div>

		<hr>
		<div class="row mb-3">
			<div class="col">Заполняем если есть подтвержденная оплата</div>
		</div>
		<div class="row mb-3">
			<label for="price" class="col-sm-4 col-form-label text-md-end">Цена экземпляра:</label>
			<div class="col-sm-8">
				<input type="number" readonly class="form-control" id="price" name="price" value="<?php if (isset($reservation['price'])) echo $reservation['price']?>">
			</div>
		</div>

		<?php
		if ($reservation['payment_status'] === "partial") {
			$reservation['comment'] = '';
			$remain_amount = $reservation['remain_amount'];
			foreach ($reservation['parts'] as $part) {
				$part_total = $part_total + $part['amount'];
				?>
				<div class="row mb-3 border-bottom border-top">
					<label class="col-5 col-sm-4 col-form-label text-md-end">Оплачено:</label>
					<div class="col-7 col-sm-2">
						<input type="text" readonly class="form-control-plaintext" value="<?php echo $part['amount']?>">
					</div>
					<label for="amount" class="col-5 col-sm-3 col-form-label text-md-end">Дата платежа:</label>
					<div class="col-7 col-sm-3">
						<input type="text" readonly class="form-control-plaintext" value="<?php echo $part['part_comment']?>">
					</div>
				</div>
				<?php
			}
			?>
			<?php
		}
		?>

		<input type="hidden" id="part_total" value="<?php echo $part_total?>">
		<div class="row mb-3">
			<label for="amount" class="col-12 col-sm-4 col-form-label text-md-end">Подтвержденная сумма:</label>
			<div class="col-12 col-sm-8">
				<input type="text" class="form-control" id="amount" name="amount" value="<?php echo $reservation['amount']?>">
				<?php if ($mode == "add") { ?>
					<small id="needed_amount"></small>
				<?php } else { ?>
					<small>Необходимая сумма: <small id="needed_amount"><?php echo $remain_amount?></small></small>
				<?php } ?>
			</div>
		</div>

		<div class="row mb-3">
			<label for="comment" class="col-sm-4 col-form-label text-md-end">Дата и время платежа:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control form-control-sm" id="comment" name="comment" value="<?php echo $reservation['comment']?>">
			</div>
		</div>

		<hr>
		<div class="row mb-3">
			<div class="col">Заполняем если предполагается доставка</div>
		</div>
		<div class="row mb-3">
			<div class="offset-sm-2 col">
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="where_go" id="whereGOhand" value="hand"<?php echo $check_hand?>>
					<label class="form-check-label small" for="whereGOhand">Самовывоз</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="where_go" id="whereGOcapital" value="capital"<?php echo $check_capital?>>
					<label class="form-check-label small" for="whereGOcapital">Доставка по городу</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="where_go" id="whereGOregion" value="region"<?php echo $check_region?>>
					<label class="form-check-label small" for="whereGOregion">Доставка в регион</label>
				</div>
			</div>
		</div>
		<div class="row mb-3">
			<label for="delivery_to" class="col-sm-4 col-form-label text-md-end">Адрес доставки:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="delivery_to" name="delivery_to" value="<?php if (isset($reservation['delivery_to'])) echo $reservation['delivery_to']?>">
				<small class="text-secondary" id="max_qty"></small>
			</div>
		</div>

		<div class="row mb-2">
			<label for="for_courier" class="col-sm-4 col-form-label text-md-end">Комментарий:</label>
			<div class="col-sm-8">
				<textarea class="form-control" id="for_courier" name="for_courier"><?php echo $reservation['for_courier']?></textarea>
			</div>
		</div>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-8 d-flex justify-content-between">
				<button type="submit" id="create_reserve" class="btn btn-primary w-50"> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>