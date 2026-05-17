<?php
if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$records = $DB->selectCol('SELECT stud_id AS ARRAY_KEY, COUNT(*) FROM ?_group_students GROUP BY stud_id');
$students = $DB->select('SELECT *, DATE_FORMAT(birthday, \'%d.%m.%Y\') AS bdate, DATE_FORMAT(sign_date, \'%d.%m.%Y %H:%i\') AS odate FROM ?_students ORDER BY last_name');
