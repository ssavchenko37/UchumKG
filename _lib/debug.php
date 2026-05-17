<?php
# 4 ............... Text

define ("CR", "<br />");

function p($outString, $isOut=true, $isHtmlspecialchars=false)
{
    // formiruem stroku dla vyvoda na erkan, kak massiv
    return nTraceVar($outString, $isOut, $isHtmlspecialchars);
}


// 2008-01-30
// formiruem stroku dla vyvoda na erkan, kak massiv
// kak p() tolko ne vyvoditsya na ekran a idet v peremennuyu
function pb(&$body, $outString, $isHtmlspecialchars=false)
{
    $body .= nTraceVar($outString, false, $isHtmlspecialchars);
    return true;
}
function pp($outString, $isHtmlspecialchars=false)
{
	$body .= nTraceVar($outString, false, $isHtmlspecialchars);
	return $body;
}



/**
 * Fast trace-debuger pring of vars
 *
 * Use short synonim p() instead this function
 * Creation: 2007-08-01     Last Changes: 2007-08-01
 *
 * @param   mixed    $outString             - string, integer, array, objest
 * @param   boolean  $isOut                 - echo or return trace-debuger var
 * @param   boolean  $isHtmlspecialchars    - usual or htmlspeciachars mode
 * @return  string                           - or null
 */
function nTraceVar($outString, $isOut=true, $isHtmlspecialchars=false)
{
    // esli peremennaja peredannaja dlja testovogo vyvoda javljaetsja massivom ili ob'ektom
    if (is_array($outString) || is_object($outString)) {
        // formiruem stroku dla vyvoda na erkan, kak massiv
        $outString = nTraceArrObj($outString, $isHtmlspecialchars);
        // esli zhe 'eto obychnaja peremennaja
    }
    // is var is boolean
    elseif (is_bool($outString))
    {
        // output it by oither way
        $outString = "\n<div style=\"margin: 1 0 1 0; font: xx-small tahoma; color: blue;\">]".(($outString) ? "true" : "false")."[</div>";
    }
    else
    {
        // if flag $isHtmlspecialchars set to true convert HTML tags to special chars
        if ($isHtmlspecialchars) $outString = htmlspecialchars($outString);
        // formiruem stroku dla vyvoda na erkan, dobavljaja sootvestvujushee okruzhenie
        $outString = "\n<div style=\"margin: 1 0 1 0; font: xx-small tahoma; color: red;\">]".$outString."[</div>";
    }

    // if flag $isOut set to true out var
    if ($isOut)
    {
        echo $outString;
        return true;
        // else (if flag $isOut set to false) - return $outString
    }
    else
    {
        return $outString;
    }
}




/**
 * Fast trace-debuger pring of arrays, objects for nTraceVar()
 *
 * Creation: 2007-08-01     Last Changes: 2007-08-01
 * 
 * @param   array    $outArray              - or object for trce-debuger output
 * @param   boolean  $isHtmlspecialchars    - usual or htmlspeciachars mode
 * @return  string                           - or null 
 */
function nTraceArrObj($outArray, $isHtmlspecialchars)
{
    $outString = "";
    $printRstring = "";

    ob_start();
    print_r($outArray);
    $printRstring .= CR.ob_get_contents();
    ob_end_clean();

    // if flag $isHtmlspecialchars set to true convert HTML tags to special chars
    if ($isHtmlspecialchars) $printRstring = htmlspecialchars($printRstring);

    // formiruem stroku dla vyvoda na erkan, dobavljaja sootvestvujushee okruzhenie
    $outString .= CR."<pre style=\"position:absolute; z-index:1020; margin: 2 0 2 0; font: xx-small tahoma; color:green;\">";
    $outString .= CR.$printRstring;
    $outString .= "</pre>".CR;

    return $outString;
}




/**
 * Convert win-1251 (russian) to UTF (russian)
 * 
 * Creation: 2007-08-01     Last Changes: 2007-08-01
 *
 * @param   string   source     - string in win-1251
 * @return  string   result     - string in utf-8
 */
function nEncWin2utf($sourceString)
{
    $resultString = "";

    for($i=0, $m=strlen($sourceString); $i<$m; $i++) {
        $c = ord($sourceString[$i]);
        if ($c <= 127) {$resultString .= chr($c); continue; }
        if ($c >= 192 && $c <= 207) {$resultString .= chr(208).chr($c-48); continue; }
        if ($c >= 208 && $c <= 239) {$resultString .= chr(208).chr($c-48); continue; }
        if ($c >= 240 && $c <= 255) {$resultString .= chr(209).chr($c-112); continue; }
        if ($c == 184) { $resultString .= chr(209).chr(209); continue; };
        if ($c == 168) { $resultString .= chr(208).chr(129); continue; };
    }
    return $resultString;
}




