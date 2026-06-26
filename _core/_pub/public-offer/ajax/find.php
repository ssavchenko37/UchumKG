<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$phone = preg_replace('/[^\d+]/', '', $post['phone']); 
$phone = preg_replace('/^(?:\+996|0)/', '', $phone);
$phone = (empty($post['phone'])) ? '0000000': $phone;

$application = $DB->select('SELECT * FROM ?_applications WHERE phone LIKE(?)'
    , "%" . $phone . "%"
);

echo json_encode($application);
exit;