<?
class CConnection
{
	var $m_oConn;
	var $m_iLastSQLRecords;
	var $m_iLastError;
	var $m_sLastErrorDescription;
	
	function Connect()
	{
		global $servername,$username,$password,$dbname;
		$this->m_oConn = @mysql_connect($servername,$username,$password);
		if (!$this->m_oConn)
		{
			$this->m_sLast=mysql_errno();
			$this->m_sLastErrorDescription=mysql_error();
			return false;
		}
		if (!mysql_select_db($dbname,$this->m_oConn)) 
		{
			$this->m_sLast=mysql_errno();
			$this->m_sLastErrorDescription=mysql_error();			
			return false;
		}
    
		mysql_query("SET NAMES UTF8");
    mysql_query("SET CHARACTER_SET UTF8");

		return true;
	}

	function Close()
	{
		return $this->m_oConn ? mysql_close() : true;
	}
	function GetValue($sql, $column = false)
	{
		if (!($res = mysql_query($sql,$this->m_oConn)))
		{ 
			// echo $sql;
			return false;
		}
		if ($column) return ($obj = mysql_fetch_object($res)) ? $obj->{$column} : false;
		else return ($row = mysql_fetch_row($res)) ? $row[0] : false;
	}

	function Execute($sql)
	{
		$res = mysql_query($sql,$this->m_oConn);
		if (!$res)
		{
			// echo $sql; 
			$this->m_sLast=mysql_errno();
			$this->m_sLastErrorDescription=mysql_error();			
			return -1;
		}
		$this->m_iLastSQLRecords = mysql_affected_rows($this->m_oConn);
		return $res;
	}	
	
	function GetLastId()
	{
		return mysql_insert_id($this->m_oConn);
	}
};

$g_oConn = new CConnection;
$g_oConn->Connect();


function FillCombo($sql,$value=0)
{
	$rs = new CRecordset($sql);
	while ($rs->MoveNext())
		echo "<option ".($rs->GetItem('value')==$value?"selected":"")." value='".$rs->GetItem('value')."'>".$rs->GetItem('text')."</option>";
}

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
		if (!$this->m_bValid) return false;
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
	
};
?>