<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CObject extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("Feed ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Feed URL", 'feed_url', CL_VIEW_GRID);

        $this->AddColumn("Twitter ID", 'twitter_id', CL_VIEW_GRID);
        $this->AddColumn("Twitter Name", 'twitter_name', CL_VIEW_GRID);
        $this->AddColumn("Twitter Screen Name", 'twitter_screen_name', CL_VIEW_GRID);
        $this->AddColumn("Twitter location", 'twitter_location', CL_VIEW_GRID);
        $this->AddColumn("Twitter description", 'twitter_description', CL_VIEW_GRID);
        $this->AddColumn("Twitter profile_image_url", 'twitter_profile_image_url', CL_VIEW_GRID);
//        $this->AddColumn("Twitter profile_image_url_https", 'twitter_profile_image_url_https', CL_VIEW_GRID);
        $this->AddColumn("Twitter url", 'twitter_url', CL_VIEW_GRID);
        $this->AddColumn("Twitter protected", 'twitter_protected', CL_VIEW_GRID);
        $this->AddColumn("Twitter followers_count", 'twitter_followers_count', CL_VIEW_GRID);
        $this->AddColumn("Twitter friends_count", 'twitter_friends_count', CL_VIEW_GRID);
        $this->AddColumn("Twitter created_at", 'twitter_created_at', CL_VIEW_GRID);
        $this->AddColumn("Twitter favourites_count", 'twitter_favourites_count', CL_VIEW_GRID);
        $this->AddColumn("Twitter utc_offset", 'twitter_utc_offset', CL_VIEW_GRID);
        $this->AddColumn("Twitter time_zone", 'twitter_time_zone', CL_VIEW_GRID);
        $this->AddColumn("Twitter notifications", 'twitter_notifications', CL_VIEW_GRID);
        $this->AddColumn("Twitter geo_enabled", 'twitter_geo_enabled', CL_VIEW_GRID);
        $this->AddColumn("Twitter verified", 'twitter_verified', CL_VIEW_GRID);
        $this->AddColumn("Twitter following", 'twitter_following', CL_VIEW_GRID);
        $this->AddColumn("Twitter statuses_count", 'twitter_statuses_count', CL_VIEW_GRID);
        $this->AddColumn("Twitter lang", 'twitter_lang', CL_VIEW_GRID);
        $this->AddColumn("Twitter listed_count", 'twitter_listed_count', CL_VIEW_GRID);
        $this->AddColumn("Twitter saved_at", 'twitter_saved_at', CL_VIEW_GRID);

        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "su.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }

        if (isset($_GET['f_name']) && trim($_GET['f_name'])) {
            $this->m_sFilter.= $delim . "sut.name like '%" . trim($_GET['f_name'])."%'";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "
SELECT 
	su.id
	,COALESCE(mc.name,'') AS source_category
	,GROUP_CONCAT(DISTINCT mc.tag) AS tags
	, mu.domain AS source_domain
	, mu.url AS source_url 
	
	, sc.name AS feed_type
	, su.url AS feed_url
	, su.title AS feed_title
	
	, sut.twitter_id AS twitter_id
	, sut.name AS twitter_name
	, sut.screen_name AS twitter_screen_name
	, sut.location AS twitter_location
	, sut.description AS twitter_description
	, sut.profile_image_url AS twitter_profile_image_url
	, sut.profile_image_url_https AS twitter_profile_image_url_https
	, sut.url AS twitter_url
	, sut.protected AS twitter_protected
	, sut.followers_count AS twitter_followers_count
	, sut.friends_count AS twitter_friends_count
	, sut.created_at AS twitter_created_at
	, sut.favourites_count AS twitter_favourites_count
	, sut.utc_offset AS twitter_utc_offset
	, sut.time_zone AS twitter_time_zone
	, sut.notifications AS twitter_notifications
	, sut.geo_enabled AS twitter_geo_enabled
	, sut.verified AS twitter_verified
	, sut.following AS twitter_following
	, sut.statuses_count AS twitter_statuses_count
	, sut.lang AS twitter_lang
	, sut.listed_count AS twitter_listed_count
	, sut.saved_at AS twitter_saved_at
FROM scrape_url su
INNER JOIN scrape_url_category suc ON suc.scrape_url_id = su.id
INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
LEFT JOIN main_url mu ON mu.id = su.url_id
LEFT JOIN main_category mc ON mc.id = suc.main_category_id
INNER JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id";

        $this->m_sCountSQL = "SELECT COUNT(DISTINCT su.id)
	FROM scrape_url su
	INNER JOIN scrape_url_category suc ON suc.scrape_url_id = su.id
	INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
	LEFT JOIN main_url mu ON mu.id = su.url_id
	LEFT JOIN main_category mc ON mc.id = suc.main_category_id
	INNER JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id";

        $this->m_sTableName = 'scrape_url';
        $this->m_sTitle = "Twitter Feeds";
        $this->m_sActionURL = "twitterfeeds.php";
        $this->m_sGroupBy = "su.id";
        $this->m_sOrderBy = "IF(ISNULL(mc.name),1,0), mc.name, mu.id";
        $this->m_nPageSize = 50;
    $this->m_nOperation = 0;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "twitter_profile_image_url") {
            $value = '<img src="'.$value.'" alt="" title="" />';
            return $value;
        }

        return $value;
    }

}

$list = new CObject();
StartPage('FEEDS', 'TWITTERFEEDS');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('FEEDS', 'TWITTERFEEDS');
    ?>
    <div class="span-20">
        <form method="get" action="twitterfeeds.php">
            <table>
                <tr>
                    <td class="span-2">
                        <label>Feed ID</label> <br />
                        <input type="text" name="f_id" value="<?= isset($_GET['f_id']) ? $_GET['f_id'] : "" ?>" />
                    </td>
                    <td class="span-2">
                        <label>Twitter Name</label> <br />
                        <input type="text" name="f_name" value="<?= isset($_GET['f_name']) ? $_GET['f_name'] : "" ?>" />
                    </td>
                    <td>
                        <input class="button" type="submit" value="Filter" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="clear"></div>

    <?php
    $list->ShowListPage();
}
TerminatePage();
?>