<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$search_name = (empty($post['name'])) ? 'XXXXXXX': $post['name'];
$search_phone = (empty($post['phone'])) ? '0000000': $post['phone'];

$students = $DB->select('SELECT * FROM ?_students WHERE first_name LIKE(?) OR last_name LIKE(?) OR phone LIKE(?)'
    , "%" . $search_name . "%", "%" . $search_name . "%", "%" . $search_phone . "%"
);

echo json_encode($students);
exit;