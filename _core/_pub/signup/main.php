<?php

$dict = $TL->dict();
$tutors = $DB->select('SELECT tutor_id AS ARRAY_KEY, phone, name, ava_img FROM ?_tutors');

$already = $DB->selectCol('SELECT group_id AS ARRAY_KEY, COUNT(stud_id) AS qty FROM ?_group_students GROUP BY group_id ORDER BY group_id');

$groups = $DB->select('SELECT G.*, A.*'
	. ' FROM ?_groups G'
	. ' INNER JOIN ?_dict A ON G.address_id=A.id'
	. ' WHERE G.status_id=? AND A.type=?'
	. ' ORDER BY created DESC, startime DESC'
	, 14, "address"
);
$result = [];
foreach ($groups as $r) {
	$aid = $r['address_id'];

	if (!isset($result[$aid])) {
		$result[$aid] = [
			'name' => $r['title'],
			'groups' => []
		];
	}

	if ($r['group_id']) {
		$result[$aid]['groups'][] = $r;
	}
}
ksort($result);
