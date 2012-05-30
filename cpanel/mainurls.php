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
                , "SELECT id AS `value`, `name` AS `text` FROM main_category ORDER BY id ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );

        $delim = "";
        if (isset($_GET['flt_id']) && intval($_GET['flt_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['flt_id']);
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
        $this->m_sTitle = "Main URLs";
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
                        <label>ID</label> <br />
                        <input class="listnav" name="flt_id" value="<?= isset($_GET['flt_id']) ? $_GET['flt_id'] : "" ?>" />
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