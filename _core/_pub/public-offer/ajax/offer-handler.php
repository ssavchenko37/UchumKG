<?php

header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = $post;
$re['status']  = 'error';

$phone = cleanPhone($post['phone']);

$exists = $DB->select('SELECT * FROM ?_students WHERE phone=?', $phone);

if (count($exists) === 1) {
	$user = $exists[0];
	$listed = $DB->select('SELECT stud_id FROM ?_group_students WHERE stud_id=?', $user['stud_id']);

	if (count($post['childfirst_arr']) > 1) {
		foreach ($post['childfirst_arr'] as $k=>$name) {
			if ($k === 0) continue;
			$exist_child = $DB->select('SELECT * FROM ?_students WHERE parent_id=? AND first_name=? AND last_name=?', $user['stud_id'], $name, $post['childlast_arr'][$k]);
			if (count($exist_child) === 0) {
				$ins['parent_id'] = $user['stud_id'];
				$ins['first_name'] = $name;
				$ins['last_name'] = $post['childlast_arr'][$k];
				$ins['birthday'] = $post['child_birthday_arr'][$k];
				$DB->query('INSERT INTO ?_students (?#) VALUES (?a)', array_keys($ins), array_values($ins));
				$re['status'] = "signing";
				$re['children'] = 1;
			}		
		}
	}

	if ($user['sign_offer'] === 1) {
		$re['status'] = "signed";
		$re['sign_date'] = date('m.d.Y', strtotime($user['sign_date'])); ;
	} else {
		if (count($listed) > 0) {
			$re['status'] = "signing";
			$upd['patronymic'] = $post['patronymic'];
			$upd['birthday'] = $post['birthday'];
			$upd['sign_offer'] = 1;
			$upd['sign_date'] = date('Y-m-d H:i:s');
			$DB->query('UPDATE ?_students SET ?a WHERE stud_id=?', $upd, $user['stud_id']);
		} else {
			$re['status'] = "impossible";
		}
	}

} else {
	$re['status'] = "impossible";
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);