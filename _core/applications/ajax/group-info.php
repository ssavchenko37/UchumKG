<?php
$id = $_POST['pid'];
$mode = $_POST['mod'];

$dict = $TL->dict_codes();

$tutors = $DB->selectCol('SELECT tutor_id AS ARRAY_KEY, name FROM ?_tutors');
$groups = array();
//group_code -> tl_dict; gstatus - forming,active
$tmp_groups = $DB->select('SELECT * FROM ?_groups WHERE group_code IN(?a) AND is_active=? ORDER BY created DESC', [14,15], 1);
foreach ($tmp_groups as $g) {
	$groups[$g['group_id']] = $g['sess_hash'] . ", " . $tutors[$g['tutor_id']];
}

$student = $DB->selectRow('SELECT A.appl_id, A.app_hash, S.*, GS.group_id AS gsid'
	. ' FROM ?_applications A'
	. ' INNER JOIN ?_students S ON A.stud_id=S.stud_id'
	. ' LEFT JOIN ?_group_students GS ON A.appl_id=GS.appl_id'
	. ' WHERE A.appl_id=?'
	, $id
);

$student['group_id'] = (empty($student['gsid'])) ? $_POST['groupId']: $student['gsid'];
//
?>

<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="stud_id" value="<?php echo $student['stud_id']?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2 mb-5">Участник</h5>

		<div class="row mb-3">
			<label class="col-sm-3 col-form-label text-end">Участник:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" disabled readonly value="<?php echo $student['app_hash']?>, <?php echo buildFIO($student)?>, 0<?php echo $student['phone']?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="group_id" class="col-sm-3 col-form-label text-end">Группы:</label>
			<div class="col-sm-9">
				<select class="form-select" id="group_id" name="group_id">
					<option value="0"> - Выбирите группу - </option>
					<?php echo getOptionsK($student['group_id'], $groups)?>
				</select>
			</div>
		</div>

		<div class="row mt-4 mb-3">
			<div class="offset-sm-3 col-sm-9 d-flex justify-content-between">
				<button type="submit" class="btn btn-primary w-50"> Сохранить </button>
				<button class="btn btn-secondary dismiss-tlaside" type="button" aria-label="Close"> Отменить </button>
			</div>
		</div>

	</div>

</form>