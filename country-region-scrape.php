<?php

/*
 * Country-region
 */

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nStarting...";

echo "\nTruncate tables";
$g_oConn->Execute("TRUNCATE country_region");

################################################################################
# Curl session
################################################################################
$url = "http://unstats.un.org/unsd/methods/m49/m49regin.htm";
//$url = "file://savedpages\\m49regin.htm";
# initialize the curl session
echo "\nCurl session";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");
curl_setopt($ch, CURLOPT_REFERER, "");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: en"));
$html = curl_exec($ch);

// Check if any error occured
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    die();
}
$html = str_get_html($html);

################################################################################
# Geographical region and composition
################################################################################
echo "\n\nGeographical region and composition";
$table = $html->find('table tr td.cheader2', 0)->parent->parent;

$world_id = 0;
$continent_id = 0;
$region_id = 0;
foreach ($table->find('tr') as $tr) {
    if (sizeof($tr->find('td.cheader2', 0)))
        continue;

    $text = $tr->find('td', 0)->plaintext;
    $text = html_entity_decode($text);
    $text = preg_replace("/\s+/si", " ", $text);
    $text = trim($text);
    $text = preg_replace("/[a-b]?\//si", "", $text);

    $code = trim(preg_replace("/[^\d]*/si", "", $text));
    $name = trim(preg_replace("/\d*/si", "", $text));

    if (!strlen($name))
        continue;

    // World
    if (sizeof($tr->find('td.content strong', 0))) {
        $sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region) VALUES (" .
                intval($code) .
                ",'" . mysql_real_escape_string($name) . "'" .
                ",0" .
                ",NULL" .
                ");";
        $g_oConn->Execute($sql);
        $world_id = $g_oConn->GetLastId();
        continue;
    }

    // Continent
    if (sizeof($tr->find('td.content', 0))) {
        $sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region) VALUES (" .
                intval($code) .
                ",'" . mysql_real_escape_string($name) . "'" .
                "," . intval($world_id) . "" .
                ",NULL" .
                ");";
        $g_oConn->Execute($sql);
        $continent_id = $g_oConn->GetLastId();
        continue;
    }

    // Region
    if (sizeof($tr->find('td.tcont', 0)) || sizeof($tr->find('td.lcont', 0))) {
        $sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region) VALUES (" .
                intval($code) .
                ",'" . mysql_real_escape_string($name) . "'" .
                "," . intval($continent_id) . "" .
                ",NULL" .
                ");";
        $g_oConn->Execute($sql);
        $region_id = $g_oConn->GetLastId();
        continue;
    }
}
/**
 * b/ The continent of North America (003) comprises:
 * Northern America (021), Caribbean (029), and Central America (013).  
 */
# Manually insert the continent of North America. Point b/
$sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region, other_region) VALUES (" .
        intval('003') . // North America code
        ",'" . mysql_real_escape_string('North America') . "'" .
        "," . intval('1') . "" .
        ",NULL" .
        ",1" .
        ");";
$g_oConn->Execute($sql);
$north_america_id = $g_oConn->GetLastId();

# Manually insert Regions for the continent of North America
$sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region, other_region) VALUES " .
        " (" . intval('021') . ",'" . mysql_real_escape_string('Northern America') . "'" . "," . intval($north_america_id) . "" . ",NULL" . ", 1)" .
        ",(" . intval('029') . ",'" . mysql_real_escape_string('Caribbean') . "'" . "," . intval($north_america_id) . "" . ",NULL" . ", 1)" .
        ",(" . intval('013') . ",'" . mysql_real_escape_string('Central America') . "'" . "," . intval($north_america_id) . "" . ",NULL" . ", 1)" .
        ";";
$g_oConn->Execute($sql);

# Manually insert Regions for countries not listed
# NB: start from code 10000 and above
// Taiwan, parent is China
$sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region, other_region) VALUES " .
        " (" . intval('10000') . ",'" . mysql_real_escape_string('Taiwan') . "'" . "," . intval(16) . "" . ",NULL" . ", 0)" .
        ";";
$g_oConn->Execute($sql);

################################################################################
# Geographical region and composition of each region
################################################################################
echo "\n\nGeographical region and composition of each region\n";
$table = $html->find('table tr td.cheader2', 2)->parent->parent;

