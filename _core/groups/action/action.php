<?php
$id = $_POST['pid'];

foreach ($_REQUEST as $rKey => $rVal) {
	if (in_array($rKey, array("tutor_id","sess_hash","format_id","age_id","address_id","schedule_id","status_id","usrqty","duration","level","startime","created"))) {
		$ins[$rKey] = trim($rVal);
	}
}
$ins['is_active'] = ($_REQUEST['is_active'] === "1") ? 1: 0;

if ($_POST['mode'] == "add") {
	$DB->query('INSERT INTO ?_groups (?#) VALUES (?a)', array_keys($ins), array_values($ins));
	header("Cache-control: private");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit;
}
if ($_POST['mode'] == "edit") {
	$DB->query('UPDATE ?_groups SET ?a WHERE group_id=?', $ins, $id);
}
if ($_POST['mode'] == "delete") {
	$DB->query('DELETE FROM ?_groups WHERE group_id=?', $id);
	header("Cache-control: private");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit;
}

