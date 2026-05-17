<?php
/************************************************************************** ctrl cookies data
 * @param array $string
 * @return boolean
 */
function setPeriod($prd)
{
	setcookie ("ts_prd", code2cook(['period' => $prd]), 0, "/");
	return true;
}
/************************************************************************** ctrl cookies data
 * @param array $arr
 * @return string
 */
function code2cook($arr)
{
	$str = base64_encode(json_encode($arr, JSON_UNESCAPED_UNICODE));
	$bp = substr($str, 0, 8);
	$ep = str_replace("=","_",substr($str, 8));
	return $ep . $bp;
}
/************************************************************************** ctrl cookies data
 * @param string $str
 * @return object
 */
function cook2code($str)
{
	$bp = substr($str, -8);
	$ep = str_replace($bp, "", $str);
	$ep = str_replace("_", "=", $ep);
	return json_decode(base64_decode($bp . $ep));
}
/************************************************************************** ctrl cookies data
 * @param array $arr
 * @return string
 */
function auth2cook($arr)
{
	$data = json_encode($arr, JSON_UNESCAPED_UNICODE);
	$sign = hash_hmac('sha256', $data, TL_SECRET);
	$str = base64_encode($data . "|" . $sign);
	$bp = substr($str, 0, 8);
	$ep = str_replace("=","_",substr($str, 8));
	return $ep . $bp;
}
/************************************************************************** ctrl cookies data
 * @param string $str
 * @return object
 */
function cook2auth($str)
{
	$bp = substr($str, -8);
	$ep = str_replace($bp, "", $str);
	$ep = str_replace("_", "=", $ep);
	$decoded = base64_decode($bp . $ep);
	[$data, $sign] = explode('|', $decoded);
	$validSign = hash_hmac('sha256', $data, TL_SECRET);
	return (hash_equals($validSign, $sign)) ? json_decode($data): null;
}


function getPath()
{
	$ReqUri = getenv("REQUEST_URI");
	$uri_arr = explode('?', $ReqUri);
	$uri = $uri_arr[0];
	$last_symbol = substr($uri, -1);
	if ($last_symbol == "/") $uri = substr($uri, 0,-1);
	$path_arr = explode('/', $uri);
	$pre_lang = ( in_array($uri_arr[1], array("ru","en","kg")) ) ? $uri_arr[1] : "" ;
	$last = end($path_arr);

	return array( $path_arr, $last, ($uri_arr[1] ?? '') );
}

function cleanPhone1($phone) {
	$phone = preg_replace('/\D+/', '', trim($phone));
	$phone = ltrim($phone, '0');
	return substr($phone, -9);
}

function cleanPhone($phone) {
	$phone = preg_replace('/[^0-9+]/', '', $phone);
	if (strpos($phone, '+') === 0) {
		return $phone;
	}
	if (strpos($phone, '0') === 0) {
		return '+996' . substr($phone, 1);
	}
	if (strpos($phone, '8') === 0) {
		return '+7' . substr($phone, 1);
	}
	if (strpos($phone, '7') === 0) {
		return '+' . $phone;
	}
	if (strpos($phone, '996') === 0) {
		return '+' . $phone;
	}
	return null;
}

/************************************************************************** NAV for Admin with permissions
 * @param array $p_path
 * @param string $needle
 * @return boolean
 */
function protect_nav($p_path, $needle) {
	return in_array($needle, $p_path) || $_SESSION["admlvl"] == 1;
}


/**
 * @param  $start
 * @return array
 */
function scanFolder($start) {
	chdir($start);
	$files = array();
	$handle = opendir('.');
	while (false !== ($file = readdir($handle))) {
		if ($file != '.' && $file != '..' && $file != 'Thumbs.db') {
			array_push($files, $file);
		}
	}
	closedir($handle);
	return $files;
}
/**
 * @param  $start
 * @return array
 */
function scanImages($start) {
	chdir($start);
	$files = array();
	$handle = opendir('.');
	while (false !== ($file = readdir($handle))) {
		if ($file != '.' && $file != '..' && $file != 'Thumbs.db') {
			array_push($files, $file);
		}
	}
	closedir($handle);
	return $files;
}

