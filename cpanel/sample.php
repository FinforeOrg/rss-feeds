<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);

        $this->AddColumn("Referral URL", 'referrer', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING);

        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }

        $this->m_sSelectSQL = "";

        $this->m_sCountSQL = "";

        $this->m_sTableName = '';
        $this->m_sTitle = "Page";
        $this->m_sActionURL = "page.php";
        $this->m_sGroupBy = "id";
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
StartPage('MAINMENU', 'MENU');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('MAINMENU', 'MENU');
    ?>
    <div class="span-20">
        <form method="get" action="homeowners.php">
            <table>
                <tr>
                    <td>
                        <label>ID</label> <br />
                        <input class="listnav" name="f_id" value="<?= isset($_GET['f_id']) ? $_GET['f_id'] : "" ?>" />
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