$region_id = 0;
foreach ($table->find('tr') as $tr) {
    if (sizeof($tr->find('td.cheader2', 0)))
        continue;

    $name = $tr->find('td', 1)->plaintext;
    $name = html_entity_decode($name);
    $name = preg_replace("/\s+/si", " ", $name);
    $name = trim($name);
    $name = preg_replace("/[a-b]?\//si", "", $name);
    $name = iconv("iso-8859-1", "utf-8", $name);

    $code = trim(preg_replace("/[^\d]*/si", "", $tr->find('td', 0)->plaintext));
    $name = trim(preg_replace("/\d*/si", "", $name));

    if (!strlen($name))
        continue;

    if ("Developing regions" == $name)
        break;

    // Regions
    if (sizeof($tr->find('td.content i b', 0))
            || sizeof($tr->find('td.lcont i b', 0))
            || sizeof($tr->find('td.tcont i b', 0))
    ) {

        $region_id = intval($g_oConn->GetValue("SELECT id FROM country_region WHERE `code` = " . intval($code) . ";"));
        echo "\n\n[$name]\n";
        continue;
    }

    if (!$region_id)
        continue;

    // Countries
    if (sizeof($tr->find('td.lcont p', 1))
            || sizeof($tr->find('td.tcont p', 1))
            || sizeof($tr->find('td.lcont', 1))
            || sizeof($tr->find('td.tcont', 1))
    ) {
        if (sizeof($tr->find('h3 b', 0)))
            continue;
        $sql = "INSERT INTO country_region (code, name, parent_region_id, developed_developing_region) VALUES (" .
                intval($code) .
                ",'" . mysql_real_escape_string($name) . "'" .
                "," . intval($region_id) . "" .
                ",NULL" .
                ");";
        $g_oConn->Execute($sql);
        echo "$name,";
        continue;
    }
}

################################################################################
# Developed and developing regions
################################################################################
echo "\n\nDeveloped and developing regions...";
$table = $html->find('table tr td.cheader2', 2)->parent->parent;

$developed_developing_region = "";
foreach ($table->find('tr') as $tr) {
    $name = $tr->find('td', 1)->plaintext;
    $name = html_entity_decode($name);
    $name = preg_replace("/\s+/si", " ", $name);
    $name = trim($name);
    $name = preg_replace("/[a-b]?\//si", "", $name);
    $name = iconv("iso-8859-1", "utf-8", $name);

    $code = trim(preg_replace("/[^\d]*/si", "", $tr->find('td', 0)->plaintext));
    $name = trim(preg_replace("/\d*/si", "", $name));

    if (!strlen($name))
        continue;

    if ("Least developed countries" == $name)
        break;

    if ("Developing regions" == $name) {
        $developed_developing_region = "DEVELOPING";
        continue;
    }

    if ("Developed regions" == $name) {
        $developed_developing_region = "DEVELOPED";
        continue;
    }

    if (!$developed_developing_region)
        continue;

    $sql = "UPDATE country_region SET 
                    developed_developing_region = '" . mysql_real_escape_string($developed_developing_region) . "' 
                WHERE
                    `code` = " . intval($code) . ";";
    $g_oConn->Execute($sql);

    echo "\n$name";
}

################################################################################
# Other groupings
################################################################################
echo "\n\nOther groupings...";
$table = $html->find('table tr td.cheader2', 2)->parent->parent;

$db_column = "";
foreach ($table->find('tr') as $tr) {
    $name = $tr->find('td', 1)->plaintext;
    $name = html_entity_decode($name);
    $name = preg_replace("/\s+/si", " ", $name);
    $name = trim($name);
    $name = preg_replace("/[a-b]?\//si", "", $name);
    $name = iconv("iso-8859-1", "utf-8", $name);

    $code = trim(preg_replace("/[^\d]*/si", "", $tr->find('td', 0)->plaintext));
    $name = trim(preg_replace("/\d*/si", "", $name));

    if (!strlen($name))
        continue;

    if ("Least developed countries" == $name) {
        $db_column = "least_developed_country";
        echo "\n\n[Least developed countries]";
        continue;
    }

    if ("Landlocked developing countries" == $name) {
        $db_column = "landlocked_developing_country";
        echo "\n\n[Landlocked developing countries]";
        continue;
    }

    if ("Small island developing States" == $name) {
        $db_column = "small_island_developing_state";
        echo "\n\n[Small island developing States]";
        continue;
    }

    if ("Transition countries d" == $name) {
        $db_column = "transition_country";
        echo "\n\n[Transition countries]";
        continue;
    }

    if (!$db_column)
        continue;

    $sql = "UPDATE country_region SET 
                    " . $db_column . " = 1 
                WHERE
                    `code` = " . intval($code) . ";";
    $g_oConn->Execute($sql);

    echo "\n$name";
}

