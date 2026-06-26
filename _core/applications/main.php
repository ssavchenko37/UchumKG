<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$dict = $TL->dict_codes();

$search_str = '';
if (!empty($_POST['schstr'])) {
	$schstr = trim($_POST['schstr']);
	$schstr = mb_strtolower($schstr, 'UTF-8');
		if (strlen($_POST['schstr']) > 2) {
		$search_str = "%" . $schstr . "%";
	}
}

$applications = $DB->select('SELECT A.*, DATE_FORMAT(A.created, \'%d.%m.%Y %H:%i\') AS created_date'
	. ', G.sess_hash, DATE_FORMAT(G.startime, \'%H:%i\') AS stime, T.name AS tutor_name'
	. ' FROM ?_applications A'
	. ' LEFT JOIN ?_groups G ON A.group_id=G.group_id'
	. ' LEFT JOIN ?_tutors T ON G.tutor_id=T.tutor_id'
	. ' WHERE 1=1'
	. ' {AND S.tel LIKE ? OR LOWER(S.name) LIKE ?}'
	. ' ORDER BY A.created DESC'
	, (empty($search_str)) ? DBSIMPLE_SKIP : $search_str, $search_str
);

// p($applications);
