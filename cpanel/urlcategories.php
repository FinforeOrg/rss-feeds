<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

//        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Feed ID", 'scrape_url_id', CL_VIEW_GRID | CL_VIEW_EDIT);
        $this->AddColumn("Feed URL", 'scrape_url_url', CL_VIEW_GRID);
        $this->AddColumn("Title", 'scrape_url_title', CL_VIEW_GRID);

        $this->AddComboColumn(
                "Main Category Name"
                , "main_category_id"
                , "main_category_name"
                , "SELECT id AS `value`, `name` AS `text` FROM main_category ORDER BY name ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );

        $this->AddComboColumn(
                "URL Type"
                , "scrape_category_id"
                , "scrape_category_name"
                , "SELECT id AS `value`, `name` AS `text` FROM scrape_category ORDER BY name ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );


        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "su.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }

        if (isset($_GET['f_cat']) && intval($_GET['f_cat']) > 0) {
            $this->m_sFilter.= $delim . "mc.id=" . intval($_GET['f_cat']);
            $delim = " and ";
        }

        if (isset($_GET["f_feed_type"]) && is_array($_GET["f_feed_type"])) {
            $this->m_sFilter.= $delim . "sc.id IN (" . implode(",", $_GET['f_feed_type']) . ")";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "SELECT 
    suc.id
	,su.id AS scrape_url_id
	,su.url AS scrape_url_url
	,su.title AS scrape_url_title
    
	,mc.id AS main_category_id
	,mc.name AS main_category_name
	,sc.id AS scrape_category_id
	,sc.name AS scrape_category_name
FROM scrape_url_category suc
INNER JOIN scrape_url su ON suc.scrape_url_id = su.id
INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
INNER JOIN main_category mc ON mc.id = suc.main_category_id";

        $this->m_sCountSQL = "SELECT COUNT(*) 
FROM scrape_url_category suc
INNER JOIN scrape_url su ON suc.scrape_url_id = su.id
INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
INNER JOIN main_category mc ON mc.id = suc.main_category_id";

        $this->m_sTableName = 'scrape_url_category';
        $this->m_sTitle = "Feed Categories and Tags";
        $this->m_sActionURL = "urlcategories.php";
        $this->m_sOrderBy = "id";
        $this->m_nPageSize = 50;
//    $this->m_nOperation = OP_EDIT | OP_ADD;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "") {
            $value = "";
        }

        return $value;
    }

}

$list = new CUsers();
StartPage('FEEDS', 'URLCATEGORIES');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('FEEDS', 'URLCATEGORIES');
    ?>
    <div class="span-20">
        <form method="get" action="urlcategories.php">
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
                    <td class="span-7">
                        <label>Feed ID</label> <br />
                        <input type="text" name="f_id" value="<?= isset($_GET['f_id']) ? $_GET['f_id'] : "" ?>" />
                        <br />

                        <label>Category</label> <br />
                        <select name="f_cat">
                            <option value="-1">Any</option>
                            <?php FillCombo("SELECT id AS `value`, `name` AS `text` FROM main_category ORDER BY `name`;", intval(Get("f_cat"))); ?>
                        </select>
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