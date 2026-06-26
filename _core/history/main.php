<?php
$stud_id = $TL->request_decode('stud_id', $tlreq);

$dict = $TL->dict_codes();
p($dict['gstatus']);

$student = $DB->selectRow('SELECT * FROM ?_students WHERE stud_id=?', $stud_id);

$rows = $DB->select('SELECT A.appl_id, A.appl_code, A.app_hash'
	. ', GS.group_id, G.sess_hash, G.group_code, G.is_active'
	. ', T.name AS tutor_name, T.ava_img'
	. ' FROM ?_applications A'
	. ' LEFT JOIN ?_group_students GS ON A.appl_id=GS.appl_id'
	. ' LEFT JOIN ?_groups G ON GS.group_id=G.group_id'
	. ' LEFT JOIN ?_tutors T ON G.tutor_id=T.tutor_id'
	. ' WHERE A.stud_id=?'
	. ' ORDER BY A.created DESC'
	, $stud_id
);

p($rows);