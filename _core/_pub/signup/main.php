<?php

$dict = $TL->dict_codes();
$tutors = $DB->select('SELECT tutor_id AS ARRAY_KEY, phone, name, ava_img FROM ?_tutors');

$already = $DB->selectCol('SELECT group_id AS ARRAY_KEY, COUNT(stud_id) AS qty FROM ?_group_students GROUP BY group_id ORDER BY group_id');

$groups = $DB->select('SELECT G.*, D.*'
	. ' FROM ?_groups G'
	. ' INNER JOIN ?_dict D ON G.address_code=D.code'
	. ' WHERE G.group_code=? AND D.type=?'
	. ' ORDER BY created DESC, startime DESC'
	, "forming", "address"
);
$result = [];
foreach ($groups as $r) {
	$aid = $r['address_code'];

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
//p($result);