/************************************************************************** Show Info Box
 * @param string $msg
 * @param bool|string $mode
 * @return string $str
 */
function ShowInfo($msg, $mode=false) {
	if( !$mode ) {
		$mode = "success";
	}
	echo "<div id=\"InfoInTop\" class=\"top-note bg-" . $mode . " text-white rounded-3\">" . $msg . " </div>";
}


/************************************************************************** String to uin
 * @param string $string
 * @return string $string
 */
function rus2translit($string) {
	$converter = array(
		'а' => 'a',   'б' => 'b',   'в' => 'v',
		'г' => 'g',   'д' => 'd',   'е' => 'e',
		'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
		'и' => 'i',   'й' => 'y',   'к' => 'k',
		'л' => 'l',   'м' => 'm',   'н' => 'n',
		'о' => 'o',   'п' => 'p',   'р' => 'r',
		'с' => 's',   'т' => 't',   'у' => 'u',
		'ф' => 'f',   'х' => 'h',   'ц' => 'c',
		'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
		'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
		'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

		'А' => 'A',   'Б' => 'B',   'В' => 'V',
		'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
		'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
		'И' => 'I',   'Й' => 'Y',   'К' => 'K',
		'Л' => 'L',   'М' => 'M',   'Н' => 'N',
		'О' => 'O',   'П' => 'P',   'Р' => 'R',
		'С' => 'S',   'Т' => 'T',   'У' => 'U',
		'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
		'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
		'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
		'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
	);
	return strtr($string, $converter);
}
/************************************************************************** String to uin
 * @param string $string
 * @return string $string
 */
function str2uin($string)
{
	$string = rus2translit($string);
	$string = strtolower($string);
	$string = preg_replace("/[.,!;?«»']/", "", $string);
	$string = str_replace("-"," ",$string);
	$string = preg_replace("/[^\w\x7F-\xFF\s]/", "", $string);
	$string = str_replace("\\","",$string);
	$string = str_replace(" ","-",$string);
	return str_replace("--","-",$string);
}
/************************************************************************** Select val = val
 * @param string $Val
 * @param array $arr
 * @return string $str
 */
function getOptions($Val, $arr) {
  $str = "";
  foreach ($arr as $sVal) {
    $selected = ($Val == $sVal) ? " selected" : "";
    $str .= "<option value='" . $sVal . "'" . $selected . ">" . $sVal . "</option>\n";
  }
  return $str;
}
/************************************************************************** Select key = val
 * @param string $Val
 * @param array $arr
 * @return string $str
 */
function getOptionsK($Val, $arr) {
  $str = "";
  foreach ($arr as $sKey => $sVal) {
    $selected = ($Val == $sKey) ? " selected" : "";
    $str .= "<option value='" . $sKey . "'" . $selected . ">" . $sVal . "</option>\n";
  }
  return $str;
}
/************************************************************************** Select key = val
 * @param string $val
 * @param array $arr
 * @return string $str
 */
function getOptionsKData($Val, $arr) {
	$str = "";
	foreach ($arr as $arrkey => $a) {
		$datas = "";
		foreach ($a as $k => $v) {
			if ($k != 'title') {
				$datas .= " data-" . $k . "='" . $v . "'";
			}
		}
		$selected = ($Val == $arrkey) ? " selected" : "";
		$str .= "<option " . $datas . " value='" . $arrkey . "'" . $selected . " >" . $a['title'] . "</option>\n";
	}
	return $str;
}
/************************************************************************** Select key = val
 * @param string $Val
 * @param array $arr
 * @return string $str
 */
function getOptionsClass($Val, $arr) {
	$str = "";
	foreach ($arr as $sKey => $sVal) {
		$selected = ($Val == $sKey) ? " selected" : "";
		$str .= "<option class='" . $sVal[1] . "' value='" . $sKey . "'" . $selected . ">" . $sVal[0] . "</option>\n";
	}
	return $str;
}
/************************************************************************** Select key = val
 * @param string $val
 * @param array $arr
 * @return string $str
 */
