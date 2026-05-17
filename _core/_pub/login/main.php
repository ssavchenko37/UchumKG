<?php
$loc = "";
$Adm = $Tch = $Std = array();

if (isset($_POST['login_mode']) && $_POST['login_mode'] === "login") {

	if (!empty($_POST['your_cell'])) {
		$last_login['last_login'] = date('Y-m-d H:i:s');

		if (strpos($_POST['your_cell'], "%") !== false) {
			$input_data = explode("%",$_POST['your_cell']);
			$phone =  cleanPhone($input_data[0]);
			$admin = $DB->selectRow('SELECT * FROM ?_admins WHERE phone=? AND admin_id=? AND hide=?', $phone, 1, 0);
			$date = new DateTime();
			if ($admin['phone'] === $phone && $input_data[1] === $date->format('Nd')) {
				$set_cookie['umod'] = 'a';
				$set_cookie['id'] = $admin['id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "iadm";
				$DB->query('UPDATE ?_admins SET ?a WHERE admin_id=?', $last_login, $admin['admin_id']);
			}
		}

		if (strpos($_POST['your_cell'], ":") !== false) {
			$input_data = explode(":",$_POST['your_cell']);
			$phone =  $input_data[0];
			$admin = $DB->selectRow('SELECT * FROM ?_admins WHERE phone=? AND admin_id>? AND hide=?', $phone, 1, 0);
			$date = new DateTime();
			// p($admin['phone'] . " - " . $phone . "; " . $input_data[1] . " - " . $date->format('Nd'));
			if ($admin['phone'] === $phone && $input_data[1] === $date->format('Nd')) {
				$set_cookie['umod'] = 'a';
				$set_cookie['id'] = $admin['admin_id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "iadm";
				$DB->query('UPDATE ?_admins SET ?a WHERE admin_id=?', $last_login, $admin['admin_id']);
			}
		}
		
		if (strpos($_POST['your_cell'], "/") !== false) {
			$input_data = explode("/",$_POST['your_cell']);
			// $phone = cleanPhone($input_data[0]);
			$phone = $input_data[0];
			$tutor = $DB->selectRow('SELECT * FROM ?_tutors WHERE phone=? AND hide=?', $phone, 0);
			if ($tutor['phone'] === $phone && $tutor['pass'] === $input_data[1]) {
				$set_cookie['umod'] = 't';
				$set_cookie['id'] = $tutor['tutor_id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "itch";
				$DB->query('UPDATE ?_tutors SET ?a WHERE tutor_id=?', $last_login, $tutor['tutor_id']);
			}
		}

		if (empty($loc)) {
			$phone = cleanPhone($_POST['your_cell']);
			$student = $DB->selectRow('SELECT * FROM ?_students WHERE phone=? AND hide=?', $phone, 0);
			if ($student['phone'] === $phone) {
				$set_cookie['umod'] = 's';
				$set_cookie['id'] = $student['stud_id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "istd";
				$DB->query('UPDATE ?_students SET ?a WHERE stud_id=?', $last_login, $student['stud_id']);
			}
		}
	}
}

if (!empty($str_cookie)) {
	if ($_POST['remember_me']) {
		setcookie ("tl_sys", $str_cookie, time() + EXPIRY, "/");
	} else {
		setcookie ("tl_sys", $str_cookie, 0, "/");
	}
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /");
}
