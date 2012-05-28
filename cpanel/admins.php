<?
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET;
        $this->AddColumn("ID", 'id', CL_VIEW_GRID | CL_VIEW_READONLYEDIT);
        $this->AddColumn("Username", 'user', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 3, 15);
        $this->AddColumn("Password", 'pass', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 3, 15);
        $this->AddColumn("First Name", 'firstname', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 0, 31);
        $this->AddColumn("Last Name", 'lastname', CL_VIEW_GRID | CL_VIEW_EDIT, CT_STRING, 0, 31);

        $this->AddColumn("Blocked", 'blocked', CL_VIEW_GRID | CL_VIEW_EDIT, CT_CHECKBOX);
        $this->AddColumn("Last Login", 'lastlogin', CL_VIEW_GRID);

        $this->AddColumn("Permissions", 'rights', CL_VIEW_GRID | CL_VIEW_EDIT);

        $this->m_sSelectSQL = "select a.* from admin a";

        $this->m_sTableAlias = "a";
        $this->m_sTableName = 'admin';
        $this->m_sOrderBy = 'id desc';
        $this->m_sTitle = "Control panel users";
        $this->m_sActionURL = "admins.php";
//		$this->m_nOperation = 0;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {
        if ($dbname == "rights") {
            $vals = array();
            if ($value & ADMIN)
                $vals[] = "ADMIN";
            if ($value & SUPPORT)
                $vals[] = "SUPPORT";
            if ($value & SALESMAN)
                $vals[] = "SALESMAN";

            $html = implode(", ", $vals);
            return $html;
        }

        if ($dbname == "lastlogin") {
            $sIPLink = IpCheckingLink($rs->GetItem("lastloginip"));
            $sCountyName = getCountryNameByIP($rs->GetItem("lastloginip"));
            $sCountyCode = getCountryCodeByIP($rs->GetItem("lastloginip"));
            $sPic = "";
            if (strlen($sCountyCode))
                $sPic = "<br><img src='./images/icons/" . strtolower($sCountyCode) . ".png' border='0'>&nbsp;";
            return $value . "<br />" . "<div nowrap>" . $sIPLink . $sPic . $sCountyName . "</div>";
        }

        if ($dbname == "user") {
            $sLogLink = '<a href="adminslog.php?flt_username=' . $rs->GetItem("fuser") . '">view log &raquo;</a>';
            return $value . ' <br />' . $sLogLink;
        }

        return $value;
    }

    function CreateCustomEdit($col, &$row)
    {
        if ($col->m_sDBName == "rights") {
            ?>
            <input type="checkbox" name="perm[]" id="perm_<?php echo ADMIN ?>" value="<?php echo ADMIN ?>" <?php echo isset($row->rights) && ($row->rights & ADMIN) ? "checked" : "" ?>>
            <label for="perm_<?php echo ADMIN ?>">ADMIN</label><br />

            <input type="checkbox" name="perm[]" id="perm_<?php echo SUPPORT ?>" value="<?php echo SUPPORT ?>" <?php echo isset($row->rights) && ($row->rights & SUPPORT) ? "checked" : "" ?>>
            <label for="perm_<?php echo SUPPORT ?>">SUPPORT</label><br />

            <!--            <input type="checkbox" name="perm[]" id="perm_<?php echo SALESMAN ?>" value="<?php echo SALESMAN ?>" <?php echo isset($row->rights) && ($row->rights & SALESMAN) ? "checked" : "" ?>>
                        <label for="perm_<?php echo SALESMAN ?>">SALESMAN</label>-->
            <?
            return true;
        }

        return false;
    }

    function GetCustomValue($col)
    {
        if ($col->m_sDBName == "rights") {
            $curval = 0;
            $vals = isset($_POST["perm"]) ? $_POST["perm"] : array();

            foreach ($vals as $v)
                $curval |= $v;

//      print_r($vals); echo $curval; die();

            return $curval;
        }
        return false;
    }

}

;

$list = new CUsers();
StartPage('ADMINMANAGEMENT', 'ADMINS');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('ADMINMANAGEMENT', 'ADMINS');
    ?>
    <form>
        <div style="width: 500px; margin: 10px auto;">
            <table cellpadding="1" cellspacing="1" border="0" style="border: 1px solid #3799FF;">
                <tr>
                    <td class="colsheader" width="250px">Menu name</td>
                    <td class="colsheader" width="70px" align="">ADMIN</td>
                    <td class="colsheader" width="70px" align="">SUPPORT</td>
    <!--                    <td class="colsheader" width="70px" align="">SALESMAN</td>-->
                    <td width="15px">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" colspan="5">
                        <div style="height: 150px; overflow: auto;">
                            <table cellpadding="1" cellspacing="1" border="0" width="99%" style="border: 0px; padding: 1px;">
                                <?php
                                $i = 0;
                                $icon_yes = "<img src='images/tick.png' alt='yes' title='yes' />";
                                $icon_no = "<img src='images/cross2.png' alt='no' title='no' />";
                                foreach ($sysmenu as $menu) {
                                    ?>
                                    <tr class="<?php echo $i ? "ROW1" : "ROW2" ?>">
                                        <td width="250px"><a class="commonlink" href="<?php echo $menu["link"] ?>"><b><?php echo $menu["name"] ?></b></a></td>
                                        <td width="70px" align="center"><?php echo $menu["rights"] & ADMIN ? $icon_yes : $icon_no ?></td>
                                        <td width="70px" align="center"><?php echo $menu["rights"] & SUPPORT ? $icon_yes : $icon_no ?></td>
                <!--                                        <td width="70px" align="center"><?php echo $menu["rights"] & SALESMAN ? $icon_yes : $icon_no ?></td>-->
                                    </tr>
                                    <?php
                                    if (isset($menu["submenu"]) && is_array($menu["submenu"])) {
                                        foreach ($menu["submenu"] as $submenu) {
                                            ?>
                                            <tr style="background: #fff;">
                                                <td style="padding-left: 10px;">&nbsp;&raquo; <a class="commonlink" href="<?php echo $submenu["link"] ?>"><?php echo $submenu["name"] ?></a></td>
                                                <td align="center"><?php echo $submenu["rights"] & ADMIN ? $icon_yes : $icon_no ?></td>
                                                <td align="center"><?php echo $submenu["rights"] & SUPPORT ? $icon_yes : $icon_no ?></td>
                <!--                                                <td align="center"><?php echo $submenu["rights"] & SALESMAN ? $icon_yes : $icon_no ?></td>-->
                                            </tr>
                                            <?php
                                        }
                                        $i = 1 - $i;
                                    }
                                }
                                ?>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <?
    $list->ShowListPage();
}
TerminatePage();
?>