/**
 * Convert UTF (russian) to win-1251 (russian)
 * 
 * Creation: 2007-08-01     Last Changes: 2007-08-01
 *
 * @param   string   source     - string in win-1251
 * @return  string   result     - string in utf-8
 */
function nEncUtf2win($sourceString)
{
    static $table = array
    (
    "\xD0\x81" => "\xA8", // Ё
    "\xD1\x91" => "\xB8", // ё
    );
    $resultString = preg_replace('#([\xD0-\xD1])([\x80-\xBF])#se', 'isset($table["$0"]) ? $table["$0"] : chr(ord("$2")+("$1" == "\xD0" ? 0x30 : 0x70))', $sourceString );
    return $resultString;
}




/**
 * Incoming values
 * 
 * Return GET or POST or SESSION or DEFAULT_VAULE
 * Creation: 2007-08-01     Last changes: 2007-09-10
 *
 * @param   string   $varName      - name of index
 * @param   mixed    $defaultValue  - (optional) default value (or array of DK:formData or $_REQUEST from JsHttpRequest )
 * @return  mixed    
 */
function nVar($varName, $defaultValue=null)
{
    fdebug(__FUNCTION__, $varName);
    $is_test = 0;
    if (isset($defaultValue['items'][$varName])) {
        $retval = $defaultValue['items'][$varName]['value'];
        if ($is_test) p(1);
    } elseif (isset($defaultValue[$varName]) && is_array($defaultValue)) {
        $retval = $defaultValue[$varName];
        if ($is_test) { p(2); p($defaultValue[$varName]);}
    } elseif (isset($_POST[$varName])) {
        $retval = $_POST[$varName];
        if ($is_test) p(3);
    } elseif (isset($_GET[$varName])) {
        $retval = $_GET[$varName];
        if ($is_test) p(4);
    } elseif (isset($_SESSION[$varName])) {
        $retval = $_SESSION[$varName];
        if ($is_test) p(5);
    } elseif (isset($defaultValue)) {
        $retval = $defaultValue;
        if ($is_test) p(6);
    } else {
        return null;
    }
    return $retval;
}




/**
 * Get Setting value from table ?_hd_settings
 * 
 * Creation: 2007-08-21     Last Changes: 2007-08-21
 *
 * @param   string   $var       - name of variable
 * @return  mixed    
 */
function nGetSetting($var)
{
    global $DB;
    return (string) $DB->selectCell('SELECT value FROM ?_hd_settings WHERE name=?', (string) $var);
}




/**
 * Update Setting in table ?_hd_settings
 *
 * Creation: 2007-08-21     Last Changes: 2007-08-21
 *
 * @param   string   $var       - name of (setting) variable 
 * @param   string   $value     - new value
 * @return  mixed    
 */
function nUpdateSetting($var, $value)
{
    global $DB;
    return (string) $DB->query('UPDATE ?_hd_settings SET value=? WHERE name=?', (string)$value, (string)$var);
}





/**
 * Quick output of debug/trace string on working systems
 *
 * Synonim for nIsDebug()
 * Creation: 2007-09-16     Last Changes: 2007-09-16
 *
 * @param   boolean     $mode   - switch off functions, false - function return false
 * @return  boolean             - true or false
 */
function d($mode=true)
{
    return nIsDebug($mode);
}





/**
 * Quick output of debug/trace string on working systems
 *
 * Use short synonim d() instead this function
 * Creation: 2007-09-16     Last Changes: 2008-03-29
 *
 * @param   boolean     $mode   - switch off functions, false - function return false
 * @return  boolean             - true or false
 */
function nIsDebug($mode=true)
{
    $_isChecked = (boolean) false;
    if (isset($_SESSION['is_test']) && true === $_SESSION['is_test'])
    {
        $_isChecked = true;
    }
    return $_isChecked;
}




// 2007-11-11
function nGetBrowser($user_agent)
{
    global $CFG;
    if (is_array($CFG->browsers))
    {
        foreach ($CFG->browsers AS $ky=>$browser)
        {
            if (0 < substr_count($user_agent, $browser))
            {
                $CFG->browser_id = $ky;
                $CFG->browser_name = $browser;
                $CFG->browser_ua = $user_agent;
            }
        }
        if (!empty($CFG->browser_id)) return true;
        else return false;
    }
    else
    {
        return false;
    }
}




