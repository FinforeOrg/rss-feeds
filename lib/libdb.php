<?php

if (!isset($_GET) && isset($HTTP_GET_VARS))
    $_GET = &$HTTP_GET_VARS;
if (!isset($_POST) && isset($HTTP_POST_VARS))
    $_POST = &$HTTP_POST_VARS;

class CConnection
{

    var $m_oConn;
    var $m_iLastSQLRecords;
    var $m_iLastError;
    var $m_sLastErrorDescription;

    function Connect()
    {
        global $servername, $username, $password, $dbname;

        $this->m_oConn = @mysql_connect($servername, $username, $password);
        if (!$this->m_oConn) {
            $this->m_sLast = mysql_errno();
            $this->m_sLastErrorDescription = mysql_error();
            echo "error = " . $this->m_sLastErrorDescription;
            return false;
        }
        if (!mysql_select_db($dbname, $this->m_oConn)) {
            $this->m_sLast = mysql_errno();
            $this->m_sLastErrorDescription = mysql_error();
            echo "dbselect error = " . $this->m_sLastErrorDescription;
            return false;
        }

        mysql_query("SET NAMES UTF8");
        mysql_query("SET CHARACTER_SET UTF8");

        return true;
    }

    function Close()
    {
        if ($this->m_oConn) {
            $this->m_oConn = false;
            return mysql_close();
        }
        return true;
    }

    function GetValue($sql, $column = false)
    {
        if (!($res = mysql_query($sql, $this->m_oConn))) {
            echo $sql;
            return false;
        }
        if ($column)
            return ($obj = mysql_fetch_object($res)) ? $obj->{$column} : false;
        else
            return ($row = mysql_fetch_row($res)) ? $row[0] : false;
    }

    function Execute($sql)
    {
        $res = mysql_query($sql, $this->m_oConn);
        if (!$res) {
            echo $sql;
            $this->m_sLast = mysql_errno();
            $this->m_sLastErrorDescription = mysql_error();
            return -1;
        }
        $this->m_iLastSQLRecords = mysql_affected_rows($this->m_oConn);
        return $res;
    }

    function FillCombo($sql, $defval)
    {
        $rs = mysql_query($sql, $this->m_oConn);
        while (($arr = mysql_fetch_array($rs)) != false) {
            echo '<option value="' . $arr[0] . '" ';
            if ($arr[0] == $defval)
                echo 'selected';
            echo '>' . $arr[1] . '</option>';
        }
    }

    function GetLastId()
    {
        return mysql_insert_id($this->m_oConn);
    }

}

;

$g_oConn = new CConnection;
$g_oConn->Connect();

class CRecordset
{

    var $m_oRs;
    var $m_oRow;
    var $m_bValid;

    function CRecordset($sql)
    {
        global $g_oConn;
        $this->m_oRs = $g_oConn->Execute($sql);
        $this->m_bValid = ($this->m_oRs != -1);
    }

    function MoveNext()
    {
        if (!$this->m_bValid)
            return false;
        return ($this->m_oRow = mysql_fetch_object($this->m_oRs)) != false;
    }

    function GetItem($name)
    {
        return $this->m_bValid && isset($this->m_oRow->{$name}) ? $this->m_oRow->{$name} : false;
    }

    function GetRecordCount()
    {
        return $this->m_bValid ? mysql_num_rows($this->m_oRs) : -1;
    }

    function MoveNextRow()
    {
        if (!$this->m_bValid)
            return false;
        return ($this->m_oRow = mysql_fetch_row($this->m_oRs)) != false;
    }

    function GetItemRow($pos)
    {
        return $this->m_bValid && isset($this->m_oRow[$pos]) ? $this->m_oRow[$pos] : false;
    }

}

;
?>