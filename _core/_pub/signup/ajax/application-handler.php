<?php

header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re['status'] = "";

$ins['phone'] = cleanPhone($post['phone']);
$ins['first_name'] = trim($post['first_name']);
$ins['last_name'] = trim($post['last_name']);

// $exist = $DB->selectRow('SELECT * FROM ?_students WHERE phone=? AND first_name=? AND last_name=?', $ins['phone'], $ins['first_name'], $ins['last_name']);
$exist = $DB->selectRow('SELECT * FROM ?_students WHERE phone=?', $ins['phone']);

$date = new DateTime();
if (count($exist) == 0) {
	$stud_id = $DB->query('INSERT INTO ?_students (?#) VALUES (?a)', array_keys($ins), array_values($ins));
	$num_lenght = ($stud_id > 99999) ? 6: 5;
	$formatted = str_pad($stud_id, $num_lenght, "0", STR_PAD_LEFT);
	$hash = $upd['first_hash'] = $date->format('d.m.y') . "." . $formatted;
	$DB->query('UPDATE ?_students SET ?a WHERE stud_id=?', $upd, $stud_id);
} else {
	$stud_id = $exist['stud_id'];
	$num_lenght = ($stud_id > 99999) ? 6: 5;
	$formatted = str_pad($stud_id, $num_lenght, "0", STR_PAD_LEFT);
	$hash = $date->format('d.m.y') . "." . $formatted;
}

$appl['appl_type'] = "pending";
if ($post['group_id'] > 0) {
	$appl['group_id'] = $post['group_id'];
	$appl['appl_type'] = "assigned";
}
$appl['stud_id'] = $stud_id;
$appl['age'] = $post['age'];
$appl['stud_type'] = ($exist['stud_id'] > 0) ? "returned": "new";
$appl['app_hash'] = $hash;
$appl['daysweek'] = implode(', ', $post['daysweek_arr']);
$appl['coursetime'] = implode(', ', array_map(function($h) {
	return sprintf('%02d:00', $h);
}, $post['coursetime_arr']));
$appl['whowill'] = $post['whowill'];
$appl['mylevel'] = $post['mylevel'];
if (!empty($post['comment'])) {
	$appl['comment'] = trim($post['comment']);
}
$appl['created'] = date('Y-m-d H:i:s');
$DB->query('INSERT INTO ?_applications (?#) VALUES (?a)', array_keys($appl), array_values($appl));
$re['status'] = 'success';

echo json_encode($re);
exit;