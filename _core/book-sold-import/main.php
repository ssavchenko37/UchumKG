<?php
/** @var array $tldata */

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
define('DOT', '.');

include S_ROOT . '/__outsider/Excel/PHPExcel.php';

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
if (!empty($_GET['sort_by_sale'])) {
	$sort = ' ORDER BY O.created_at ' . $_GET['sort_by_sale'];
} else {
	$sort = ' ORDER BY O.created_at DESC';
}
if (!empty($_GET['sort_by_paid'])) {
	$sort = ' ORDER BY P.comment ' . $_GET['sort_by_paid'];
}

$orders = $DB->select($sql.$sort, (empty($tutor_id)) ? DBSIMPLE_SKIP : $tutor_id);
$branches = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches');
$tutors = [];
foreach ($orders as $r) {
	$tutors[$r['tutor_id']] = $employee[$r['tutor_id']];
}

// Создаем объект Excel
$excel = new PHPExcel();

// Активный лист
$sheet = $excel->setActiveSheetIndex(0);

// Заголовки
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Дата продажи');
$sheet->setCellValue('C1', 'Сотрудник');
$sheet->setCellValue('D1', 'Филиал');
$sheet->setCellValue('E1', 'Доставка');
$sheet->setCellValue('F1', 'Телефон');
$sheet->setCellValue('G1', 'Кол-во');
$sheet->setCellValue('H1', 'Сумма');
$sheet->setCellValue('I1', 'Дата оплаты');
$sheet->setCellValue('J1', 'Продажа');

// Данные
$i = 2;
foreach ($orders as $r) {
	$sale_type = ($r['reservation_id'] > 0) ? "Бронь": "Прямая";
	$is_delivery = (empty($r['delivery_to'])) ? "На руки": "Доставка";
	$delivery = ($r['where_go'] == "hand") ? "": $r['delivery_to'];
	$delivery_type = 1;
	$delivery_type = ($r['reservation_id'] > 0 && empty($r['delivery_to'])) ? 2: $delivery_type;
	$delivery_type = ($r['reservation_id'] == 0) ? 3: $delivery_type;

	$sheet->setCellValue('A' . $i, '#');
	$sheet->setCellValue('B' . $i, $r['odate']);
	$sheet->setCellValue('C' . $i, $employee[$r['tutor_id']]);
	$sheet->setCellValue('D' . $i, $r['branch_name']);
	$sheet->setCellValue('E' . $i, $delivery . "\n" . $r['for_courier']);
	$sheet->setCellValueExplicit('F'.$i, $r['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue('G' . $i, $r['qty']);
	$sheet->setCellValue('H' . $i, $r['total_amount']);
	$sheet->setCellValue('I' . $i, $r['comment']);
	$sheet->setCellValue('J' . $i, $sale_type . "\n" . $is_delivery);
	$i++;
}

$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(52);
$sheet->getColumnDimension('F')->setWidth(18);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(20);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(20);

$sheet->getStyle('A1:J'.$i)->getAlignment()->setWrapText(true);
$sheet->getStyle('A1:J'.$i)->getFont()->setSize(12);
$sheet->getStyle('A1:J'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

// Название листа
$sheet->setTitle('Книги');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sold_books.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWriter->save('php://output');

exit;