<?
require_once "library/include.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET;
        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Username", 'username', CL_VIEW_GRID);
        $this->AddColumn("Password", 'password', CL_VIEW_GRID);
        $this->AddColumn("Status Code: Note", 'status', CL_VIEW_GRID);
        $this->AddColumn("Date", 'date', CL_VIEW_GRID);
        $this->AddColumn("IP", 'ip', CL_VIEW_GRID);

        $delim = "";
        if (isset($_GET['flt_username']) && strlen($_GET['flt_username']) > 0) {
            $this->m_sFilter.= $delim . "a.username like '%" . $_GET['flt_username'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_fname']) && strlen($_GET['flt_fname']) > 0) {
            $this->m_sFilter.= $delim . "a.firstname like '%" . $_GET['flt_fname'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_lname']) && strlen($_GET['flt_lname']) > 0) {
            $this->m_sFilter.= $delim . "a.lastname like '%" . $_GET['flt_lname'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_status']) && $_GET['flt_status'] != -1) {
            $this->m_sFilter.= $delim . "a.status = " . intval($_GET['flt_status']) . "";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "select a.* from admin_log a";

        $this->m_sTableName = 'admin_log';
        $this->m_sOrderBy = 'a.id desc';
        $this->m_sTitle = "Log";
        $this->m_sActionURL = "adminslog.php";
        $this->m_nOperation = 0;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "status") {
            if ("0" == $value)
                return $value . ": Incorrect login/password";
            elseif ("1" == $value)
                return $value . ": Logged in";
            elseif ("2" == $value)
                return $value . ": Login is banned";
            else
                return $value;
        }

        return $value;
    }

}

;

$list = new CUsers();
StartPage('ADMINMANAGEMENT', 'ADMINSLOG');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('ADMINMANAGEMENT', 'ADMINSLOG');
    ?>
    <div class="span-20">
        <form method="get" action="adminslog.php">
            <table cellpadding="1" cellspacing="0" width="80%" align="center">
                <tr>
                    <td class="listmain">
                        <table cellpadding="2" cellspacing="0" width="100%" border="0">
                            <tr>
                                <td class="ROW1">
                                    <label>Username</label>
                                    <input type="text" name="flt_username" value="<?= isset($_GET['flt_username']) ? $_GET['flt_username'] : "" ?>"></td>
                                <td class="ROW1">
                                    <label>First name</label>
                                    <input type="text" name="flt_fname" value="<?= isset($_GET['flt_fname']) ? $_GET['flt_fname'] : "" ?>"></td>
                                <td class="ROW1">
                                    <label>Last name</label>
                                    <input type="text" name="flt_lname" value="<?= isset($_GET['flt_lname']) ? $_GET['flt_lname'] : "" ?>"></td>
                                <td class="ROW1">
                                    <label>Status</label>
                                    <select name="flt_status" class="listnav">
                                        <option value="-1" <?= isset($_GET['flt_status']) && intval($_GET['flt_status']) == -1 ? "selected" : "" ?> >- Select status -</option>
                                        <option value="0"  <?= isset($_GET['flt_status']) && intval($_GET['flt_status']) == 0 ? "selected" : "" ?> >Incorrect login/password</option>
                                        <option value="1"  <?= isset($_GET['flt_status']) && intval($_GET['flt_status']) == 1 ? "selected" : "" ?> >Logged in</option>
                                        <option value="2"  <?= isset($_GET['flt_status']) && intval($_GET['flt_status']) == 2 ? "selected" : "" ?> >Login is banned</option>
                                    </select>
                                </td>
                                <td class="ROW1">
                                    <input class="button" class="listnav" type="submit" value="Filter" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="clear"></div>
    <?
    $list->ShowListPage();
}
TerminatePage();
?>