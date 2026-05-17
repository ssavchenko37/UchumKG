<?php
$id = $_POST['pid'];

foreach ($_REQUEST as $rKey => $rVal) {
	if (in_array($rKey, array("name","phone","birthday","gender","tg_chat_id"))) {
		if (!empty($rVal)) {
			$ins[$rKey] = trim($rVal);
		}
	}
}

if ($_POST['mode'] == "add") {
	//$DB->query('INSERT INTO ?_students (?#) VALUES (?a)', array_keys($ins), array_values($ins));
}
if ($_POST['mode'] == "edit") {
	$DB->query('UPDATE ?_students SET ?a WHERE stud_id=?', $ins, $id);
}

if ($_POST['mode'] == "delete") {
	$DB->query('DELETE FROM ?_students WHERE stud_id=?', $id);
	$DB->query('DELETE FROM ?_applications WHERE stud_id=?', $id);
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;