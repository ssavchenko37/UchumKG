<?php
/** @var array $tlreq */
/** @var array $tldata */

require S_ROOT . '/__outsider/book_full/bootstrap.php';
use Outsider\Book\Services\BookService;
$BS = new BookService();

if (($_POST['mode'] ?? '')) {
	include "action/action.php";
}

$sort_by = [
	"sale" => [""=>"не сортировать","DESC"=>"По убыванию даты","ASC"=>"По возрастанию даты"],
	"employee" => [""=>"не сортировать","ASC"=>"Сортировка от А до Я","DESC"=>"Сортировка от Я до А"],
	"paid" => [""=>"не сортировать","DESC"=>"По убыванию даты","ASC"=>"По возрастанию даты"],
];

if ($_POST) {
	foreach ($_POST as $rKey => $rVal) {
		if (in_array($rKey, array("sort_by_sale","sort_by_paid"))) {
			$sort_val[$rKey] = $rVal;
		}
	}
} else {
	$sort_val['sort_by_sale'] = 'DESC';
}

$order_id = $TL->request_decode('order_id', $tlreq);
$tutor_id = null;
if ($tldata['umod'] === 't') {
	$tutor_id = $tldata['id'];
}
$tmp_tutors = $DB->selectCol('SELECT tutor_id AS ARRAY_KEY, name FROM ?_tutors');
$tmp_admins = $DB->selectCol('SELECT admin_id AS ARRAY_KEY, name FROM ?_admins');
$employee = $tmp_tutors + $tmp_admins;

$sql = 'SELECT O.order_id, O.tutor_id, O.status, O.total_amount, O.delivery_to, O.created_at
, DATE_FORMAT(O.created_at, \'%d.%m.%Y %H:%i\') AS odate
, I.qty
, P.reservation_id, P.amount, P.method, P.paid_at, P.phone, P.comment
, BR.branch_id, BR.name AS branch_name
, B.book_id, B.title, B.author, B.price
, R.for_courier
FROM ?_bk_orders O
JOIN ?_bk_order_items I ON O.order_id = I.order_id
JOIN ?_bk_payments P ON O.payment_id = P.payment_id
JOIN ?_bk_branches BR ON O.branch_id = BR.branch_id
JOIN ?_bk_books B ON I.book_id = B.book_id
LEFT JOIN ?_bk_reservations R ON P.reservation_id = R.reservation_id
WHERE 1=1 {AND O.tutor_id=?}';
$sort = '';

if (!empty($sort_val['sort_by_sale'])) {
	$sort = ' ORDER BY O.created_at ' . $sort_val['sort_by_sale'];
} else {
	$sort = ' ORDER BY O.created_at DESC';
}
if (!empty($sort_val['sort_by_paid'])) {
	$sort = ' ORDER BY P.comment ' . $sort_val['sort_by_paid'];
}


$orders = $DB->select($sql.$sort , (empty($tutor_id)) ? DBSIMPLE_SKIP : $tutor_id);
p(count($orders));


$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');

$tutors = [];
foreach ($orders as $r) {
	$tutors[$r['tutor_id']] = $employee[$r['tutor_id']];
}