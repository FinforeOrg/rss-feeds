<?php

/*
 * Open each URL and collect content-type and title.
 */

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/include.php';

# Load args
##################################################################################
$ARGS = array();
if (isset($_SERVER['argv']) && isset($_SERVER['argc']))
{
  for ($i = 1; $i < $_SERVER['argc']; $i++)
    if (isset($_SERVER['argv'][$i]) && isset($_SERVER['argv'][$i + 1]))
      $ARGS[$_SERVER['argv'][$i]] = $_SERVER['argv'][$i + 1];
}
else
{
//  _die("\nFatal: Enter some arguments!\n");
}

$perpage = isset($ARGS["--pp"]) ? trim($ARGS["--pp"]) : 1000;
$page = isset($ARGS["--p"]) ? trim($ARGS["--p"]) : -1;

echo "\nStarting...\n";
echo "\nPage " . $page . "\n";

# initialize the curl session
$ch = curl_init();

$sql = "SELECT * 
        FROM scrape_url 
        WHERE 
          curl_parsed = 0
          AND curl_has_error = 0
        ORDER BY id
        " . ($page != -1 ? "LIMIT " . ($page * $perpage) . ", " . $perpage . "" : "" ) . "
        ;";

$rs = new CRecordset($sql);
$total = 0;
while ($rs->MoveNext())
{
  curl_setopt($ch, CURLOPT_URL, $rs->GetItem("url"));
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");
  curl_setopt($ch, CURLOPT_REFERER, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: en"));
  $html = curl_exec($ch);

  // get content_type
  $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

  if (in_array($content_type, array("application/pdf", "audio/mpeg")))
  {
    // update URL
    $sql = "UPDATE scrape_url SET curl_parsed = 1 ,content_type = '" . mysql_real_escape_string($content_type) . "' WHERE id = " . $rs->GetItem("id") . ";";
    $g_oConn->Execute($sql);
    continue;
  }

  // Check if any error occured
  if (curl_errno($ch))
  {
    echo "[" . $rs->GetItem("url") . "]\n";
    echo "Curl error: " . curl_error($ch) . "\n";
    // update URL
    $sql = "UPDATE scrape_url SET curl_error = '" . mysql_real_escape_string(curl_error($ch)) . "', curl_has_error = 1, content_type = '" . mysql_real_escape_string($content_type) . "' WHERE id = " . $rs->GetItem("id") . ";";
    $g_oConn->Execute($sql);
    continue;
  }

  if (!strlen($html))
    $html.= " ";

  $html = str_get_html($html);

  // get title
  $title = sizeof($html->find("title", 0)) ? $html->find("title", 0)->plaintext : "";

  // determine FeedBurner from title
  // if FeedBurner then append "?format=xml" at end of the URL and insert new record in scrape_url
  if (stripos($title, "FeedBurner") !== false)
  {
    // remove powered by FeedBurner note from title
    $title = str_replace(" - powered by FeedBurner", "", $title);
    // Actual RSS feed URL
    $new_url = $rs->GetItem("url") . "?format=xml";

    // insert in DB
    $unique_id = gen_unique_id($new_url);

    $sql = "INSERT IGNORE INTO scrape_url (url_id, url, title, unique_id, curl_parsed, content_type, created_at) VALUES (" .
            $rs->GetItem("url_id") .
            ",'" . mysql_real_escape_string($new_url) . "'" .
            ",'" . mysql_real_escape_string($title) . "'" .
            ",'" . mysql_real_escape_string($unique_id) . "'" .
            ",1" .
            ",'" . mysql_real_escape_string($content_type) . "'" .
            ",NOW()" .
            ");";
    $g_oConn->Execute($sql);
  }

  // update URL
  $sql = "UPDATE
            scrape_url 
          SET
            curl_parsed = 1
            ,title = '" . mysql_real_escape_string($title) . "'
            ,content_type = '" . mysql_real_escape_string($content_type) . "'
          WHERE id = " . $rs->GetItem("id") . ";";

  $g_oConn->Execute($sql);

  $total++;
  echo "\rTotal: " . $total . "\r";

  $html->clear();
  unset($html);
}


echo "\nDone!";
curl_close($ch);
?>