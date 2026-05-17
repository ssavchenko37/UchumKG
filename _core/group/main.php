<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}
$MAXDays = 13;

$group_id = $TL->request_decode('group_id', $tlreq);

$group = $DB->selectRow('SELECT G.*, T.name, T.tel, T.ava_img'
	. ' FROM ?_groups G'
	. ' INNER JOIN ?_tutors T ON G.tutor_id=T.tutor_id'
	. ' WHERE group_id=?'
	, $group_id
);

$ava_img = (is_file(S_AVA . DIRECTORY_SEPARATOR . $group['ava_img'])) ? S_AVA . DIRECTORY_SEPARATOR . $group['ava_img']: S_AVA . DIRECTORY_SEPARATOR . "no-ava.png";

$students = $DB->select('SELECT S.*'
	. ', A.appl_id, A.status, A.app_hash'
	. ', GS.group_id'
	. ' FROM ?_students S'
	. ' INNER JOIN ?_applications A ON S.stud_id=A.stud_id'
	. ' INNER JOIN ?_group_students GS ON A.appl_id=GS.appl_id'
	. ' INNER JOIN ?_groups G ON GS.group_id=G.group_id'
	. ' WHERE GS.group_id=?'
	. ' ORDER BY S.name'
	, $group_id
);

$items = array();
$tmp_items = $DB->select('SELECT * FROM ?_ibook_items WHERE group_id=?', $group_id);

foreach ($tmp_items as $iv) {
	$items[$iv['item_uin']][$iv['stud_id']] = $iv;
}

$meta = $DB->select('SELECT meta_uin AS ARRAY_KEY, meta_id, group_id, meta_date, DATE_FORMAT(meta_date, \'%d.%m\') AS tdate, DATE_FORMAT(meta_date, \'%H:%i\') AS ttime, meta_topic, meta_cancelled'
	. ' FROM ?_ibook_meta'
	. ' WHERE group_id=?'
	, $group_id
);
