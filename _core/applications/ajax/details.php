<?php
$appl_id = $_POST['pid'];
$mode = $_POST['mod'];

$dict = $TL->dict();
$group = $groups = [];
$tutors = $DB->select('SELECT tutor_id AS ARRAY_KEY, name, ava_img FROM ?_tutors ORDER BY name');
$participants = $DB->selectCol('SELECT group_id AS ARRAY_KEY, count(id) FROM ?_group_students GROUP BY group_id');

$application = $DB->selectRow('SELECT S.* , A.*'
	. ', G.sess_hash'
	. ' FROM ?_applications A'
	. ' INNER JOIN ?_students S ON A.stud_id=S.stud_id'
	. ' LEFT JOIN ?_groups G ON A.group_id=G.group_id'
	. ' WHERE A.appl_id=?'
	, $appl_id
);

if (empty($application['group_id'])) {
	$daysweek = explode(", ",$application['daysweek']);
	$dw = array();
	foreach ($daysweek as $d) {
		if (str_contains($dict['schedule'][9]['title'], $d)) {
			$dw[] = 9;
		}
		if (str_contains($dict['schedule'][10]['title'], $d)) {
			$dw[] = 10;
		}
	}
	$daysweek_result = array_unique($dw);

	$hours = array_map(function($t) {
		return (int)substr($t, 0, 2);
	}, explode(", ",$application['coursetime']));

	$age_ids = [5,6];
	$age_ids = ($application['whowill'] == "me") ? [5]: $age_ids;
	$age_ids = ($application['whowill'] == "child") ? [6]: $age_ids;

	$groups = $DB->select('SELECT *, DATE_FORMAT(startime, \'%H:%i\') AS stime FROM ?_groups G WHERE age_id IN(?a) AND schedule_id IN(?a) AND HOUR(startime) IN(?a)', $age_ids, $daysweek_result, $hours);
} else {
	$group = $DB->selectRow('SELECT *, DATE_FORMAT(startime, \'%H:%i\') AS stime FROM ?_groups G WHERE group_id=?', $application['group_id']);
	$groups[] = $group;
}
$tutor = $DB->selectRow('SELECT * FROM ?_tutors WHERE tutor_id=?', $group['tutor_id']);
$group_type = ($dict['age'][$group['age_id']]['code'] == "CHL") ? "child": "adult";
$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $tutor['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";

$details = [];
if ($application['appl_type'] == 'pending') {
	$details['title'] = $appltype[$application['appl_type']];
}
if ($application['appl_type'] == 'assigned') {    
	$details['title'] = $appltype[$application['appl_type']] . " " . $application['sess_hash'];
}
if ($application['appl_type'] == 'enrolled') {
	$details['title'] = $appltype[$application['appl_type']] . " в группу" . $application['sess_hash'];
}

?>
<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $id?>">
	<input type="hidden" name="mode" value="<?php echo $mode?>">

	<div class="col-sm-12 offset-md-1 col-md-10 col-lg-9 col-xl-8">
		<h5 class="mt-2"><?php echo $details['title']?></h5>
		<p class="mt-3">Заявитель: <strong><?php echo buildFIO($application)?>,  0<?php echo $application['phone']?></strong></p>
	
		<?php
		$application['mylevel'] = (empty($application['mylevel'])) ? "zero": $application['mylevel'];
		if ($application['group_id'] > 0) {
			?>
			<p class="mt-1">Формат: <strong><?php echo $dict['format'][$group['format_id']]['title']?> • <?php echo $dict['age'][$group['age_id']]['title']?></strong></p>
			<div class="mt-1 admin-row admin-row--tutor">
				<span>Преподаватель:</span>
				<div class="group__tutor-ava">
					<img src="/<?php echo $ava_img?>" alt="<?php echo $tutor['name']?>">
				</div>
				<strong><?php echo $tutor['name']?></strong>
			</div>
			<p class="mt-1">Расписание: 
				<strong><?php echo $dict['schedule'][$group['schedule_id']]['title']?></strong>, 
				<strong><?php echo date("H:i", strtotime($application['startime']))?></strong>
			</p>
			<p class="mt-1">Адрес: <strong><?php echo $dict['address'][$group['address_id']]['title']?></strong></p>
			<p class="mt-1">Свободный мест: <strong><?php echo $places_left['qty']?></strong></p>
			<?php
		} else {
			?>
			<p class="mt-1">Дни недели: <strong><?php echo $application['daysweek']?></strong></p>
			<p class="mt-1">Время начала: <strong><?php echo $application['coursetime']?></strong></p>
			<?php
		}
		?>
		<p class="mt-1">Кто будет учиться: <strong><?php echo $who_will[$application['whowill']]?></strong></p>
		<p class="mt-1">Текущий уровень: <strong><?php echo implode(' ', $my_level[$application['mylevel']])?></strong></p>
		<p class="mt-1">Комментарий: <?php echo nl2br($application['comment'])?></p>
	</div>

	<table class="table table-striped table-hover border-secondary-subtle">
		<thead>
		<tr class="fixed-row sticky-tr">
			<th>#</th>
			<th class="w-20">Код</th>
			<th class="w-20">Преподаватель</th>
			<th class="w-15">Формат</th>
			<th class="w-20">Адрес</th>
			<th class="w-5">Места</th>
			<th class="w-15">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$q = 1;
		foreach ($groups as $r) {
			$tr_class = ($id == $r['group_id']) ? "table-success": "";
			$request = $TL->request_encode('group_id', $r['group_id']);
			$active_class = ($r['is_active'] == 1) ? "default": "not-active";
			$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $tutors[$r['tutor_id']]['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $tutors[$r['tutor_id']]['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";
			$request = $TL->request_encode('group_id', $r['group_id']);
			$how_many = $r['usrqty'] - $participants[$r['group_id']];
			?>
			<tr class="rws <?php echo $active_class?> <?php echo $tr_class?>">
				<td class="align-middle"><?php echo $q?></td>
				<td class="align-middle"><?php echo $r['sess_hash']?></td>
				<td class="align-middle">
					<div class="tutor-ava-name">
						<div class="ava-img"><img src="/<?php echo $ava_img?>" alt=""></div>
						<?php echo $tutors[$r['tutor_id']]['name']?>
					</div>
				</td>
				<td class="align-middle">
					<?php echo $dict['format'][$r['format_id']]['title']?>,
					<?php echo $dict['age'][$r['age_id']]['title']?>
					<br>
					<?php echo $r['stime']?>,
					<?php echo $dict['schedule'][$r['schedule_id']]['title']?>
				</td>
				<td class="align-middle">
					<?php echo $dict['address'][$r['address_id']]['title']?>
				</td>
				<td class="align-middle"><?php echo $how_many?></td>
				<td class="align-middle">
					<div class="text-end ctrlBtn" data-pid="<?php echo $appl_id?>">
						<button class="btn btn-secondary btn-sm" type="button" data-group-id="<?php echo $r['group_id']?>" data-mod="link" data-page="link-group">Записать</button>
					</div>
				</td>
			</tr>
			<?php
			$q++;
		}
		?>
		</tbody>
	</table>
</form>
