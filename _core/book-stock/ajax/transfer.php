<?php
$id = $_POST['pid'];
$book_id = $_POST['view_book_id'];

$branches = [];
$tmp_branches = $DB->select('SELECT BR.*, BR.name AS branch_name
	, (ST.qty_total - ST.qty_reserved - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
	FROM ?_bk_branches BR
	JOIN ?_bk_book_stock ST ON BR.branch_id=ST.branch_id
	WHERE ST.book_id=?'
	, $book_id
);

foreach ($tmp_branches as $k=>$r) {
	if ($r['available'] > 0) {
		$branches[$k]['branch_id'] = $r['branch_id'];
		$branches[$k]['branch_name'] = $r['branch_name'] . "; доступно для трансфера: " . $r['available'];
		$branches[$k]['branch_max'] = $r['available'];
	}
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" id="mode" name="mode" value="transfer">
	<input type="hidden" id="pid" name="pid" value="<?php echo $id?>">
	<input type="hidden" id="book_id" name="book_id" value="<?php echo $book_id?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5">Трансфер книги</h5>

		<div class="row mb-3">
			<label for="branch_from" class="col-sm-3 col-form-label text-end">Филиал откуда:</label>
			<div class="col-sm-9">
				<select class="form-select" id="branch_from" name="branch_from">
					<option value=""> -- </option>
					<?php foreach ($branches as $b) { ?>
					<option value="<?php echo $b['branch_id']?>" data-send="<?php echo $b['branch_max']?>"> <?php echo $b['branch_name']?> </option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="branch_to" class="col-sm-3 col-form-label text-end">Филиал куда:</label>
			<div class="col-sm-9">
				<select class="form-select" id="branch_to" name="branch_to">
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="qty_transfer" class="col-sm-3 col-form-label text-end">Количество:</label>
			<div class="col-sm-7">
				<input type="number" class="form-control" id="qty_transfer" name="qty_transfer" value="">
				<small></small>
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