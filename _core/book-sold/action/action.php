<?php
/** @var array $tldata */
$admin_id = ($tldata['umod'] == 'a') ? $tldata['id']: 1;
$id = $_POST['pid'];

if ($_POST['mode'] === "edit_order") {

	$BS->rollbackSale($id, $admin_id);

}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;