<?php
/** @var array $tldata */
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$tutor_id = ($tldata['umod'] === 't') ? $tldata['id']: '';

$reservations = $BS->getPayments();
// p($reservations[0]);
$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');