<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Name", 'name', CL_VIEW_GRID | CL_VIEW_EDIT);
        $this->AddColumn("Tag", 'tag', CL_VIEW_GRID | CL_VIEW_EDIT);

        $delim = "";
        if (isset($_GET['flt_name']) && strlen($_GET['flt_name']) > 0) {
            $this->m_sFilter.= $delim . " (name like '%" . ($_GET['flt_name'])."%' OR tag like '%" . ($_GET['flt_name'])."%')";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "SELECT * FROM main_category";

        $this->m_sCountSQL = "SELECT COUNT(*) FROM main_category";

        $this->m_sTableName = 'main_category';
        $this->m_sTitle = "Categories";
        $this->m_sActionURL = "maincategories.php";
        $this->m_sOrderBy = "name";
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
StartPage('FEEDS', 'MAINCATEGORIES');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('FEEDS', 'MAINCATEGORIES');
    ?>
    <div class="span-20">
        <form method="get" action="maincategories.php">
            <table>
                <tr>
                    <td>
                        <label>Name or Tag</label> <br />
                        <input type="text" class="span-7" name="flt_name" value="<?= isset($_GET['flt_name']) ? $_GET['flt_name'] : "" ?>" />
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