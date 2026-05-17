<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$dict = $TL->dict_arrays();
$tutors = $DB->selectCol('SELECT tutor_id AS ARRAY_KEY, name FROM ?_tutors ORDER BY name');

$group = array();
if ($mode == "add") {
	$sTTL = "Добавить";
	$group['usrqty'] = 15;
	$group['duration'] = 12;
	$group['level'] = 1;
	$group['is_active'] = 1;
}
if ($mode == "edit") {
	$sTTL = "Редактировать";
	$group = $DB->selectRow('SELECT * FROM ?_groups WHERE group_id=?', $id);
}
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5">Группа</h5>

		<div class="row mb-3">
			<label for="sess_hash" class="col-sm-3 col-form-label text-end">Код группы:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" readonly id="sess_hash" name="sess_hash" value="<?php if (isset($group['sess_hash'])) echo $group['sess_hash']?>">
				<small id="sess_descr" class="form-text">Код группы генерируется на основе даты, формата, возраста и id преподавателя</small>
			</div>
		</div>

		<div class="row mb-3">
			<label for="created" class="col-sm-3 col-form-label text-end">Дата:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control form-control-sm" id="created" name="created" value="<?php if (isset($group['created'])) echo $group['created']?>" placeholder="Дата">
				<small id="sess_date" class="form-text">Дата первого занятия группы</small>
			</div>
			<label for="startime" class="col-sm-1 col-form-label text-end">Время:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control form-control-sm" id="startime" name="startime">
			</div>
		</div>

		<div class="row mb-3">
			<label for="tutor_id" class="col-sm-3 col-form-label text-end">Преподаватель:</label>
			<div class="col-sm-9">
				<select class="form-select" id="tutor_id" name="tutor_id">
					<option value=""> -- Выберите преподавателя </option>
					<?php echo getOptionsK($group['tutor_id'], $tutors)?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="schedule_id" class="col-sm-3 col-form-label text-end">Расписание:</label>
			<div class="col-sm-4">
				<select class="form-select form-select-sm" id="schedule_id" name="schedule_id">
					<option value=""> -- Выберите расписание </option>
					<?php echo getOptionsK($group['schedule_id'], $dict['schedule'])?>
				</select>
			</div>
			<label for="format_id" class="col-sm-1 col-form-label text-end">Формат:</label>
			<div class="col-sm-4">
				<select class="form-select form-select-sm" id="format_id" name="format_id">
					<option value=""> -- Выберите формат </option>
					<?php echo getOptionsK($group['format_id'], $dict['format'])?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="age_id" class="col-sm-3 col-form-label text-end">Возраст:</label>
			<div class="col-sm-4">
				<select class="form-select form-select-sm" id="age_id" name="age_id">
					<option value=""> -- Выберите возраст </option>
					<?php echo getOptionsK($group['age_id'], $dict['age'])?>
				</select>
			</div>
			<label for="address_id" class="col-sm-1 col-form-label text-end">Адрес:</label>
			<div class="col-sm-4">
				<select class="form-select form-select-sm" id="address_id" name="address_id">
					<option value=""> -- Выберите адрес </option>
					<?php echo getOptionsK($group['address_id'], $dict['address'])?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<label for="usrqty" class="col-sm-3 col-form-label text-end">Студенты:</label>
			<div class="col-sm-4">
				<input type="number" class="form-control form-control-sm" id="stud_current" name="stud_current" value="<?php if (isset($group['stud_current'])) echo $group['stud_current']?>">
				<small class="form-text">Теущее количество</small>
			</div>
			<div class="col-sm-5">
				<input type="number" class="form-control form-control-sm" id="usrqty" name="usrqty" value="<?php if (isset($group['usrqty'])) echo $group['usrqty']?>">
				<small class="form-text">Максимальное количество</small>
			</div>
		</div>

		<div class="row mb-3">
			<label for="level" class="col-sm-3 col-form-label text-end">Уровень курса:</label>
			<div class="col-sm-4">
				<input type="number" class="form-control form-control-sm" id="level" name="level" value="<?php if (isset($group['level'])) echo $group['level']?>">
			</div>
			<label for="duration" class="col-sm-2 col-form-label text-end">Уроков в цикле:</label>
			<div class="col-sm-3">
				<input type="number" class="form-control form-control-sm" id="duration" name="duration" value="<?php if (isset($group['duration'])) echo $group['duration']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="status_id" class="col-sm-3 col-form-label text-end">Статус группы:</label>
			<div class="col-sm-9">
				<select class="form-select" id="status_id" name="status_id">
					<?php echo getOptionsK($group['status_id'], $dict['gstatus'])?>
				</select>
			</div>
		</div>

		<div class="row mb-3">
			<div class="offset-sm-3 col-sm-9">
				<div class="form-check form-switch">
					<input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value=1 <?php if ($group['is_active'] == 1) echo " checked"?>>
					<label class="form-check-label" for="is_active">
						<span class="text-secondary">Архивная</span> / <span class="text-primary">Рабочая</span>
					</label>
				</div>
			</div>
		</div>

		<div class="row mb-3 mt-5">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" id="group_btn" class="btn btn-primary w-50" disabled> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>