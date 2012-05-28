<?php
session_start();
require_once dirname(__FILE__) . "/../../config/database.php";
require_once dirname(__FILE__) . "/../../config/config.php";
require_once dirname(__FILE__) . "/libdb.php";
require_once dirname(__FILE__) . "/list.php";
require_once dirname(__FILE__) . "/menu.php";

function MonthYearPickerParse($prefix, &$hash)
{
  global $_GET;

  $day = 1;
  $month = isset($hash[$prefix . "_month"]) ? intval($hash[$prefix . "_month"]) : 0;
  $year = isset($hash[$prefix . "_year"]) ? intval($hash[$prefix . "_year"]) : 0;
  if (checkdate($month, $day, $year))
    return sprintf("%04d-%02d-%02d", $year, $month, $day);
  return false;
}

function ShowMonthYearPicker($prefix, $value, $flag=false, $class=false, $bIsExpDate = false)
{
  $day = $month = $year = 0;
  if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $value, $regs))
  {
    $year = intval($regs[1]);
    $month = intval($regs[2]);
    $day = intval($regs[3]);
  }

  $class = $class ? $class : "";
  $sStartDate = $bIsExpDate ? date("Y") : "2007";
  ?>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <select class="<?= $class ?>" name="<?= $prefix ?>_month">
          <option value="0">month
  <? for ($i = 1; $i < 13; $i++)
    echo "<option " . ($month == $i ? "selected" : "") . " value='$i'>$i"; ?>
        </select>
      </td>
      <td class="b12" rowspan="2">&nbsp;&nbsp;</td>
      <td>
        <select class="<?= $class ?>" name="<?= $prefix ?>_year">
          <option value="0">year
            <?
            if ($flag)
              for ($i = 1920; $i < 2005; $i++)
                echo "<option " . ($year == $i ? "selected" : "") . " value='$i'>$i";
            else
              for ($i = $sStartDate; $i < 2030; $i++)
                echo "<option " . ($year == $i ? "selected" : "") . " value='$i'>$i";
            ?>
        </select>
      </td>
      <!--td class="b12" rowspan="2">&nbsp;&nbsp;</td>
      <td>
        <select class="<?= $class ?>" name="<?= $prefix ?>_day">
  					<option value="0">day
  <? for ($i = 1; $i < 32; $i++)
    echo "<option " . ($day == $i ? "selected" : "") . " value='$i'>$i"; ?>
  				</select>
  			</td-->
    </tr>
  </table>
  <?
}

function IsValidEmail($sMail)
{
  global $REGISTER_EMAILS;
  $exp = explode("@", $sMail);
  if (!is_array($exp) || count($exp) != 2 || in_array(strtolower($exp[1]), $REGISTER_EMAILS))
    return false;
  return true;
}

function Post($nm, $def="")
{
  return isset($_POST[$nm]) ? trim($_POST[$nm]) : $def;
}

function Get($nm, $def="")
{
  return isset($_GET[$nm]) ? trim(rawurldecode($_GET[$nm])) : $def;
}

function ChkPost($nm, $vl)
{
  return isset($_POST[$nm]) && $_POST[$nm] == $vl;
}

function ChkGet($nm, $vl)
{
  return isset($_GET[$nm]) && $_GET[$nm] == $vl;
}

function CheckEmail($email)
{
  return preg_match("/^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]+$/i", $email);
}

function Redirect($url, $header=true)
{
  global $g_oConn;
  $g_oConn->Close();
  if ($header)
    header("Location: " . $url);
  else
    echo "<script>window.location='" . $url . "';</script>";
  die();
}

function FillArCombo($ar, $value=0)
{
  foreach ($ar as $k => $v)
    echo "<option " . ($k == $value ? "selected" : "") . " value='" . $k . "'>" . $v . "</option>";
}

function CheckRegExpr($txt, $srch)
{
  preg_match('/' . $srch . '/iusU', $txt, $fnd);
  return count($fnd) > 0;
}

function vSql($sValue)
{
  //$sValue = stripslashes($sValue); //Note: If magic_quotes_gpc is enabled, first apply stripslashes() to the data. Using this function on data which has already been escaped will escape the data twice.
  return "'" . mysql_real_escape_string($sValue) . "'";
}

function vSqlStr($sValue)
{
  //$sValue = stripslashes($sValue); //Note: If magic_quotes_gpc is enabled, first apply stripslashes() to the data. Using this function on data which has already been escaped will escape the data twice.
  return "'" . mysql_real_escape_string($sValue) . "'";
}

function vSqlInt($sValue)
{
  return intval($sValue);
}

function vSqlFloat($sValue)
{
  return floatval($sValue);
}

function vDb($sValue)
{
  return stripslashes($sValue);
}

function isValidUrl($sUri)
{
  $urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
  if (eregi($urlregex, $sUri))
    return true;
  else
    return false;
}

function IpCheckingLink($sIp)
{
  return '<a target="_blank" href="http://ipinfodb.com/ip_locator.php?ip=' . $sIp . '">' . $sIp . '</a>';
}
?>