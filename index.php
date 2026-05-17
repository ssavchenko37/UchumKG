<?php
require_once 'kernel.php';

if(!isset($sPath)) {
	$sPath = array();
}

if (($sPath[1] ?? '') == "logout") {
	setcookie ("tl_sys", 0, time() - 3600, "/");
	header("Location: /");
	die();
}

$tldata = $TL->tldata();

if (is_array($tldata)) {
	if ($sPath[1] === 'ajx' && $_SERVER['REQUEST_METHOD'] === 'POST') {
		if (empty($sPath[4])) {
			$handler = "_core/" . $sPath[2] . "/ajax/" . $sPath[3] . ".php";
		} else {
			$handler = "_core/" . $sPath[2] . "/" . $sPath[3] . "/ajax/" . $sPath[4] . ".php";
		}
		if (is_file($handler)) {
			require $handler;
		}
		exit;
	}
}

include 'core.php';

if ($tldata['umod'] !== "pub") {
	include "inner.php";
} else {
	include "innex.php";
}