<?php
header('Content-Type: application/json; charset=utf-8');

$usr = $tldata['usr']['iid'];

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$group_id = $post['group_id'];
$meta_id = -1;
if(empty($post['meta_id'])) {
	$ins['group_id'] = $group_id;
	$ins['meta_uin'] = $post['meta_uin'];
	$ins['meta_date'] = $post['meta_date'];
	if (!empty($ins['meta_date'])) {
		$meta_id = $DB->query('INSERT INTO ?_ibook_meta (?#) VALUES (?a)', array_keys($ins), array_values($ins));
	}
} else {
	$upd['meta_date'] = $post['meta_date'];
	$DB->query('UPDATE ?_ibook_meta SET ?a WHERE meta_id=?', $upd, $post['meta_id']);
	$meta_id = $post['meta_id'];
}

$checkup = $DB->selectRow('SELECT *, DATE_FORMAT(meta_date, \'%d.%m\') AS monthday, DATE_FORMAT(meta_date, \'%H:%i\') AS hoursec FROM ?_ibook_meta WHERE meta_id=?', $meta_id);

$data['meta_id'] = $checkup['meta_id'];
$data['monthday'] = $checkup['monthday'];
$data['hoursec'] = $checkup['hoursec'];

// unset($checkup['monthday']);
// unset($checkup['hoursec']);
// $checkup['who'] = $usr;
// $checkup['entered'] = date('Y-m-d H:i:s');
// $DB->query('INSERT INTO ?_ibook_metabkp (?#) VALUES (?a)', array_keys($checkup), array_values($checkup));

echo json_encode($data);
exit;