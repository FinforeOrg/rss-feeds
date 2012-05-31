<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("URL", 'url', CL_VIEW_GRID | CL_VIEW_EDIT);
        $this->AddColumn("Domain", 'domain', CL_VIEW_GRID | CL_VIEW_EDIT);
        $this->AddComboColumn(
                "Category"
                , "category_id"
                , "category_name"
                , "SELECT id AS `value`, `name` AS `text` FROM main_category ORDER BY name ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );

        $delim = "";
        if (isset($_GET['f_url']) && strlen($_GET['f_url'])) {
            $this->m_sFilter.= $delim . "mu.url LIKE '%" . $_GET['f_url']."%'";
            $delim = " and ";
        }

        if (isset($_GET['f_cat']) && intval($_GET['f_cat']) > 0) {
            $this->m_sFilter.= $delim . "mc.id = " . intval($_GET['f_cat'])."";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "
SELECT 
	mu.*
    ,mc.id AS category_id
	,mc.name AS category_name
FROM main_url mu
LEFT JOIN main_category mc ON mc.id = mu.category_id";

        $this->m_sCountSQL = "SELECT COUNT(*) FROM main_url";

        $this->m_sTableName = 'main_url';
        $this->m_sTitle = "Source URLs";
        $this->m_sActionURL = "mainurls.php";
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
    
    function OnUpdate()
    {
        global $g_oConn;
        
        parent::OnUpdate();
        
        $id = intval(Post("id"));
        
        $sql = "UPDATE scrape_url_category suc
                INNER JOIN scrape_url su ON su.id = suc.scrape_url_id
                INNER JOIN main_url mu ON mu.id = su.url_id
                SET suc.main_category_id = mu.category_id
                WHERE
                    mu.id = ".intval($id);
        
        $g_oConn->Execute($sql);
    }

}

$list = new CUsers();
StartPage('FEEDS', 'MAINURLS');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('FEEDS', 'MAINURLS');
    ?>
    <div class="span-20">
        <form method="get" action="mainurls.php">
            <table>
                <tr>
                    <td>
                        <label>URL (all or part of it)</label> <br />
                        <input type="text" class="span-7" name="f_url" value="<?= isset($_GET['f_url']) ? $_GET['f_url'] : "" ?>" />
                    </td>
                    <td>
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