<?php
$id = $_POST['pid'];
$branch_id = $_POST['view_branch_id'];
$book_id = $_POST['view-book_id'];
$mode = $_POST['mod'];

$books = $branches = array();

if ($mode == "add") {
	$sTTL = "Добавить";
	$exists = $DB->selectCol('SELECT book_id FROM ?_bk_book_stock WHERE branch_id=?', $branch_id);
	if (count($exists) === 0) {
		$exists[] = 0;
	}
	$books = $DB->selectCol('SELECT book_id AS ARRAY_KEY, CONCAT(title, " / ", author) AS book_meta FROM ?_bk_books WHERE book_id NOT IN(?a)', $exists);
	$stock['book_id'] = "";
	$stock['branch_id'] = $branch_id;
}
if ($mode == "edit") {
	$sTTL = "Редактировать";
	$stock = $DB->selectRow('SELECT * FROM ?_bk_book_stock WHERE stock_id=?', $id);
	$books = $DB->selectCol('SELECT book_id AS ARRAY_KEY, CONCAT(title, " / ", author) AS book_meta FROM ?_bk_books WHERE book_id=?', $stock['book_id']);
}

if ($stock['branch_id'] > 0) {
	$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches WHERE branch_id=?', $stock['branch_id']);
} else {
	$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" id="pid" name="pid" value="<?php echo $id?>">
	<input type="hidden" id="mode" name="mode" value="<?php echo $mode?>">
	

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>
		
		<div class="row mb-3">
			<label for="branch_id" class="col-sm-3 col-form-label text-md-end">Филиал:</label>
			<div class="col-sm-9">
				<select class="form-select" id="branch_id" name="branch_id">
					<?php if ($mode == "add" && $branch_id < 1) { ?>
						<option value=""> -- </option>
					<?php } ?>
					<?php echo getOptionsK($stock['branch_id'], $branches)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="book_id" class="col-sm-3 col-form-label text-md-end">Книга:</label>
			<div class="col-sm-9">
				<select class="form-select" id="book_id" name="book_id">
					<?php if ($mode == "add") { ?>
						<option value=""> -- </option>
					<?php } ?>
					<?php echo getOptionsK($stock['book_id'], $books)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="qty_total" class="col-sm-3 col-form-label text-md-end">Количество:</label>
			<div class="col-sm-7">
				<input type="text" class="form-control" id="qty_total" name="qty_total" value="<?php if (isset($stock['qty_total'])) echo $stock['qty_total']?>">
			</div>
		</div>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" class="btn btn-primary w-50"> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>