$html->clear();
unset($html);

curl_close($ch);

################################################################################
# ISO Country names and code elements
################################################################################
// missing counterparts from UN to ISO websites
$UN_TO_ISO = array(
    "Bonaire, Saint Eustatius and Saba" => "BONAIRE, SINT EUSTATIUS AND SABA"
    , "Democratic Republic of the Congo" => "CONGO, THE DEMOCRATIC REPUBLIC OF THE"
    , "Faeroe Islands" => "FAROE ISLANDS"
    , "Holy See" => "HOLY SEE (VATICAN CITY STATE)"
    , "China, Hong Kong Special Administrative Region " => "HONG KONG"
    , "Democratic People's Republic of Korea" => "KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF"
    , "Republic of Korea" => "KOREA, REPUBLIC OF"
    , "China, Macao Special Administrative Region " => "MACAO"
    , "The former Yugoslav Republic of Macedonia" => "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF"
    , "Republic of Moldova" => "MOLDOVA, REPUBLIC OF"
    , "Occupied Palestinian Territory" => "PALESTINIAN TERRITORY, OCCUPIED"
    , "Saint-Barthélemy" => "SAINT BARTHELEMY"
    , "Saint Helena" => "SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA"
    , "Saint Kitts and Nevis" => "SAINT KITTS AND NEVIS"
    , "Georgia" => "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS"
    , "Svalbard and Jan Mayen Islands" => "SVALBARD AND JAN MAYEN"
    , "United Republic of Tanzania" => "TANZANIA, UNITED REPUBLIC OF"
    , "United Kingdom of Great Britain and Northern Ireland" => "UNITED KINGDOM"
    , "United States of America" => "UNITED STATES"
    , "British Virgin Islands" => "VIRGIN ISLANDS, BRITISH"
    , "United States Virgin Islands" => "VIRGIN ISLANDS, U.S."
    , "Wallis and Futuna Islands" => "WALLIS AND FUTUNA"
    , "United Republic of Tanzania" => "TANZANIA, UNITED REPUBLIC OF"
    , "Democratic Republic of the Congo" => "CONGO, THE DEMOCRATIC REPUBLIC OF THE"
    , "Iran (Islamic Republic of)" => "IRAN, ISLAMIC REPUBLIC OF"
    , "Bolivia (Plurinational State of)" => "BOLIVIA, PLURINATIONAL STATE OF"
    , "Venezuela (Bolivarian Republic of)" => "VENEZUELA, BOLIVARIAN REPUBLIC OF"
    , "Falkland Islands (Malvinas)" => "FALKLAND ISLANDS (MALVINAS)"
    , "Micronesia (Federated States of)" => "MICRONESIA, FEDERATED STATES OF"
    , "Saint Martin (French part)" => "SAINT MARTIN (FRENCH PART)"
    , "Saint-Barthélemy" => "SAINT BARTHÉLEMY"
    , "Sint Maarten (Dutch part)" => "SINT MAARTEN (DUTCH PART)"
    , "Taiwan" => "TAIWAN, PROVINCE OF CHINA"
);

$ISO_TO_UN = array_flip($UN_TO_ISO);

echo "\n\nISO Country names and code elements";
$url = "http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm";
//$url = "file://savedpages\\country_names_and_code_elements.htm";
# initialize the curl session
echo "\nCurl session";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");
curl_setopt($ch, CURLOPT_REFERER, "");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: en"));
$html = curl_exec($ch);

// Check if any error occured
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    die();
}
$html = str_get_html($html);


foreach ($html->find("table.sortable tr") as $tr) {
    if (!sizeof($tr->find("td", 0)))
        continue;

    $country_name = $tr->find("td", 1)->plaintext;
    $country_name = preg_replace("/\d*/si", "", $country_name);
    $country_name = preg_replace("/\s+/si", " ", $country_name);
    $country_name = trim($country_name);
    $country_name = isset($ISO_TO_UN[$country_name]) ? $ISO_TO_UN[$country_name] : $country_name;

    $country_code = $tr->find("td", 0)->plaintext;

    echo "\n$country_name";


    $sql = "UPDATE country_region 
            SET iso_country_code = '" . mysql_real_escape_string($country_code) . "' 
            WHERE 
                `name` = '" . mysql_real_escape_string($country_name) . "';";
//    echo $sql;
    $g_oConn->Execute($sql);
}

$html->clear();
unset($html);

curl_close($ch);

echo "\nDone!";
?>