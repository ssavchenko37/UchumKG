<?php
$ids = $_POST['ibooks'];
$re = array();
$re['status']  = 'error';
if ($_POST['mod'] == "delete") {
	foreach ($ids as $ibook_id) {
		$DB->query('DELETE FROM ?_ibook_temps WHERE ibook_id=?', $ibook_id);
		$DB->query('DELETE FROM ?_ibook_items WHERE ibook_id=?', $ibook_id);
		$DB->query('DELETE FROM ?_ibook_meta WHERE ibook_id=?', $ibook_id);
		$DB->query('DELETE FROM ?_ibook_units WHERE ibook_id=?', $ibook_id);
		$DB->query('DELETE FROM ?_ibook WHERE ibook_id=?', $ibook_id);
		$re['status']  = 'delete';
	}
}

if ($_POST['mod'] == "tutor") {
	foreach ($ids as $ibook_id) {
		$upd['tutor_id'] = $_POST['tutorid'];
		$DB->query('UPDATE ?_ibook SET ?a WHERE ibook_id=?', $upd, $ibook_id);
		$tutor = $TS->tutor($upd['tutor_id']);
		$re['tutorId'] = $upd['tutor_id'];
		$re['tutorName'] = $tutor['tutor']['tutor_fullru'];
	}
	$re['status']  = 'tutor';
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);