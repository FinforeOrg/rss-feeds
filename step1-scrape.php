<?php

/*
 * Collect all URLs from the given links.
 */

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/include.php';

# initialize the curl session
$ch = curl_init();

echo "\nStarting...\n";

$sql = "SELECT
          u.* 
          ,c.name as category_name
        FROM main_url u 
        INNER JOIN main_category c ON c.id = u.category_id
        WHERE
          curl_parsed = 0
        ORDER BY u.id;";
$rs = new CRecordset($sql);
while ($rs->MoveNext()) {
    curl_setopt($ch, CURLOPT_URL, $rs->GetItem("url"));
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

    // get all categories
    echo "\n[" . $rs->GetItem("category_name") . "]\n";
    echo "ID: " . $rs->GetItem("id") . ", [" . $rs->GetItem("url") . "]\n";
    $total = 0;
    foreach ($html->find('a') as $a) {
        if (!strlen($a->href))
            continue;

        $url = trim($a->href);
        if (strpos($url, "http") === false) {
            if ($url[0] == "/")
                $url = "http://" . $rs->GetItem("domain") . "" . $url;
            else
                $url = "http://" . $rs->GetItem("domain") . "/" . $url;
        }

        $unique_id = gen_unique_id($url);

        $sql = "INSERT IGNORE INTO scrape_url (url_id, url, title, unique_id, created_at) VALUES (" .
                $rs->GetItem("id") .
                ",'" . mysql_real_escape_string($url) . "'" .
                ",'" . mysql_real_escape_string($a->title) . "'" .
                ",'" . mysql_real_escape_string($unique_id) . "'" .
                ",NOW()" .
                ");";
        $g_oConn->Execute($sql);

        $total++;
        echo "\rTotal: " . $total . "\r";
    }

    // update URL
    $sql = "UPDATE
            main_url 
          SET
            curl_parsed = 1
          WHERE id = " . $rs->GetItem("id") . ";";

    $g_oConn->Execute($sql);

    $html->clear();
    unset($html);
}

echo "\nDone!";
curl_close($ch);
?>