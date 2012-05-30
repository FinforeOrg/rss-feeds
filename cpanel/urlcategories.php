<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Feed URL ID", 'scrape_url_id', CL_VIEW_EDIT);
        $this->AddColumn("Feed URL", 'scrape_url_url', CL_VIEW_GRID);
        $this->AddColumn("Title", 'scrape_url_title', CL_VIEW_GRID);
        
        $this->AddComboColumn(
                "Main Category Name"
                , "main_category_id"
                , "main_category_name"
                , "SELECT id AS `value`, `name` AS `text` FROM main_category ORDER BY id ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );

        $this->AddComboColumn(
                "URL Type"
                , "scrape_category_id"
                , "scrape_category_name"
                , "SELECT id AS `value`, `name` AS `text` FROM scrape_category ORDER BY id ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );


        $delim = "";
            if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "su.id=" . intval($_GET['f_id']);
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
        $this->m_sTitle = "URL Categories and Tags";
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
                    <td>
                        <label>ID</label> <br />
                        <input type="text" name="f_id" value="<?= isset($_GET['f_id']) ? $_GET['f_id'] : "" ?>" />
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