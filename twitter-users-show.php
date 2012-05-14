<?php

/*
 * Visit Twitter profiles and collect:
 * - description (title)
 * - followers count
 * - friends count
 * - location
 */

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/twitteroauth/twitteroauth.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nStarting...";

if (!$g_oConn->GetValue("SELECT GET_LOCK('rss_feeds.scrape_url_twitter',3);"))
{
  echo "\nTable is locked. Other process is using it.";
  echo "\nExitting.";
  exit;
}

# check rate limit first
$remaining_hits = 0;
$reset_time_in_seconds = 0;
$rs = new CRecordset("SELECT * FROM twitter_rate_limit_status WHERE id = 1;");
if ($rs->MoveNext())
{
  $remaining_hits = $rs->GetItem("remaining_hits");
  $reset_time_in_seconds = $rs->GetItem("reset_time_in_seconds");
}

// rate limit has not expired yet
if ($reset_time_in_seconds > time() && $remaining_hits <= 0)
{
  echo "\nRate Limit will expire at: ". date("Y-m-d H:i:s", $reset_time_in_seconds);
  echo "\nExiting now.";
  $g_oConn->GetValue("SELECT RELEASE_LOCK('rss_feeds.scrape_url_twitter');");
  exit;
}

//echo "\ntime: ".time();
//echo "\nreset_time: ".$reset_time_in_seconds;
//echo "\nrem_hits".$remaining_hits;
//die();

$sql = "SELECT 
su.* 
FROM scrape_url su 
LEFT JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id
WHERE 
	su.url LIKE '%twitter.com%' 
	AND sut.id IS NULL
  AND su.not_valid_twitter_url = 0
ORDER BY id;";

$rs = new CRecordset($sql);
$i = 0;
while ($rs->MoveNext() && $remaining_hits > 0)
{
  $twitter_username = $rs->GetItem("url");
  $twitter_username = str_replace("http://twitter.com/#!/", "", $twitter_username);
  $twitter_username = str_replace("https://twitter.com/#!/", "", $twitter_username);
  //
  $twitter_username = str_replace("http://www.twitter.com/#!/", "", $twitter_username);
  $twitter_username = str_replace("https://www.twitter.com/#!/", "", $twitter_username);
  //
  $twitter_username = str_replace("http://twitter.com/", "", $twitter_username);
  $twitter_username = str_replace("https://twitter.com/", "", $twitter_username);
  //
  $twitter_username = str_replace("http://www.twitter.com/", "", $twitter_username);
  $twitter_username = str_replace("https://www.twitter.com/", "", $twitter_username);
  //
  $twitter_username = str_replace("http://twitter.com/intent/user?screen_name=", "", $twitter_username);
  $twitter_username = str_replace("https://twitter.com/intent/user?screen_name=", "", $twitter_username);
  //
  $twitter_username = str_replace("/", "", $twitter_username);
  
  if (!strlen($twitter_username))
    continue;
  
  $connection = new TwitterOAuth(
                  CONSUMER_KEY
                  , CONSUMER_SECRET
                  , "551084002-hdE8gtFkI6Fp621qklGNPFNPa72T8IRfQaA9V46M"
                  , "IHZGsEbn7E3b3YYYPAkXWNXHbf6TDytFqqSuvWbeb0"
  );

  $content = $connection->get("users/show", array(
      "screen_name" => $twitter_username,
      "include_entities" => "true"
  ));
  $remaining_hits--;
  
  if (!sizeof($content))
    break;

//  print_r($content);
//  die();

  if (!isset($content->id))
  {
    if (isset($content->error))
    {
      $sql = "UPDATE scrape_url SET 
                not_valid_twitter_url = 1
                ,not_valid_twitter_url_error = '".mysql_real_escape_string(print_r($content, true))."' 
              WHERE
                id = ".$rs->GetItem("id").";";
      $g_oConn->Execute($sql);
    }
    
    print_r($content);
    continue;
  }
  
  $sql = "REPLACE INTO scrape_url_twitter (
            scrape_url_id
            ,twitter_id
            ,name
            ,screen_name
            ,location
            ,description
            ,profile_image_url
            ,profile_image_url_https
            ,url
            ,protected
            ,followers_count
            ,friends_count
            ,created_at
            ,favourites_count
            ,utc_offset
            ,time_zone
            ,notifications
            ,geo_enabled
            ,verified
            ,following
            ,statuses_count
            ,lang
            ,listed_count
            ,saved_at
          ) VALUES (" .
          $rs->GetItem("id") .
          "," . intval($content->id) . "
          ,'" . mysql_real_escape_string($content->name) . "'
          ,'" . mysql_real_escape_string($content->screen_name) . "'
          ,'" . mysql_real_escape_string($content->location) . "'
          ,'" . mysql_real_escape_string($content->description) . "'
          ,'" . mysql_real_escape_string($content->profile_image_url) . "'
          ,'" . mysql_real_escape_string($content->profile_image_url_https) . "'
          ,'" . mysql_real_escape_string($content->url) . "'
          ,'" . mysql_real_escape_string($content->protected) . "'
          ,'" . mysql_real_escape_string($content->followers_count) . "'
          ,'" . mysql_real_escape_string($content->friends_count) . "'
          ," . strtotime($content->created_at) . "
          ,'" . mysql_real_escape_string($content->favourites_count) . "'
          ,'" . mysql_real_escape_string($content->utc_offset) . "'
          ,'" . mysql_real_escape_string($content->time_zone) . "'
          ,'" . mysql_real_escape_string($content->notifications) . "'
          ,'" . mysql_real_escape_string($content->geo_enabled) . "'
          ,'" . mysql_real_escape_string($content->verified) . "'
          ,'" . mysql_real_escape_string($content->following) . "'
          ,'" . mysql_real_escape_string($content->statuses_count) . "'
          ,'" . mysql_real_escape_string($content->lang) . "'
          ,'" . mysql_real_escape_string($content->listed_count) . "'
          ,NOW()
          );";
  $g_oConn->Execute($sql);
  
  $i++;
  echo "\rDone: ".$i.", [".$twitter_username."]                             \r";
}

// release lock
$g_oConn->GetValue("SELECT RELEASE_LOCK('rss_feeds.scrape_url_twitter');");

# update rate limit status
require dirname(__FILE__) . '/twitter-rate-limit-status.php';

echo "\nDone!";
exit;
?>