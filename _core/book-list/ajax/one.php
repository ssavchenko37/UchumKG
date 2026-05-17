<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$book = array();

if ($mode == "add") {
	$sTTL = "Добавить";
}
if ($mode == "edit") {
	$book = $DB->selectRow('SELECT * FROM ?_bk_books WHERE book_id=?', $id);
	$sTTL = "Редактировать";
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5"><?php echo $sTTL?></h5>

		<div class="row mb-3">
			<label for="title" class="col-sm-3 col-form-label text-end">Название:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="title" name="title" value="<?php if (isset($book['title'])) echo $book['title']?>" placeholder="Название книги">
			</div>
		</div>

		<div class="row mb-3">
			<label for="author" class="col-sm-3 col-form-label text-end">Автор:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="author" name="author" value="<?php if (isset($book['author'])) echo $book['author']?>" placeholder="Автор">
			</div>
		</div>

		<div class="row mb-3">
			<label for="isbn" class="col-sm-3 col-form-label text-end">ISBN:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="isbn" name="isbn" value="<?php if (isset($book['isbn'])) echo $book['isbn']?>" placeholder="ISBN">
			</div>
		</div>

		<div class="row mb-3">
			<label for="price" class="col-sm-3 col-form-label text-end">Цена:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="price" name="price" value="<?php if (isset($book['price'])) echo $book['price']?>" placeholder="Цена">
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