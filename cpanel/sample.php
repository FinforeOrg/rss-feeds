<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE;

        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);

        $this->AddColumn("Username", 'username', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 1);
        $this->AddColumn("Password", 'password', CL_VIEW_EDIT, CT_PASSWORD);

        $this->AddColumn("Email", 'email_address', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 1);

        $this->AddColumn("Name", 'name', CL_VIEW_GRID);
        $this->AddColumn("Jobs", 'jobs_count', CL_VIEW_GRID);


        $this->AddColumn("First Name", 'first_name', CL_VIEW_EDIT, CT_STRING, 1, 31);
        $this->AddColumn("Last Name", 'last_name', CL_VIEW_EDIT, CT_STRING, 1, 31);

        $this->AddColumn("Login & Register Info", 'login_register', CL_VIEW_GRID, CT_STRING);

        $this->AddColumn("Active?", 'is_active', CL_VIEW_GRID, CT_CHECKBOX);

        $this->AddColumn("Address", 'address', CL_VIEW_EDIT, CT_STRING, 0, 255);
        $this->AddColumn("City", 'city', CL_VIEW_EDIT, CT_STRING, 0, 255);
        $this->AddColumn("Zip", 'zip', CL_VIEW_EDIT, CT_STRING, 0, 255);

        $this->AddColumn("Phone", 'mobile_number', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 0, 31);

        $this->AddColumn("Referral URL", 'referrer', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING);

        $delim = "";
        if (isset($_GET['flt_id']) && intval($_GET['flt_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['flt_id']);
            $delim = " and ";
        }
        if (isset($_GET['flt_email']) && strlen($_GET['flt_email']) > 0) {
            $this->m_sFilter.= $delim . "h.email_address like '%" . $_GET['flt_email'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_uname']) && strlen($_GET['flt_uname']) > 0) {
            $this->m_sFilter.= $delim . "h.username like '%" . $_GET['flt_uname'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_fname']) && strlen($_GET['flt_fname']) > 0) {
            $this->m_sFilter.= $delim . "h.first_name like '%" . $_GET['flt_fname'] . "%'";
            $delim = " and ";
        }
        if (isset($_GET['flt_lname']) && strlen($_GET['flt_lname']) > 0) {
            $this->m_sFilter.= $delim . "h.lasts_name like '%" . $_GET['flt_lname'] . "%'";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "SELECT h.*, COUNT(j.id) AS jobs_count
                           FROM homeowner h
                           INNER JOIN job j ON j.homeowner_id = h.id";

        $this->m_sCountSQL = "SELECT COUNT(*) FROM homeowner";

        $this->m_sTableName = 'homeowner';
        $this->m_sTitle = "Homeowners";
        $this->m_sActionURL = "homeowners.php";
        $this->m_sGroupBy = "h.id";
//    $this->m_nOperation = OP_EDIT | OP_ADD;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "username") {
            // Auto-login button
            $return = <<<EOF
{$value} <hr />
<a href="javascript:;"
  onclick="
  document.autoLoginForm.email_address.value='{$rs->GetItem("username")}';
  document.autoLoginForm.password.value='{$rs->GetItem("password")}';
  document.autoLoginForm.submit();"><img src='images/lock.png' class='comm-img' alt='Auto-login' title='Auto-login' /></a>
EOF;

            return $return;
        }

        if ($dbname == "login_register") {
            $register_ip = IpCheckingLink($rs->GetItem("register_ip"));
            $lastlogin_ip = IpCheckingLink($rs->GetItem("last_login_ip"));

            $sRegisterCountyName = getCountryNameByIP($rs->GetItem("register_ip"));
            $sRegisterCountyCode = getCountryCodeByIP($rs->GetItem("register_ip"));
            $sRegisterIcon = strlen($sRegisterCountyCode) ? "<img src='images/icons/" . strtolower($sRegisterCountyCode) . ".png' border='0' title='" . $sRegisterCountyCode . "'>&nbsp;" : "";

            $sLoginCountyName = getCountryNameByIP($rs->GetItem("last_login_ip"));
            $sLoginCountyCode = getCountryCodeByIP($rs->GetItem("last_login_ip"));
            $sLoginIcon = strlen($sLoginCountyCode) ? "<img src='images/icons/" . strtolower($sLoginCountyCode) . ".png' border='0' title='" . $sLoginCountyCode . "'>&nbsp;" : "";


            $value = <<<EOT
 <div style="width: 200px;">Register Date: {$rs->GetItem("created_at")} </div>
 <div style="width: 200px;">Register IP: {$register_ip} {$sRegisterIcon} </div>
 Last Login Date: {$rs->GetItem("last_login")} <br />
 Last Login IP: {$lastlogin_ip} {$sLoginIcon}<br />
EOT;
            return $value;
        }

        if ($dbname == "name") {
            return $rs->GetItem("first_name") . " " . $rs->GetItem("last_name");
        }
        if ($dbname == "jobs_count") {
            return "<a href='jobs.php?flt_homeowner=" . $rs->GetItem("id") . "'>" . $value . "</a>";
        }

        if ($dbname == "referrer") {
            $html = <<<EOF
<textarea style="font-size: 12px; width: 200px; height: 50px;" readonly onclick="this.focus(); this.select();">{$value}</textarea>
EOF;
            return $html;
        }

        return $value;
    }

}

$list = new CUsers();
StartPage('HOMEOWNERMENU', 'HOMEOWNERS');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('HOMEOWNERMENU', 'HOMEOWNERS');
    ?>
    <div class="span-20">
        <form method="get" action="homeowners.php">
            <table>
                <tr>
                    <td>
                        <label>ID</label> <br />
                        <input class="listnav" name="flt_id" value="<?= isset($_GET['flt_id']) ? $_GET['flt_id'] : "" ?>" />
                    </td>
                    <td>
                        <label>Username</label> <br />
                        <input class="listnav" name="flt_uname" value="<?= isset($_GET['flt_uname']) ? $_GET['flt_uname'] : "" ?>" />
                    </td>
                    <td>
                        <label>Email</label> <br />
                        <input class="listnav" name="flt_email" value="<?= isset($_GET['flt_email']) ? $_GET['flt_email'] : "" ?>" />
                    </td>
                    <td>
                        <label>First name</label> <br />
                        <input class="listnav" name="flt_fname" value="<?= isset($_GET['flt_fname']) ? $_GET['flt_fname'] : "" ?>" />
                    </td>
                    <td>
                        <label>Last name</label> <br />
                        <input class="listnav" name="flt_lname" value="<?= isset($_GET['flt_lname']) ? $_GET['flt_lname'] : "" ?>" />
                    </td>

                    <td>
                        <input class="button" type="submit" value="Filter" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="clear"></div>

    <form name="autoLoginForm" target="_blank" method="post" action="<?php echo $HTTPURL . typo3Url("login.php") ?>">
        <input type="hidden" name="autologin" value="1">
        <input type="hidden" name="do" value="1">
        <input type="hidden" name="email_address" value="">
        <input type="hidden" name="password" value="">
    </form>


    <?php
    $list->ShowListPage();
}
TerminatePage();
?>