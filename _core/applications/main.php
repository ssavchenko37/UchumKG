<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$records = $DB->selectCol('SELECT stud_id AS ARRAY_KEY, COUNT(*) FROM ?_group_students GROUP BY stud_id');
$search_str = '';
if (!empty($_POST['schstr'])) {
	$schstr = trim($_POST['schstr']);
	$schstr = mb_strtolower($schstr, 'UTF-8');
		if (strlen($_POST['schstr']) > 2) {
		$search_str = "%" . $schstr . "%";
	}
}

$applications = $DB->select('SELECT S.*, DATE_FORMAT(S.sign_date, \'%d.%m.%Y %H:%i\') AS odate'
	. ', A.*, A.group_id AS gid'
	. ', GS.group_id, G.sess_hash'
	. ' FROM ?_applications A'
	. ' INNER JOIN ?_students S ON A.stud_id=S.stud_id'
	. ' LEFT JOIN ?_group_students GS ON A.appl_id=GS.appl_id'
	. ' LEFT JOIN ?_groups G ON GS.group_id=G.group_id'
	. ' WHERE 1=1'
	. ' {AND S.tel LIKE ? OR LOWER(S.name) LIKE ?}'
	. ' ORDER BY A.created DESC'
	, (empty($search_str)) ? DBSIMPLE_SKIP : $search_str, $search_str
);
// p($applications);
