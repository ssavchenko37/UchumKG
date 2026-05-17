<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$tutor = array();

$ava = DIRECTORY_SEPARATOR . S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
if ($mode == "add") {
	$sTTL = "Добавить";
}
if ($mode == "edit") {
	$tutor = $DB->selectRow('SELECT * FROM ?_tutors WHERE tutor_id=?', $id);
	$sTTL = "Редактировать";
	
	if (is_file(S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img'])) {
		$ava = DIRECTORY_SEPARATOR . S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img'];	
	}
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5">Преподаватель</h5>

		<div class="row mb-3">
			<label for="name" class="col-sm-3 col-form-label text-end">Имя:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="name" name="name" value="<?php if (isset($tutor['name'])) echo $tutor['name']?>" placeholder="Имя преподавателя">
			</div>
		</div>

		<div class="row mb-3">
			<label for="phone" class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="phone" name="phone" value="<?php if (isset($tutor['phone'])) echo $tutor['phone']?>" placeholder="Телефон">
			</div>
		</div>

		<div class="row mb-3">
			<label for="pass" class="col-sm-3 col-form-label text-end">Пароль:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="pass" name="pass" value="<?php if (isset($tutor['pass'])) echo $tutor['pass']?>" placeholder="Пароль">
			</div>
		</div>

		<div class="row mb-3 ava">
			<label for="ava_img" class="col-sm-3 col-form-label text-end">Фото:</label>
			<div class="col ava__image">
				<img src="<?php echo $ava?>" alt="<?php if (isset($tutor['name'])) echo $tutor['name']?>">
			</div>
			<div class="col-sm-7 ava__file">
				<input class="form-control " type="file" name="ava_img" id="ava_img">
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