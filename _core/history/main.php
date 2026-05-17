<?php
$stud_id = $TL->request_decode('stud_id', $tlreq);

$student = $DB->selectRow('SELECT * FROM ?_students WHERE stud_id=?', $stud_id);

$rows = $DB->select('SELECT A.appl_id, A.status AS apl_status, A.app_hash'
	. ', GS.group_id, G.sess_hash, G.status AS group_status, G.is_active'
	. ', T.name AS tutor_name, T.ava_img'
	. ' FROM ?_applications A'
	. ' LEFT JOIN ?_group_students GS ON A.appl_id=GS.appl_id'
	. ' LEFT JOIN ?_groups G ON GS.group_id=G.group_id'
	. ' LEFT JOIN ?_tutors T ON G.tutor_id=T.tutor_id'
	. ' WHERE A.stud_id=?'
	. ' ORDER BY A.created DESC'
	, $stud_id
);
