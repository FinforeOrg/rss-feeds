<?php

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/twitteroauth/twitteroauth.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nStarting...\n";
$connection = new TwitterOAuth(
                CONSUMER_KEY
                , CONSUMER_SECRET
                , "551084002-hdE8gtFkI6Fp621qklGNPFNPa72T8IRfQaA9V46M"
                , "IHZGsEbn7E3b3YYYPAkXWNXHbf6TDytFqqSuvWbeb0"
);

$rs = new CRecordset("SELECT * from twitter_users_search WHERE done=0 ORDER BY id;");
while ($rs->MoveNext())
{
  echo "\n[" . $rs->GetItem("q") . "]\n";
  $i = 0;
  $page = 1;
  while (true)
  {
    $content = $connection->get("users/search", array(
        "q" => $rs->GetItem("q"),
        "page" => $page,
            )
    );
    if (!sizeof($content))
      break;

//    print_r($content); die();

    foreach ($content as $obj)
    {
      if (!is_object($obj))
      {
        print_r($obj);
        die($connection->http_header);
      }

      $tweeter_username = $obj->screen_name;
      $tweeter_title = $obj->description;
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

      if ($i > 100 && in_array($rs->GetItem("q"), array("commodities", "business", "mergers", "ipo")))
        break 2;
    }

    $page++;
  }

  $g_oConn->Execute("UPDATE twitter_users_search SET done = 1 WHERE id = " . $rs->GetItem("id") . ";");
}

echo "\nDone!";
?>