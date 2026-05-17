<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$defect = $books = $branches = array();
$disabled = $disabled_r = "";
if ($mode == "add") {
	$sTTL = "Добавить брак";
	$books = $DB->selectCol('SELECT book_id AS ARRAY_KEY, CONCAT(title, " / ", author) FROM ?_bk_books ORDER BY title');
	$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name AS branch_name FROM ?_bk_branches ORDER BY branch_id');
}
if (in_array($mode, ["edit","review"])) {
	$sTTL = "Редактировать";
	$defect = $DB->selectRow('SELECT D.*
		, B.title AS book_title
		, BR.name AS branch_name
		FROM ?_bk_defects D
		JOIN ?_bk_books B ON D.book_id=B.book_id
		JOIN ?_bk_branches BR ON D.branch_id = BR.branch_id
		WHERE defect_id=?'
		, $id
	);
	$books[$defect['book_id']] = $defect['book_title'];
	$branches[$defect['branch_id']] = $defect['branch_name'];
	$disabled = " disabled";
}
if ($mode == "review") {
	$disabled_r = " disabled";
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>

		<div class="row mb-3">
			<label for="book_id" class="col-sm-4 col-form-label text-end">Книга:</label>
			<div class="col-sm-8">
				<select class="form-select" id="book_id" name="book_id"<?php echo $disabled?>>
					<?php if (count($books) > 1) { ?>
					<option value=""> -- </option>
					<?php } ?>
					<?php echo getOptionsK($defect['book_id'], $books)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="branch_id" class="col-sm-4 col-form-label text-end">Филиал:</label>
			<div class="col-sm-8">
				<select class="form-select" id="branch_id" name="branch_id"<?php echo $disabled?>>
					<option value=""> -- </option>
					<?php echo getOptionsK($defect['branch_id'], $branches)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="qty" class="col-sm-4 col-form-label text-end">Количество:</label>
			<div class="col-sm-8">
				<input type="number" class="form-control" id="qty" name="qty" value="<?php if (isset($defect['qty'])) echo $defect['qty']?>"<?php echo $disabled_r?>>
				<small class="text-secondary" id="max_qty"></small>
			</div>
		</div>

		<div class="row mb-3">
			<label for="comment" class="col-sm-4 col-form-label text-end">Комментарий:</label>
			<div class="col-sm-8">
				<textarea class="form-control" id="comment" name="comment"<?php echo $disabled_r?>><?php echo $defect['comment']?></textarea>
			</div>
		</div>

		<?php if ($mode == "edit") { ?>
		<div class="row mb-3">
			<div class="col-sm-8 offset-sm-4">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="returned" id="returned" value="1">
					<label class="form-check-label" for="returned">Передано в типографию</label>
				</div>
			</div>
		</div>
		
		<?php } ?>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" class="btn btn-primary w-50"<?php echo $disabled_r?>> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>