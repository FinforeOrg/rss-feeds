<?php
include(dirname(__FILE__)."/geoip.inc");

function getCountryCodeByIP($sIP)
{	
	if (!strlen($sIP)) return "";
	$gi = geoip_open(dirname(__FILE__)."/GeoIP.dat",GEOIP_STANDARD);
	$sResult = geoip_country_code_by_addr($gi, $sIP);	
	geoip_close($gi);
	return $sResult;	
}

function getCountryNameByIP($sIP)
{
	if (!strlen($sIP)) return "";
	
	$gi = geoip_open(dirname(__FILE__)."/GeoIP.dat",GEOIP_STANDARD);
	$sResult = geoip_country_name_by_addr($gi, $sIP);
	geoip_close($gi);
	return $sResult;
}
?>