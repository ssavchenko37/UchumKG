<?php
header('Content-Type: application/json; charset=utf-8');

$tldata = $TL->tldata();
$usr = $tldata['usr']['iid'];
$tid = ($tldata['umod'] == "a") ? $tldata['usr']['id']: $tldata['usr']['tutor_id'];

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re['status']  = 'error';
$mode = $post['mode'];
$group_id = $post['group_id'];

if ($mode == "edited_meta") {
	$upd[$post['field']] = $post['rate'];
	$DB->query('UPDATE ?_ibook_meta SET ?a WHERE meta_id=?', $upd, $post['meta_id']);
	$checkup = $DB->selectRow('SELECT * FROM ?_ibook_meta WHERE meta_id=?', $post['meta_id']);
	$checkup['who'] = $usr;
	$checkup['entered'] = date('Y-m-d H:i:s');
	$DB->query('INSERT INTO ?_ibook_metabkp (?#) VALUES (?a)', array_keys($checkup), array_values($checkup));

	$re['status'] = 'success';
	$re['value'] = $checkup[$post['field']];
}

if ($mode == "edited_val") {
	$item_id = -1;
	if (empty($post['item_id'])) {
		$ins['group_id'] = $group_id;
		$ins['item_uin'] = $post['item_uin'];
		$ins['stud_id'] = $post['stud_id'];
		$ins['item_val'] = trim($post['rate']);
		if ($post['rate'] == 'нб') {
			$ins['is_abs'] = 1;
		}
		$ins['who'] = $usr;

		$exist = $DB->selectRow('SELECT * FROM ?_ibook_items WHERE group_id=? AND item_uin=? AND stud_id=?', $ins['group_id'], $ins['item_uin'], $ins['stud_id']);
		if (count($exist) > 0) {
			$upd_exist['item_val'] = trim($post['rate']);
			$upd['who'] = $usr;
			$DB->query('UPDATE ?_ibook_items SET ?a WHERE item_id=?', $upd_exist, $exist['item_id']);
			$item_id = $exist['item_id'];
		} else {
			$item_id = $DB->query('INSERT INTO ?_ibook_items (?#) VALUES (?a)', array_keys($ins), array_values($ins));
		}
	} else {
		$item_id = $post['item_id'];
		$upd['item_val'] = trim($post['rate']);
		$upd['who'] = $usr;
		$DB->query('UPDATE ?_ibook_items SET ?a WHERE item_id=?', $upd, $post['item_id']);
	}

	$checkup = $DB->selectRow('SELECT * FROM ?_ibook_items WHERE item_id=?', $item_id);
	$checkup['entered'] = date('Y-m-d H:i:s');
	$DB->query('INSERT INTO ?_ibook_itemsbkp (?#) VALUES (?a)', array_keys($checkup), array_values($checkup));

	$re['item_id'] = $item_id;
	$re['status'] = 'success';
	$re['value'] = $checkup['item_val'];
}

echo json_encode($re);

