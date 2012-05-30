<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CObject extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE, $g_oConn;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
//        $this->AddColumn("Source Category", 'source_category', CL_VIEW_GRID);
        $this->AddColumn("Source Categories", 'source_categories', CL_VIEW_GRID);
        $this->AddColumn("Tags", 'tags', CL_VIEW_GRID);

        $this->AddColumn("Source Domain", 'source_domain', CL_VIEW_GRID);
//        $this->AddColumn("Source URL", 'source_url', CL_VIEW_GRID);
        $this->AddComboColumn(
                "Source URL"
                , "url_id"
                , "source_url"
                , "SELECT
                    mu.id AS `value`
                    , CONCAT(mc.name, ' - ', url) AS `text` 
                FROM main_url mu 
                INNER JOIN main_category mc ON mc.id = category_id
                ORDER BY mc.name ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );
        $this->AddColumn("Feed Type", 'feed_type', CL_VIEW_GRID);

        $this->AddColumn("Feed URL", 'url', CL_VIEW_GRID | CL_VIEW_EDIT);
        $this->AddColumn("Feed Title", 'title', CL_VIEW_GRID | CL_VIEW_EDIT);


        $this->AddColumn("Options", 'options', CL_VIEW_GRID);

        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }

        if (isset($_GET["f_feed_type"]) && is_array($_GET["f_feed_type"])) {
            $this->m_sFilter.= $delim . "sc.id IN (" . implode(",", $_GET['f_feed_type']) . ")";
            $delim = " and ";
        }

        // search query
        $query = Get("f_q");
        $region_ids_string = "";
        $join_region_tables = "";
        if (strlen($query)) {
            $query = str_replace("OR", "__#__", $query);
            $words = explode("__#__", $query);
            // trim array values
            array_walk($words, create_function('&$val', '$val = trim($val);'));
            $sql = "SELECT GROUP_CONCAT(DISTINCT id) FROM country_region WHERE `name` IN ('" . implode("','", $words) . "');";
            $region_ids_string = $g_oConn->GetValue($sql);

            $join_region_tables = <<<EOT
INNER JOIN (
    SELECT
    cr4.name
    # World
    FROM country_region cr1
    # Continent
    INNER JOIN country_region cr2 ON cr1.id = cr2.parent_region_id AND cr2.other_region = 0
    # Region
    INNER JOIN country_region cr3 ON cr2.id = cr3.parent_region_id AND cr3.other_region = 0
    # Country
    INNER JOIN country_region cr4 ON cr3.id = cr4.parent_region_id AND cr4.other_region = 0
    WHERE
        cr4.id IN ({$region_ids_string})
        OR cr4.parent_region_id IN ({$region_ids_string})
        OR cr3.parent_region_id IN ({$region_ids_string})
        OR cr2.parent_region_id IN ({$region_ids_string})
        OR cr1.parent_region_id IN ({$region_ids_string})
) AS rn on rn.name = mc.name
EOT;
        }

        $this->m_sSelectSQL = "
SELECT 
	su.id
	,su.url
	,su.title
    
    ,sut.twitter_id AS twitter_id
    
	,COALESCE(mc.name,'') AS source_category
	,GROUP_CONCAT(DISTINCT COALESCE(mc.name,'')) AS source_categories
	,GROUP_CONCAT(DISTINCT mc.tag) AS tags
    
	, mu.id AS source_url_id 
	, mu.domain AS source_domain
	, mu.url AS source_url 
	
	, sc.name AS feed_type
    , GROUP_CONCAT(DISTINCT sc.name) AS feed_types
FROM scrape_url su
INNER JOIN scrape_url_category suc ON suc.scrape_url_id = su.id
INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
LEFT JOIN main_url mu ON mu.id = su.url_id
LEFT JOIN main_category mc ON mc.id = suc.main_category_id
LEFT JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id
" . $join_region_tables . "
";

        $this->m_sCountSQL = "SELECT COUNT(DISTINCT su.id)
	FROM scrape_url su
	INNER JOIN scrape_url_category suc ON suc.scrape_url_id = su.id
	INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
	LEFT JOIN main_url mu ON mu.id = su.url_id
	LEFT JOIN main_category mc ON mc.id = suc.main_category_id
	LEFT JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id
    " . $join_region_tables . "
    ";

        $this->m_sTableName = 'scrape_url';
        $this->m_sTitle = "RSS Feeds";
        $this->m_sActionURL = "rssfeeds.php";
        $this->m_sGroupBy = "su.id";
        $this->m_sOrderBy = "IF(ISNULL(mc.name),1,0), mc.name, mu.id";
        $this->m_nPageSize = 50;
//    $this->m_nOperation = OP_EDIT | OP_ADD;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "options") {
            $value.= " <a href='urlcategories.php?f_id=" . $rs->GetItem("id") . "'><img src='images/tag--pencil.png' border=0 alt='Categories and tags' title='Categories and tags' /></a>";
            if ($rs->GetItem("twitter_id"))
                $value.= " <a href='twitterfeeds.php?f_id=" . $rs->GetItem("id") . "'><img src='images/twitter.png' border=0 alt='Twitter details' title='Twitter details' /></a>";
        }

        if ($dbname == "source_categories") {
            
        }

        return $value;
    }

}

$list = new CObject();
StartPage('FEEDS', 'RSSFEEDS');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('FEEDS', 'RSSFEEDS');
    ?>
    <div class="span-20">
        <form method="get" action="rssfeeds.php">
            <table>
                <tr>
                    <td class="span-2">
                        <label>Feed Type</label> <br />
                        <?php
                        FillCheckbox("SELECT id, `name` FROM scrape_category ORDER BY id;"
                                , isset($_GET["f_feed_type"]) && is_array($_GET["f_feed_type"]) ? $_GET["f_feed_type"] : array()
                                , 1
                                , "f_feed_type"
                        );
                        ?>
                    </td>
                    <td class="span-2">
                        <label>Query (world, continent, region, country)</label> <br />
                        <input type="text" name="f_q" id="f_q" value="<?php echo Get("f_q") ?>" class="span-7" /> <br />
                        Note: use "OR" to combine search terms
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