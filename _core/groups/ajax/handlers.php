<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re['status'] = "";
$re['done'] = 0;

if ($post['mode'] == "build_code") {
	$dict_codes = $DB->selectCol('SELECT code AS ARRAY_KEY, code FROM ?_dict');

	$tid = $DB->selectCell('SELECT tutor_id FROM ?_tutors WHERE tutor_id=?', $post['tutor_id']);

    
	$code[] = ($post['date_path'] === 0) ? "ddmmyy": $post['date_path'];
	$code[] = ($post['time_path'] === 0) ? "hhmm": $post['time_path'];
	$code[] = (empty($post['schedule_code'])) ? "SSS": $dict_codes[$post['schedule_code']];
	$code[] = (empty($post['format_code'])) ? "FFF": $dict_codes[$post['format_code']];
	$code[] = (empty($post['age_code'])) ? "AAA": $dict_codes[$post['age_code']];
	$code[] = str_pad($tid, "2", "0", STR_PAD_LEFT);

	$re['code'] = implode(".", $code);
	$re['status'] = "success";
}

echo json_encode($re);