<?php
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();



if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');