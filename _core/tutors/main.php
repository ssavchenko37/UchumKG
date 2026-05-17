<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$tutors_groups = $active_groups = array();
$groups = $DB->select('SELECT * FROM ?_groups ORDER BY created DESC');
foreach($groups as $r) {
	$tutors_groups[$r['tutor_id']][$r['group_id']] = $r;
	if ($r['is_active'] == 1) {
		$active_groups[$r['tutor_id']][$r['group_id']] = $r;
	}
}

$tutors = $DB->select('SELECT * FROM ?_tutors ORDER BY name');
$ava_dir = DIRECTORY_SEPARATOR . S_AVA . DIRECTORY_SEPARATOR;
