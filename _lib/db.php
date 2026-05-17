<?php ## Connect to DB.

require_once S_ROOT.'/__outsider/DbSimple/Connect.php';

$DB = new DbSimple_Connect(S_DSN_DEFAULT);
$DB->query("SET NAMES utf8mb4");
$DB->query("SET time_zone = '+06:00'");

$DB->setIdentPrefix(S_TABLE_PREFIX); // Set DB Prefix

$DB->setErrorHandler('databaseErrorHandler'); // set Errorbackend
function databaseErrorHandler($message, $info)
{
  // If you use @, do nothing.
  if (!error_reporting()) return;
  // Displays detailed information about the error.
  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $headers .= "From: TestEdu.KG \r\n";
  $msg  = "<br />SQL Error:<div style='font:12px Verdana;color:#C00;'>";
  $msg .= "<br />" . $info['code'];
  $msg .= "<br /><br />" . str_replace("*", "`*`", $info['message']);
  $msg .= "<br /><br />" . str_replace("*", "`*`", $info['query']);
  $msg .= "<br /><br />" . $info['context'];
  $msg .= "</div>";
  mail("ssavchenko@gmail.com", ".: TestEdu.KG site SQL error :.", $msg, $headers);

  echo "SQL Error:<div style='font:12px Verdana;color:#C00;width:80%;'>$message<br /><br /><pre>";
  print_r($info);
  echo "</pre></div>";
  //echo "<div style='font:10px Verdana;color:green;width:80%;'>Sorry, something wrong<br />Technical services will soon correct the errors<br /><pre>";
  exit();
}
