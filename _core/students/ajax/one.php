<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$student = array();

if ($mode == "add") {
	$sTTL = "Добавить";
}
if ($mode == "edit") {
	$student = $DB->selectRow('SELECT * FROM ?_students WHERE stud_id=?', $id);
	$sTTL = "Редактировать";
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5">Участник</h5>

		<div class="row mb-3">
			<label for="name" class="col-sm-3 col-form-label text-end">Номер:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" disabled readonly value="<?php if (isset($student['first_hash'])) echo $student['first_hash']?>" aria-describedby="first_namber">
				<div id="first_namber" class="form-text">Номер первого обращения</div>
			</div>
		</div>

		<div class="row mb-3">
			<label for="phone" class="col-sm-3 col-form-label text-end">Телефон:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="phone" name="phone" value="<?php if (isset($student['phone'])) echo $student['phone']?>" placeholder="Телефон">
			</div>
		</div>

		<div class="row mb-3">
			<label for="name" class="col-sm-3 col-form-label text-end">Имя:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="name" name="name" value="<?php if (isset($student['name'])) echo $student['name']?>" placeholder="Фамилия Имя Отчество">
			</div>
		</div>

		<div class="row mb-3">
			<label for="birthday" class="col-sm-3 col-form-label text-end">Дата рождения:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="birthday" name="birthday" value="<?php if (isset($student['birthday'])) echo $student['birthday']?>" placeholder="Дата рождения">
			</div>
		</div>

		<div class="row mb-3">
			<label for="gender" class="col-sm-3 col-form-label text-end">Пол:</label>
			<div class="col-sm-9">
				<select class="form-control" id="gender" name="gender">
					<option value="0"> Пол </option>
					<?php echo getOptionsK($student['gender'], [1=>"Женский", 2=>"Мужской"])?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="tg_chat_id" class="col-sm-3 col-form-label text-end">Телеграм ID:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="tg_chat_id" name="tg_chat_id" value="<?php if (isset($student['tg_chat_id'])) echo $student['tg_chat_id']?>" placeholder="Телеграм ID">
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