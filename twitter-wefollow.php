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

$i = 0;
$rs = new CRecordset("SELECT * from wefollow ORDER BY id;");
while ($rs->MoveNext())
{
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
  if (curl_errno($ch))
  {
    echo 'Curl error: ' . curl_error($ch);
    die();
  }
  $html = str_get_html($html);

  foreach ($html->find("div.result_row") as $row)
  {
    $tweeter_username = $row->find("div.result_details p.result_header strong a", 0)->plaintext;
    $tweeter_title = $row->find("div.result_details p", 1)->plaintext;

    $tweeter_url = "http://twitter.com/" . $tweeter_username;

    $unique_id = gen_unique_id($tweeter_url);

    $id = intval($g_oConn->GetValue("SELECT id FROM scrape_url WHERE unique_id = '" . mysql_real_escape_string($unique_id) . "';"));
    if (!$id)
    {
      $sql = "INSERT IGNORE INTO scrape_url (url_id, url, title, unique_id, curl_parsed, content_type, created_at) VALUES (" .
              0 .
              ",'" . mysql_real_escape_string($tweeter_url) . "'" .
              ",'" . mysql_real_escape_string($tweeter_title) . "'" .
              ",'" . mysql_real_escape_string($unique_id) . "'" .
              ",1" .
              ",''" .
              ",NOW()" .
              ");";
      $g_oConn->Execute($sql);
      $id = $g_oConn->GetLastId();
    }

    $sql = "INSERT INTO scrape_url_category (scrape_url_id,scrape_category_id,main_category_id) VALUES (" .
            $id .
            ", " . intval($rs->GetItem("scrape_category_id")) .
            ", " . intval($rs->GetItem("main_category_id")) .
            ") " .
            "ON DUPLICATE KEY UPDATE main_category_id=" . $rs->GetItem("main_category_id") .
            ";";
    $g_oConn->Execute($sql);


    echo "\rTotal: " . $i . " \r";
    $i++;
  }

  $html->clear();
  unset($html);
}

echo "\nDone!";
curl_close($ch);
?>