// 2007-11-11
function nGetCssForBrowser ($user_agent)
{
    global $CFG;

    if (!empty($CFG->browser_id))
    {
        $css_name = $CFG->browser_id;
    }
    else
    {
        nGetBrowser($user_agent);
        $css_name = $CFG->browser_id;
    }

    $path = "/css/".$css_name.".css";
    if (!empty ($css_name) && is_file(N_ROOT.$path))
    {
        $css = '<link rel="stylesheet" type="text/css" href="'.N_WWW.$path.'" />';
        #p($css);
        return $css;
    }
    else
    {
        return false;
    }
}




// 2007-11-26
function nTmplHeader ($options=array(), $mode='main')
{
    global $CFG;
    $template_name = $CFG->path_tmpl.'/'.$mode.'_header.php';
    //trace('Header Template', $template_name);
    return $template_name;
}




// 2007-11-26
function nTmplFooter($mode='main')
{
    global $CFG;
    $template_name = $CFG->path_tmpl.'/'.$mode.'_footer.php';
    //trace('Footer Template', $template_name);
    return $template_name;
}




// 2007-11-27
function e($error='Unknown Error')
{
    return nErrorMessage($error);
}




// 2007-11-27 - 2008-03-29
function nErrorMessage($error='Unknown Error!')
{
    //    return '<div class="error"><b>Error!</b><br />'.$error.'</div>';
    return '<div class="error">'.$error.'</div>';
}




//2007-12-15
function nInsertRowsInDb ($table, $rows, $mode='INSERT', $options=array())
{
    global $DB;
    fdebug(__FUNCTION__, $table.' ('.@count($rows).')');
    $link = $DB->link;
    $isStop = false;
    $isStripSlashes = false;
    $fieldsStr = '';
    $rowsStr = '';
    $rowStr = '';
    $affRows = 0;

    $isStripSlashes = (isset($options['stripslashes']) && true === $options['stripslashes']);


    // TODO: check $table name format should be:
    // `table_name` or `db_name`.`table_name`

    if (!$isStop && !is_array($rows))
    {
        // TODO: Error report
        $isStop = true;
    }

    if (!$isStop)
    {
        $fields = array_keys(current($rows));
        if (!is_array($fields))
        {
            // TODO: Error report
            $isStop = true;
        }
    }

    if (!$isStop)
    {
        foreach ($fields as $vl) $fieldsStr .= ', `'.$vl.'`';
        $fieldsStr = '('.substr($fieldsStr, 2).')';

        $rowsStr = '';
        foreach ($rows as $row)
        {
            $rowStr = '';
            if (is_array($row))
            {
                foreach ($row as $field)
                {
                    if ($isStripSlashes) $field = stripslashes($field);
                    $rowStr .= ", '".mysql_real_escape_string($field)."'";
                }
                $rowsStr .= ",\n(".substr($rowStr, 2).")";
            }
        }
        $rowsStr = substr($rowsStr, 1);

        $sql = $mode.' INTO '.$table.' '.$fieldsStr.' VALUES '.$rowsStr.'';
        $affRows = $DB->query($sql);
        //p($affRows);
        if (null === $affRows)
        {
            // TODO: limited error report
            $error = '('.mysql_errno($link).') '.mysql_error();
            //p($error);
            $isStop = true;
        }
    }

    //trace ('Affected rows ('.$table.')', $affRows);

    return ($isStop) ? false : $affRows;
}

// 2008-03-15
function nGetMicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    //p(microtime()); p($usec); p($sec);
    return ((float)$usec + (float)$sec);
}

// 2008-03-15
function nGetMicrotimeDate()
{
    list($usec, $sec) = explode(" ", microtime());
    return date("Y-m-d H:i:s", $sec).substr((string)$usec, 1);
}

// 2008-03-21
function nCreateDirsForFile($path)
{
    //fdebug(__FUNCTION__, $path);
    $dirs = explode('/', dirname($path));
    //p($dirs);
    if (is_array($dirs))
    {
        $parent = array_shift($dirs);
        foreach ($dirs as $ky=>$dir)
        {
            //p($parent.' - '.is_dir($parent));
            $current = $parent.'/'.$dir;
            if (is_dir($parent) && !is_dir($current))
            {
                mkdir($current, 0770);
            }
            $parent = $current;
        }
    }
}



function nSetUnsetTestMode()
{
    global $_IN, $_SESSION;
    //fdebug(__FUNCTION__);
    $isTest = false;
    
    if (isset($_IN['set_test_mode']) && 1 == $_IN['set_test_mode'])
    {
        $isTest = true;
    }
    else 
    {
        $isTest = (isset($_SESSION['is_test']) && true === $_SESSION['is_test']);
    }
    
    if (isset($_IN['unset_test_mode']) && 1 == $_IN['unset_test_mode'])
    {
        $isTest = false;
    }

    return $isTest;
}
?>