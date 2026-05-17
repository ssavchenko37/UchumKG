<?php
$id = $_POST['pid'];
$avaFolder = S_ROOT . DIRECTORY_SEPARATOR . S_AVA . DIRECTORY_SEPARATOR;
foreach ($_REQUEST as $rKey => $rVal) {
	if (in_array($rKey, array("name","phone","pass"))) {
		$ins[$rKey] = trim($rVal);
	}
}

if (($_FILES['ava_img']['error'] ?? '') == 0) {
	$iTmp = explode(".", $_FILES['ava_img']['name']);
	$ext = end($iTmp);
	$iFile = "tutor_" . $iTmp[0] . "." . $ext;
	$target_file = $avaFolder . $iFile;

	include S_ROOT . "/__outsider/wideimage/WideImage.php";
	$image = WideImage::load('ava_img');
	$resized = $image->resize('96', '96', 'inside', 'down')->crop('center', 'middle', '96', '96');
	$resized->saveToFile($target_file);
	unset($image);
	$ins['ava_img'] = $iFile;
}

if ($_POST['mode'] == "add") {
	$ins['created'] = date('Y-m-d H:i:s');
	$DB->query('INSERT INTO ?_tutors (?#) VALUES (?a)', array_keys($ins), array_values($ins));
}
if ($_POST['mode'] == "edit") {
	$DB->query('UPDATE ?_tutors SET ?a WHERE tutor_id=?', $ins, $id);
}
if ($_POST['mode'] == "delete") {
	$tutor = $DB->selectRow('SELECT * FROM ?_tutors WHERE tutor_id=?', $id);
	p('Deleaem Delete');
	p($tutor);
	//@unlink ($avaFolder . $tutor['ava_img']);
	// $DB->query('DELETE FROM ?_groups WHERE tutor_id=?', $id);
	// $DB->query('DELETE FROM ?_tutors WHERE tutor_id=?', $id);
}