<?php

/*
 * Determine the collected URLs categories.
 * No curl sessions here.
 * Make use of the title, content-type, etc. information.
 */

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nStarting...\n";

# initialize the curl session

$sql = "SELECT * 
        FROM scrape_url 
        WHERE
          curl_parsed = 1
        ORDER BY id
        -- LIMIT 5
        ;";
$rs = new CRecordset($sql);
$total = 0;
while ($rs->MoveNext())
{
  // Feed content-type: text/xml; application/rss+xml; application/rdf+xml
  if (stripos($rs->GetItem("content_type"), "xml") !== false || stripos($rs->GetItem("content_type"), "rss") !== false || stripos($rs->GetItem("content_type"), "atom") !== false)
  {
    // podcast
    if (stripos($rs->GetItem("url"), "podcast") !== false)
      $g_oConn->Execute("INSERT IGNORE INTO scrape_url_category VALUES (NULL, " . intval($rs->GetItem("id")) . ", 2);");
    // vodcast
    elseif (stripos($rs->GetItem("url"), "video") !== false
            || stripos($rs->GetItem("url"), "vid") !== false
            || stripos($rs->GetItem("url"), "vod") !== false
    )
      $g_oConn->Execute("INSERT IGNORE INTO scrape_url_category VALUES (NULL, " . intval($rs->GetItem("id")) . ", 3);");
    // twitter
    elseif (stripos($rs->GetItem("url"), "twitter") !== false)
      $g_oConn->Execute("INSERT IGNORE INTO scrape_url_category VALUES (NULL, " . intval($rs->GetItem("id")) . ", 4);");
    // default to normal RSS text feed
    else
      $g_oConn->Execute("INSERT IGNORE INTO scrape_url_category VALUES (NULL, " . intval($rs->GetItem("id")) . ", 1);");
  }

  // process Twitter here and any other non-RSS URLs.
  if (stripos($rs->GetItem("url"), "twitter") !== false)
    $g_oConn->Execute("INSERT IGNORE INTO scrape_url_category VALUES (NULL, " . intval($rs->GetItem("id")) . ", 4);");

  $total++;
  echo "\rTotal: " . $total . "\r";
}

echo "\nDone!";
?>