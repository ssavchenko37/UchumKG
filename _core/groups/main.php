<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$dict = $TL->dict();



if ($tldata['umod'] === "t") {
	$tutors = $DB->select('SELECT tutor_id AS ARRAY_KEY, name, ava_img FROM ?_tutors WHERE tutor_id=?', $tldata['id']);
	$groups = $DB->select('SELECT *, DATE_FORMAT(startime, \'%H:%i\') AS stime FROM ?_groups WHERE tutor_id=? ORDER BY created DESC, startime DESC', $tldata['id']);
	$gids = $DB->selectCol('SELECT group_id FROM ?_groups WHERE tutor_id=?', $tldata['id']);
	$participants = $DB->selectCol('SELECT group_id AS ARRAY_KEY, count(id) FROM ?_group_students WHERE group_id IN(?a) GROUP BY group_id', $gids);
} else {
	$tutors = $DB->select('SELECT tutor_id AS ARRAY_KEY, name, ava_img FROM ?_tutors ORDER BY name');
	$groups = $DB->select('SELECT *, DATE_FORMAT(startime, \'%H:%i\') AS stime FROM ?_groups ORDER BY created DESC, startime DESC');
	$participants = $DB->selectCol('SELECT group_id AS ARRAY_KEY, count(id) FROM ?_group_students WHERE status<>? GROUP BY group_id', 'listed');
}


