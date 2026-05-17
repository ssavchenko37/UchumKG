<?php
$loc = "";
$Adm = $Tch = $Std = array();

if (isset($_POST['login_mode']) && $_POST['login_mode'] === "login") {
	if (!empty($_POST['your_cell'])) {

		if (strpos($_POST['your_cell'], "%") !== false) {
			$input_data = explode("%",$_POST['your_cell']);
			$tel =  cleanPhone($input_data[0]);
			$admin = $DB->selectRow('SELECT * FROM ?_admins WHERE tel=? AND id=? AND hide=?', $tel, 1, 0);
			$date = new DateTime();
			if ($admin['tel'] === $tel && $input_data[1] === $date->format('Nd')) {
				$set_cookie['umod'] = 'a';
				$set_cookie['id'] = $admin['id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "iadm";
			}
		}

		if (strpos($_POST['your_cell'], ":") !== false) {
			$input_data = explode(":",$_POST['your_cell']);
			$tel =  cleanPhone($input_data[0]);
			$admin = $DB->selectRow('SELECT * FROM ?_admins WHERE tel=? AND id>? AND hide=?', $tel, 1, 0);
			$date = new DateTime();
			if ($admin['tel'] === $tel && $input_data[1] === $date->format('Nd')) {
				$set_cookie['umod'] = 'a';
				$set_cookie['id'] = $admin['id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "iadm";
			}
		}
		
		if (strpos($_POST['your_cell'], "/") !== false) {
			$input_data = explode("/",$_POST['your_cell']);
			$tel = cleanPhone($input_data[0]);
			$tutor = $DB->selectRow('SELECT * FROM ?_tutors WHERE tel=? AND hide=?', $tel, 0);
			if ($tutor['tel'] === $tel && $tutor['pass'] === $input_data[1]) {
				$set_cookie['umod'] = 't';
				$set_cookie['id'] = $tutor['tutor_id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "itch";
			}
		}

		if (empty($loc)) {
			$tel = cleanPhone($_POST['your_cell']);
			$student = $DB->selectRow('SELECT * FROM ?_students WHERE tel=? AND hide=?', $tel, 0);
			if ($student['tel'] === $tel) {
				$set_cookie['umod'] = 's';
				$set_cookie['id'] = $student['stud_id'];
				$str_cookie = auth2cook($set_cookie);
				$loc = "istd";
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