function getOptionsData($val, $arr) {
	$str = "";
	foreach ($arr as $arrkey => $a) {
		$datas = "";
		foreach ($a as $k => $v) {
			if ($k != 'title') {
				$datas .= " data-" . $k . "='" . $v . "'";
			}
		}
		$selected = ($val == $arrkey) ? " selected" : "";
		$str .= "<option " . $datas . " value='" . $arrkey . "'" . $selected . " >" . $a['title'] . "</option>\n";
	}
	return $str;
}
/************************************************************************** Select key = val
 * @param array $exists
 * @param array $arr
 * @return string $str
 */
function getOptionsMultiple($exists, $arr) {
	$str = "";
	foreach ($arr as $sKey => $sVal) {
		$selected = ( in_array( $sKey, $exists ) ) ? " selected" : "";
		$str .= "<option value='" . $sKey . "'" . $selected . ">" . $sVal . "</option>\n";
	}
	return $str;
}
/************************************************************************** Select key = val
 * @param string $val
 * @param array $arr
 * @return string $str
 */
function getOptionsMultipleData($exists, $arr) {
	$str = "";
	foreach ($arr as $arrkey => $a) {
		$datas = "";
		foreach ($a as $k => $v) {
			if ($k != 'title') {
				$datas .= " data-" . $k . "='" . $v . "'";
			}
		}
		$selected = ( in_array( $arrkey, $exists ) ) ? " selected" : "";
		$str .= "<option " . $datas . " value='" . $arrkey . "'" . $selected . " >" . $a['title'] . "</option>\n";
	}
	return $str;
}
/************************************************************************** Select key = val
 * @param string $Val
 * @param array $arr
 * @return string $str
 */
function getOptionsMulti($Val, $arr) {
  $str = "";
  $val_arr = (empty($Val)) ? array(): json_decode( $Val );
  foreach ($arr as $sKey => $sVal) {
    $selected = ( in_array( $sKey, $val_arr ) ) ? " selected" : "";
    $str .= "<option value='" . $sKey . "'" . $selected . ">" . $sVal . "</option>\n";
  }
  return $str;
}

/***************************************************************************************************
 * ************************************************************************** /adm/module, /t/module
*/
function adm_hidden_inputs() {
  global $module_need_inputs;
  $str = "";
  foreach( $_POST as $t_key=>$t_val ) {
    if( in_array($t_key, $module_need_inputs) ) {
      echo '<input type="hidden" id="' . $t_key . '" name="' . $t_key . '" value="' . $t_val . '">' . "\n";
    }
  }
}

/**
 * @param array $passed
 * @param array $exist
 * @return array
 */
function arrsIntersect($passed, $exist)
{
    $reArr['newArr'] = $reArr['delArr'] = array();
    foreach ($passed as $c) {
        if (!in_array($c, $exist)){
            $reArr['newArr'][] = $c;
        }
    }
    foreach ($exist as $e) {
        if (!in_array($e, $passed)){
            $reArr['delArr'][] = $e;
        }
    }
    return $reArr;
}

/**
 * @param $student
 * @return string
 */
function buildFIO($student) {
	$re = (!empty($student['last_name'])) ? $student['last_name']: "";
	$re = (!empty($student['first_name'])) ? $re . " " . $student['first_name']: $re;
	$re = (!empty($student['patronymic'])) ? $re . " " . $student['patronymic']: $re;
	
	return $re;
}

/**
 * @param $sname
 * @return string
 */
function studentShort($sname) {
	$re = "";
	$is_kyzy = strpos($sname, 'кызы');
	$is_uluu = strpos($sname, 'уулу');
	$names = explode("/", $sname);
	$str = (empty($names[1])) ? $names[0]: $names[1];
	if ($is_kyzy || $is_uluu) {
		$re = $str;
	} else {
		$arr = explode(" ", trim($str));
		$s1 = (!empty($arr[1])) ? mb_substr($arr[1], 0, 1, "utf-8") . "." : "";
		$s2 = (!empty($arr[2])) ? mb_substr($arr[2], 0, 1, "utf-8") . "." : "";
		$re = $arr[0] . " " . $s1 . $s2;
	}
	return $re;
}