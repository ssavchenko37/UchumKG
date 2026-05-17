<?php
/** @var array $sPath */
/** @var array $tldata */

if ($tldata['umod'] == 't') {
	$access = ['','groups','book-selling','book-selling-reserve','book-sold'];
	if (!in_array($sPath[1], $access)) {
		header("Cache-control: private");
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /");
		exit;
	}
}

$js_path = json_encode($sPath);
$sCore = (!empty( $sPath[1] )) ? $sPath[1] : 'home';
// $sCore = (isset($_POST['login_mode']) && $_POST['login_mode'] === "login") ? 'home': $sCore;
$sMod = "_" . $tldata['umod'];
$sCore = (empty( $sPath[1] ) && !empty(($tldata['umod'] ?? ''))) ? $sMod : $sCore;

$userName = $tldata['usr']['name'];
$globalMode = true;

if (in_array($tldata['umod'], ["s", "pub"])) {
	if (!empty( $sPath[1] )) {
		$globalMode = false;
	}
}
if (is_file(S_ROOT . "/_core/" . $sCore . "/main.php") && $globalMode) {

	include S_ROOT . "/_core/" . $sCore . "/main.php";
}
if (is_file(S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/main.php")) {
	include S_ROOT . "/_core/" . $sMod . "/" . $sCore . "/main.php";
}