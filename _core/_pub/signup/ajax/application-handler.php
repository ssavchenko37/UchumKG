<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re['status'] = "";

$date = new DateTime();
$exist = $DB->selectRow('SELECT * FROM ?_students WHERE phone=?', $ins['phone']);
$stud_id = 0;
if (count($exist) > 0) {	
	$stud_id = $exist['stud_id'];
}

if ($post['group_id'] > 0) {
	$appl['group_id'] = $post['group_id'];
	$appl['appl_code'] = "assigned";
}
$appl['stud_id'] = $stud_id;
$appl['first_name'] = trim($post['first_name']);
$appl['last_name'] = trim($post['last_name']);
$appl['phone'] = cleanPhone($post['phone']);
$appl['age'] = $post['age'];
$appl['stud_code'] = ($exist['stud_id'] > 0) ? "returned": "new";
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