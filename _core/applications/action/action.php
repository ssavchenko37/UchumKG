<?php
//p($_POST);
$appl = $DB->selectRow('SELECT * FROM ?_applications WHERE appl_id=?', $_POST['pid']);

if ($_POST['mode'] == "add_application") {
	$date = new DateTime();
	if (!empty($_POST['stud_name']) && !empty($_POST['stud_tel'])) {
		if (empty($_POST['stud_id'])) {
			$tmp_name = explode(" ", $_POST['stud_name']);
			$ins['first_name'] = $tmp_name[0];
			$ins['last_name'] = $tmp_name[1];
			$ins['patronymic'] = $tmp_name[2];
			$ins['phone'] = cleanPhone($_POST['stud_tel']);
			$ins['birthday'] = $_POST['stud_birthday'];

			$stud_id = $DB->query('INSERT INTO ?_students (?#) VALUES (?a)', array_keys($ins), array_values($ins));

			$num_lenght = ($stud_id > 99999) ? 6: 5;
			$formatted = str_pad($stud_id, $num_lenght, "0", STR_PAD_LEFT);
			$hash = $upd['first_hash'] = $date->format('d.m.y') . "." . $formatted;
			$DB->query('UPDATE ?_students SET ?a WHERE stud_id=?', $upd, $stud_id);
		} else {
			$tmp = $DB->selectRow('SELECT * FROM ?_students WHERE stud_id=?', $_POST['stud_id']);
			$stud_id = $tmp['stud_id'];
			$num_lenght = ($stud_id > 99999) ? 6: 5;
			$formatted = str_pad($stud_id, $num_lenght, "0", STR_PAD_LEFT);
			$hash = $date->format('d.m.y') . "." . $formatted;
		} 

		$appl['stud_id'] = $stud_id;
		$appl['sutd_type'] = (empty($_POST['stud_id'])) ? "new": "returned";
		$appl['app_hash'] = $hash;
		$appl['created'] = date('Y-m-d H:i:s');
		$DB->query('INSERT INTO ?_applications (?#) VALUES (?a)', array_keys($appl), array_values($appl));
	}
}

if ($_POST['mode'] === "approved") {
	$upd_status['appl_code'] = "approved";
	$DB->query('UPDATE ?_applications SET ?a WHERE appl_id=?', $upd_status, $appl['appl_id']);
}
if ($_POST['mode'] === "initial") {
	$upd_status['appl_code'] = ($appl['group_id'] > 0)? "assigned": "pending";
	$DB->query('UPDATE ?_applications SET ?a WHERE appl_id=?', $upd_status, $appl['appl_id']);
}

if ($_POST['mode'] === "link") {
	$exist = $DB->selectRow('SELECT * FROM ?_group_students WHERE group_id=? AND stud_id=? AND appl_id=?', $_POST['group_id'], $_POST['stud_id'], $_POST['pid']);
	
	if (count($exist) == 0) {
		// $ins['group_id'] = $_POST['group_id'];
		// $ins['stud_id'] = $_POST['stud_id'];
		// $ins['appl_id'] = $_POST['pid'];
		// $ins['assigned'] = date('Y-m-d H:i:s');
		// $ins['status'] = "listed";
		// $DB->query('INSERT INTO ?_group_students (?#) VALUES (?a)', array_keys($ins), array_values($ins));
		
		// $upd_appl['appl_code'] = "recorded";
		//$DB->query('UPDATE ?_applications SET ?a WHERE appl_id=?', $upd_appl, $_POST['pid']);
	}
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;