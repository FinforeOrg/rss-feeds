<?php

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/twitteroauth/twitteroauth.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nGetting Twitter Rate Limit Status...\n";
$connection = new TwitterOAuth(
                CONSUMER_KEY
                , CONSUMER_SECRET
                , "551084002-hdE8gtFkI6Fp621qklGNPFNPa72T8IRfQaA9V46M"
                , "IHZGsEbn7E3b3YYYPAkXWNXHbf6TDytFqqSuvWbeb0"
);

$content = $connection->get("account/rate_limit_status", array());
if (!sizeof($content))
  break;

//print_r($content);
//die();


$sql = "REPLACE INTO twitter_rate_limit_status (id,remaining_hits,hourly_limit,reset_time_in_seconds,reset_time) VALUES (" .
        "1" .
        ", " . intval($content->remaining_hits) .
        ", " . intval($content->hourly_limit) .
        ", " . intval($content->reset_time_in_seconds) .
        ", '" . mysql_real_escape_string($content->reset_time) . "'" .
        ");";

$g_oConn->Execute($sql);
?>