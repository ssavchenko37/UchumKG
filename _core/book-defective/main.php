<?php
require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$tmp_tutors = $DB->selectCol('SELECT tutor_id AS ARRAY_KEY, name FROM ?_tutors');
$tmp_admins = $DB->selectCol('SELECT admin_id AS ARRAY_KEY, name FROM ?_admins');
$employee = $tmp_tutors + $tmp_admins;

$defects = $DB->select('SELECT D.*, DATE_FORMAT(D.created_at, \'%d.%m.%Y %H:%i\') AS createdAt, DATE_FORMAT(D.updated_at, \'%d.%m.%Y %H:%i\') AS updatedAt
	, B.title
	, BR.name AS branch_name
	FROM ?_bk_defects D
	JOIN ?_bk_books B ON D.book_id=B.book_id
	JOIN ?_bk_branches BR ON D.branch_id = BR.branch_id
	ORDER BY D.updated_at DESC'
);
// p($defects);