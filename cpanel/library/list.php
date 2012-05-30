<?
define('OP_ADD', 1);
define('OP_DELETE', 2);
define('OP_EDIT', 4);
define('OP_APPROVE', 8);
define('OP_REJECT', 16);
define('OP_BLOCK', 32);
define('OP_UNBLOCK', 64);
define('OP_MARKASNEW', 128);
define('OP_MARKASREAD', 256);

define('CL_VIEW_GRID', 1);
define('CL_VIEW_EDIT', 2);
define('CL_VIEW_READONLYEDIT', 4);

define('CT_INTEGER', 1);
define('CT_REAL', 2);
define('CT_DATE', 3);
define('CT_STRING', 4);
define('CT_TEXT', 5);
define('CT_COMBO', 6);
define('CT_LINK', 7);
define('CT_CHECKBOX', 8);
define('CT_RADIO', 9);
define('CT_TIMESTAMP', 10);
define('CT_IMAGE', 11);
define('CT_PASSWORD', 12);
define('CT_CMYKENTRY', 13);
define('CT_HTML_EDITOR', 14);
define('CT_IP', 15);

class CColumn
{

    var $m_nMinSize;
    var $m_nMaxSize;
    var $m_sName;
    var $m_sDBName;
    var $m_nType;
    var $m_nVisibility;
    var $m_sLinkTo;
    var $m_sComboDBName;
    var $m_sComboSQL;

}

;

////////////////////////////////////////////////////////////////////////////////
//
// class CList
// purpose: generic grid for binding with SQL script and displaying result
// date: 23.12.2002
//
////////////////////////////////////////////////////////////////////////////////

class CList
{

// vars
    var $m_bDebugMode;
// list numbers related
    var $m_nPageSize;  // visible rows per page
    var $m_nCurrentPage; // current visible page
    var $m_nTotalPages;  // total pages for the current data set
    var $m_nTotalRecords; // total records in the data set
// navigations related
    var $m_sActionURL;  // url for the addnew, delete and edit buttons
    var $m_sAppendToInsert; // parameters added to the add new link
// SQL related
    var $m_sOrderBy;  // order by clause
    var $m_sFilter;   // where clause
    var $m_sGroupBy; // group by clause
    var $m_sTableName;  // table name
    var $m_sPrimaryKey;  // primary key in the data set (used for edit, delete etc.)
// internal
    var $m_nOperation;  // grid permissions 
// columns
    var $m_aColumns;  // array specifying columns in the data set
// labels
    var $m_sTitle;   // grid title
// result set
    var $m_oRs;    // CRecordset object
    var $m_sSelectSQL;  // optional select sql 
    var $m_sCountSQL;
    var $m_iHeight;
    var $m_iWidth;
    var $m_sTableAlias;
    var $m_sError;
// file upload handler
    var $m_oFile;
// custom hidden values for the edit form
    var $m_aHiddenVals = array();

// methods
    function CList() // construction, set default parameters
    {
        global $_SESSION;
        $this->m_nCurrentPage = $this->m_nTotalPages = 0;
        $this->m_bDebugMode = true;
//		setcookie ('rows',20,time()*2);
        $this->m_nPageSize = isset($_SESSION['rows']) ? $_SESSION['rows'] : 15;
        $this->m_nOperation = OP_ADD | OP_DELETE | OP_EDIT;
        $this->m_sSQL = $this->m_sAppendToInsert = "";
        $this->m_aColumns = array();
        $this->m_sPrimaryKey = "id";
        $this->m_iHeight = 450;
        $this->m_iWidth = 600;
        $this->m_sTableAlias = "";
        $this->InitiliseList();
        $this->ReadURLParams();
    }

    function InitiliseList()
    {
        
    }

// must overwrite

    function AddButtons()
    {
        
    }

// overwrite if additional buttons are needed (in the navigation part)

    function IsDeletePossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record deleting

    function IsMarkMessagePossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record deleting

    function IsApprovePossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record approving

    function IsRejectPossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record rejecting

    function IsBlockPossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record blocking

    function IsUnblockPossible($id)
    {
        return true;
    }

// overwrite for custom allowing/denying record unblocking

    function CreateCustomEdit($col, &$row)
    {
        return false;
    }

// overwrite to create custom edit control in the edit page

    function GetCustomValue($col)
    {
        return false;
    }

// overwrite to get custom value for the edit control in the edit page 

    function OnDelete($sel)
    {
        
    }

    function OnMarkMessage($sel, $status)
    {
        
    }

    function OnApprove($sel)
    {
        
    }

    function OnReject($sel)
    {
        
    }

    function OnBlock($sel)
    {
        
    }

    function OnUnblock($sel)
    {
        
    }

    function AddCustomButtons()
    {
        
    }

    function CustomValidator()
    {
        
    }

    function ReadURLParams() // read grid parameters (get or post)
    {
        global $_POST, $_GET;
        $this->m_nCurrentPage = isset($_POST['page']) ? intval($_POST['page']) - 1 : (isset($_GET['page']) ? intval($_GET['page']) : 0);
        if ($this->m_nCurrentPage < 0)
            $this->m_nCurrentPage = 0;

        if (isset($_POST['ls_action']) && $_POST['ls_action'] != '0') {
            switch (intval($_POST['ls_action'])) {
                case 1: $this->DeleteRecords();
                    break;
                case 2: $this->ApproveRecords();
                    break;
                case 3: $this->RejectRecords();
                    break;
                case 4: $this->BlockRecords();
                    break;
                case 5: $this->UnblockRecords();
                    break;
                case 6: $this->MarkMessage(1);
                    break;
                case 7: $this->MarkMessage(0);
                    break;
            }
        }

        if (count($_POST) > 0 && (!isset($_POST['action']) || $_POST['action'] != 'save')) {
            global $_SERVER;
            $url = $_SERVER['PHP_SELF'] . "?";
            foreach ($_GET as $key => $value)
                if (strlen($value) && strlen($key) && $key != 'page')
                    $url.=$key . "=" . $value . "&";
            if ($this->m_nCurrentPage)
                $url.="page=" . $this->m_nCurrentPage . "&";
            echo "<script>window.location='" . $url . "';</script>";
            die();
        }
    }

    function GetSelectedRecords() // get selected records for current page
    {
        global $_POST, $_GET;
        $selected = array();
        foreach ($_POST['chk'] as $key => $value)
            $selected[] = $key;
        return is_array($selected) ? implode(",", $selected) : $selected;
    }

    function DeleteRecords() // delete selected records
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsDeletePossible($selected)) {
                $this->OnDelete($selected);
                $g_oConn->Execute("delete from " . $this->m_sTableName . " where " . $this->m_sPrimaryKey . " in (" . $selected . ")");
            }
        }
    }

    function ApproveRecords() // approve selected records
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsApprovePossible($selected))
                $this->OnApprove($selected);
        }
    }

    function RejectRecords() // reject selected records
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsRejectPossible($selected))
                $this->OnReject($selected);
        }
    }

    function BlockRecords() // block selected records
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsBlockPossible($selected))
                $this->OnBlock($selected);
        }
    }

    function UnblockRecords() // block selected records
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsUnblockPossible($selected))
                $this->OnUnblock($selected);
        }
    }

    function MarkMessage($status)
    {
        global $g_oConn;
        $selected = $this->GetSelectedRecords();
        if (strlen($selected)) {
            if ($this->IsMarkMessagePossible($selected, $status))
                $this->OnMarkMessage($selected, $status);
        }
    }

    function ShowListPage()  // show default grid page
    {
        if (isset($_POST['act']) && $_POST['act'] == 'setflag') {
            $error = $this->SetFlag();
            if (!$error) {
                echo "<script>window.location=window.location; </script>";
                return;
            }
        }

        $this->OpenRs();
        global $_SERVER, $_GET;
        $url = $_SERVER['PHP_SELF'] . "?";
        foreach ($_GET as $key => $value) {
            if (is_array($value))
                foreach ($value as $k => $v)
                    $url.=$key . "[]=" . $v . "&";
            elseif (strlen($value) && strlen($key) && $key != 'page')
                $url.=$key . "=" . $value . "&";
        }
        ?>
        <form id="list" method="post" name="FRM_LIST" action="<?= $url ?>">
            <input type="hidden" name="ls_action" value="0">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <caption><?= $this->m_sTitle; ?> - <?php echo $this->m_nTotalRecords ?> found</caption>
                <?php $this->ShowListContent(); ?>
                <tr>
                    <td class="bar"><?php $this->ShowNavigation($url); ?></td>
                </tr>
            </table>
        </form>
        <?
    }

    function ShowNavigation($url) // show default navigation part of the grid
    {
        ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="10$">
                    <?
                    if ($this->m_oRs->GetRecordCount()
                            && $this->m_nOperation
                            && ($this->m_nOperation != OP_EDIT)
                            && ($this->m_nOperation != OP_ADD)
                            && ($this->m_nOperation != (OP_EDIT | OP_ADD))
                    )
                        echo "<input type='checkbox' name='selectall' onclick='javascript:checkAll();'>";
                    else
                        echo "&nbsp;";
                    ?>
                </td>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="listnav" style="text-align: center; width: 80%">
                                <?
                                if ($this->m_nCurrentPage)
                                    echo "<a href='" . $url . "page=0'><img src='images/btnMinusMinus.gif' border='0'></a>";
                                else
                                    echo "<img src='images/btnMinusMinusGray.gif' border='0'>";
                                if ($this->m_nCurrentPage)
                                    echo "<a href='" . $url . "page=" . ($this->m_nCurrentPage - 1) . "'><img src='images/btnMinus.gif' border='0'></a>";
                                else
                                    echo "<img src='images/btnMinusGray.gif' border='0'>";
                                ?>
                                <input type="text" size="10" name="page" value="<?= $this->m_nCurrentPage + 1 ?>"> / <?= $this->m_nTotalPages + 1 ?>
                                <input type="submit" value="Go" class="button" />
                                <?
                                if ($this->m_nCurrentPage != $this->m_nTotalPages)
                                    echo "<a href='" . $url . "page=" . ($this->m_nCurrentPage + 1) . "'><img src='images/btnPlus.gif' border='0'></a>";
                                else
                                    echo "<img src='images/btnPlusGray.gif' border='0'>";
                                if ($this->m_nCurrentPage != $this->m_nTotalPages)
                                    echo "<a href='" . $url . "page=" . $this->m_nTotalPages . "'><img src='images/btnPlusPlus.gif' border='0'></a>";
                                else
                                    echo "<img src='images/btnPlusPlusGray.gif' border='0'>";
                                ?>
                            </td>
                            <td class="listnav" style="text-align: right;">
                                <?
                                if ($this->m_nOperation & OP_MARKASNEW) {
                                    echo "<input type='button' class='button' value='Mark as new' onClick=\"javascript:return confirmAction('" . ($url) . "','mark as new');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_MARKASREAD) {
                                    echo "<input type='button' class='button' value='Mark as read' onClick=\"javascript:return confirmAction('" . ($url) . "','mark as read');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_APPROVE) {
                                    echo "<input type='button' class='button' value='Approve all' onClick=\"javascript:return confirmAction('" . ($url) . "','approve');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_REJECT) {
                                    echo "<input type='button' class='button' value='Reject all' onClick=\"javascript:return confirmAction('" . ($url) . "','reject');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_BLOCK) {
                                    echo "<input type='button' class='button' value='Block all' onClick=\"javascript:return confirmAction('" . ($url) . "','block');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_UNBLOCK) {
                                    echo "<input type='button' class='button' value='Unblock all' onClick=\"javascript:return confirmAction('" . ($url) . "','unblock');\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_ADD) {
                                    $path = ($this->m_sActionURL . (strpos($this->m_sActionURL, "?") !== false ? "&" : "?") . "action=edit" . $this->m_sAppendToInsert);
                                    echo "<input type='button' class='button' value='Add New' onClick=\"popupEdit('$path'," . $this->m_iWidth . "," . $this->m_iHeight . ");\">";
                                    echo '&nbsp;';
                                }
                                ?>
                                <?
                                if ($this->m_nOperation & OP_DELETE)
                                    echo "<input type='button' class='button' value='Delete' onClick=\"javascript:return confirmAction('" . ($url) . "','delete');\">";
                                $this->AddButtons();
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
    }

    function CustomRenderHeader()
    {
        return false;
    }

    function CustomRenderRow($class, $isdata)
    {
        return false;
    }

    function ShowListContent() // show entire list content
    {
        global $GLOBAL_URL;
        $colsize = is_array($this->m_aColumns) ? count($this->m_aColumns) : 0;
        if (!$colsize)
            return;
        $viscols = 0;
        if (!$this->CustomRenderHeader()):
            ?>
            <thead>
                <tr class="odd">
                    <th width="1%" colspan="2">&nbsp;</th>
                    <?php for ($i = 0; $i < $colsize; $i++): ?>
                        <?php if ($this->m_aColumns[$i]->m_nVisibility & CL_VIEW_GRID): ?>
                            <?php $viscols++; ?>
                            <th class="colsheader"><?php echo $this->m_aColumns[$i]->m_sName ?>&nbsp;</th>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>	
            </thead>
        <?php endif; ?>
        <tbody>
            <?php
            for ($i = 0, $isdata = true; $i < $this->m_nPageSize; $i++) {
                $explanatory = "";
                $exppos = -1;
                if ($isdata)
                    $isdata = $this->m_oRs->MoveNext();
                if ($this->CustomRenderRow("ROW" . (2 - $i % 2), $isdata))
                    continue;
                ?>
                <tr class="ROW<?= (2 - $i % 2) ?>">
                    <? if ($isdata) {
                        ?>
                        <td width="1%">
                            <?php if ($this->m_nOperation && ($this->m_nOperation != OP_EDIT) && ($this->m_nOperation != OP_ADD) && ($this->m_nOperation != (OP_EDIT | OP_ADD))): ?>
                                <input type="checkbox" name="chk[<?= $this->m_oRs->GetItem($this->m_sPrimaryKey) ?>]" />
                            <?php endif; ?>
                        </td>
                        <td width="1%">
                            <?php if ($this->m_nOperation & OP_EDIT): ?>
                                <a href="#" onclick="javascript:popupEdit('<?= ($this->m_sActionURL . (strpos($this->m_sActionURL, "?") !== false ? "&" : "?") . "action=edit&id=" . $this->m_oRs->GetItem($this->m_sPrimaryKey)) ?>',<?= $this->m_iWidth ?>,<?= $this->m_iHeight ?>);return false;"><img alt="Edit" src="images/edit.gif" border="0"></a>
                                <?php $this->AddCustomButtons(); ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <?
                        for ($j = 0; $j < $colsize; $j++) {
                            if ($this->m_aColumns[$j]->m_nVisibility & CL_VIEW_GRID) {
                                echo("<td class='ROW" . (2 - $i % 2) . "'>");
                                if ($this->m_aColumns[$j]->m_nType == CT_LINK) {
                                    if (preg_match("/~f<([a-zA-Z]*)>/", $this->m_aColumns[$j]->m_sDBName, $regs))
                                        $itm = preg_replace("/~f<[a-zA-Z]*>/", $this->m_oRs->GetItem($regs[1]), $this->m_aColumns[$j]->m_sDBName);
                                    else
                                        $itm = $this->m_oRs->GetItem($this->m_aColumns[$j]->m_sDBName);
                                }
                                else if ($this->m_aColumns[$j]->m_nType == CT_IMAGE) {
                                    $name = $this->m_aColumns[$j]->m_sDBName;
                                    $itm = $this->m_oRs->GetItem($name);
                                    $itm = $this->GetCellEntry($name, $itm, $this->m_oRs, $j);
                                } else if ($this->m_aColumns[$j]->m_nType == CT_COMBO) {
                                    $name = strlen($this->m_aColumns[$j]->m_sComboDBName) ? $this->m_aColumns[$j]->m_sComboDBName : $this->m_aColumns[$j]->m_sDBName;
                                    $itm = $this->m_oRs->GetItem($name);
                                    $itm = $this->GetCellEntry($name, $itm, $this->m_oRs, $j);
                                } else {
                                    $itm = $this->m_oRs->GetItem($this->m_aColumns[$j]->m_sDBName);
                                    if ($this->m_aColumns[$j]->m_nType == CT_CHECKBOX)
                                        $itm = $itm > 0 ? 'Yes' : 'No';
                                    if ($this->m_aColumns[$j]->m_nType == CT_DATE) {
                                        $itm = date('Y-F-d H:i:s', strtotime($itm));
                                    }
                                    $itm = $this->GetCellEntry($this->m_aColumns[$j]->m_sDBName, $itm, $this->m_oRs, $j);
                                }
                                echo $itm;
                                echo("</td>");
                            }
                            if (isset($this->m_aColumns[$j]->m_sExplanatory) && strlen($this->m_aColumns[$j]->m_sExplanatory)) {
                                $explanatory = $this->m_oRs->GetItem($this->m_aColumns[$j]->m_sExplanatory);
                                $spacepos = substr($explanatory, 100);
                                $spacepos = strpos($spacepos, " ");
                                $explanatory = substr($explanatory, 0, ($spacepos !== false ? 100 + $spacepos : 100));
                                $exppos = $j;
                            }
                        }
                    }
                    else
                        echo "<td height='20px' colspan='" . ($viscols + 2) . "'>&nbsp;</td>";
                    ?>
                </tr>
                <? if (strlen($explanatory)) {
                    ?>
                    <tr class="ROW<?= (2 - $i % 2) ?>">
                        <td colspan="<?= ($exppos + 1) ?>"></td><td colspan='<?= $viscols + 2 - $exppos ?>'><?= $explanatory ?>...</td>
                    </tr>
                <? } ?>
            <? } ?>
        </tbody>
        </table>
        <?
    }

    function GetCellEntry($dbname, $value, &$rs, $column) // default return the $value itself, overwrite to customize grid
    {
        return $value;
    }

    function IsDate($date) // check whether the date is correct
    {
        return preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $regs) ? checkdate($regs[2], $regs[3], $regs[1]) : false;
    }

    function SaveData() // save data entered in the edit page
    {
        global $_POST, $g_oConn;
// checking data
        $isadding = $_POST['id'] == -1;
        $cols = $values = $sql = "";
        $separator = $isadding ? "(" : "";

        if ($isadding)
            $this->OnBeforeInsert();
        else
            $this->OnBeforeUpdate();

        for ($i = 0, $count = count($this->m_aColumns); $i < $count; $i++) {

            if (!strlen($this->m_aColumns[$i]->m_sDBName))
                continue;
            if (isset($_POST[$this->m_aColumns[$i]->m_sDBName]) && (($this->m_aColumns[$i]->m_nType == CT_CMYKENTRY || $this->m_aColumns[$i]->m_nType == CT_COMBO || $this->m_aColumns[$i]->m_nType == CT_DATE) || ($this->m_aColumns[$i]->m_nMinSize != -1 && $this->m_aColumns[$i]->m_nMaxSize != -1))) {
                switch ($this->m_aColumns[$i]->m_nType) {
                    case CT_INTEGER:
                        $val = intval($_POST[$this->m_aColumns[$i]->m_sDBName]);
                        if ($val < $this->m_aColumns[$i]->m_nMinSize || $val > $this->m_aColumns[$i]->m_nMaxSize)
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Must be between&nbsp;" . $this->m_aColumns[$i]->m_nMinSize . " and " . $this->m_aColumns[$i]->m_nMaxSize;
                        break;
                    case CT_TIMESTAMP:
                        if (!$this->IsValidTimestamp($_POST[$this->m_aColumns[$i]->m_sDBName]))
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Incorrect timestamp";
                        break;
                    case CT_REAL:
                        $val = floatval($_POST[$this->m_aColumns[$i]->m_sDBName]);
                        if ($val < $this->m_aColumns[$i]->m_nMinSize || $val > $this->m_aColumns[$i]->m_nMaxSize)
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Must be between" . $this->m_aColumns[$i]->m_nMinSize . " and " . $this->m_aColumns[$i]->m_nMaxSize;
                        break;
                    case CT_DATE:
                        if (strlen($_POST[$this->m_aColumns[$i]->m_sDBName]) && !$this->IsDate($_POST[$this->m_aColumns[$i]->m_sDBName]))
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Date must be in format yyyy-mm-dd";
                        break;
                    case CT_STRING:
                    case CT_PASSWORD:
                        $val = strlen($_POST[$this->m_aColumns[$i]->m_sDBName]);
                        if ($val < $this->m_aColumns[$i]->m_nMinSize || $val > $this->m_aColumns[$i]->m_nMaxSize)
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Must be between&nbsp;" . $this->m_aColumns[$i]->m_nMinSize . " and " . $this->m_aColumns[$i]->m_nMaxSize . "&nbsp;characters";
                        break;
                    case CT_TEXT:
                    case CT_COMBO:
                        $val = $_POST[$this->m_aColumns[$i]->m_sDBName];
                        if ($this->m_aColumns[$i]->m_nMinSize == 1 && (!strlen($val) || $val == -1))
                            return " " . $this->m_aColumns[$i]->m_sName . " must be selected";
                        break;
                    case CT_RADIO:
                        break;
                    case CT_CMYKENTRY:
                        $val = $_POST[$this->m_aColumns[$i]->m_sDBName];
                        if (!$this->IsCMYKEntry($val))
                            return " " . $this->m_aColumns[$i]->m_sName . " must be color format";
                        break;
                    case CT_IP:
                        $val = $_POST[$this->m_aColumns[$i]->m_sDBName];
                        break;
                }
            }

            if (isset($_POST[$this->m_aColumns[$i]->m_sDBName . "_isfile"]) && $_POST[$this->m_aColumns[$i]->m_sDBName . "_isfile"] == 1) {
                switch ($this->m_aColumns[$i]->m_nType) {
                    case CT_IMAGE:
                        if (!isset($_FILES[$this->m_aColumns[$i]->m_sDBName]) || !strlen($_FILES[$this->m_aColumns[$i]->m_sDBName]["name"]))
                            return $this->m_aColumns[$i]->m_sName . ": &nbsp;Plaese, choose " . $this->m_aColumns[$i]->m_sName;
                        elseif (!$this->IsTypeAllowed($_FILES[$this->m_aColumns[$i]->m_sDBName]["type"]))
                            return $this->m_aColumns[$i]->m_sName . "&nbsp;Type not allowed";
                        break;
                }
            }


            $curval = $this->GetCustomValue($this->m_aColumns[$i]);
            if (!$curval) {
                $curval = isset($_POST[$this->m_aColumns[$i]->m_sDBName]) ? $_POST[$this->m_aColumns[$i]->m_sDBName] : "0";
                if ($this->m_aColumns[$i]->m_nType == CT_COMBO && $curval == -1)
                    $curval = "";
//				if (!strlen($curval) && $this->m_aColumns[$i]->m_nType == CT_INTEGER) $curval = 0;
            }

            if ($this->m_aColumns[$i]->m_nType == CT_IP)
                $curval = ip2long($curval);

            if (($this->m_aColumns[$i]->m_nVisibility & CL_VIEW_EDIT)) {
                if ($this->m_aColumns[$i]->m_nType == CT_STRING || strlen($curval)) {
                    if ($isadding) {
                        $cols .= $separator . $this->m_aColumns[$i]->m_sDBName;
                        $values .= $separator . "" . vSqlStr($curval) . "";
                        if ($separator = "(")
                            $separator = ",";
                    }
                    else {
                        $sql .= $separator . $this->m_aColumns[$i]->m_sDBName . "=" . vSqlStr($curval) . "";
                        if (!strlen($separator))
                            $separator = ",";
                    }
                }
            }
        }
        if ($isadding) {
            $cols.=")";
            $values.=")";
            $sql = "insert into " . $this->m_sTableName . " " . $cols . " values " . $values;
        }
        else
            $sql = "update " . $this->m_sTableName . " set " . $sql . " where " . $this->m_sPrimaryKey . "=" . $_POST['id'];
// saving data
//    die($sql);

        $g_oConn->Execute($sql);

        if ($isadding)
            $this->OnInsert();
        else
            $this->OnUpdate();

        return false;
    }

    function SetFlag()
    {
        global $g_oConn;
        $sql = "update " . $this->m_sTableName . " set fflag = " . $_POST["flag"] . " where fid = " . $_POST["fid"];
        $g_oConn->Execute($sql);
        return false;
    }

    function IsCMYKEntry($value)
    {
        return preg_match("/\d+\.*\d*,\d+\.*\d*,\d+\.*\d*,\d+\.*\d*/", $value);
    }

    function OnInsert()
    {
        
    }

    function OnUpdate()
    {
        
    }

    function OnBeforeInsert()
    {
        
    }

    function OnBeforeUpdate()
    {
        
    }

    function ShowEditPage() // show edit page (and pre-load the data for the current record (if any))
    {
        global $_POST, $_GET, $SERVER, $g_oConn;

        $row = null;

        if (isset($_POST['action']) && $_POST['action'] == 'save') {
            $this->m_sError = strlen($this->CustomValidator()) ? $this->CustomValidator() : $this->SaveData();

            if (!$this->m_sError) {
                echo "<script>window.opener.location.reload(); window.close();</script>";
                return;
            }
        }
        $id = isset($_GET['id']) ? $_GET['id'] : -1;
        if ($id != -1) {
            $sql = "select * from " . $this->m_sTableName . " where " . $this->m_sPrimaryKey . "=" . $id;
            if (isset($this->m_sEditSqlPattern) && strlen($this->m_sEditSqlPattern)) {
                $sql = sprintf($this->m_sEditSqlPattern, $id);
            }
            $rs = new CRecordset($sql);
            if (!$rs->MoveNext())
                return;
            $row = &$rs->m_oRow;
        }
        for ($i = 0, $count = count($this->m_aColumns); $i < $count; $i++)
            if (isset($_GET[$this->m_aColumns[$i]->m_sDBName]))
                $row->{$this->m_aColumns[$i]->m_sDBName} = $_GET[$this->m_aColumns[$i]->m_sDBName];

        for ($i = 0, $count = count($this->m_aColumns); $i < $count; $i++)
            if (isset($_POST[$this->m_aColumns[$i]->m_sDBName]))
                $row->{$this->m_aColumns[$i]->m_sDBName} = $_POST[$this->m_aColumns[$i]->m_sDBName];
        ?>
        <form id="edit" method="post" action="<?= $SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $id ?>">
            <?
            foreach ($this->m_aHiddenVals as $name => $val)
                echo "<input type='hidden' name='" . $name . "' value='" . $val . "'>";
            ?>

            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <caption><?= $this->m_sTitle; ?></caption>
                <?
                if (isset($this->m_sError) && strlen($this->m_sError)) {
                    ?>
                    <tr>
                        <td colspan="3" class="red">&nbsp;<?= "Error&nbsp;" . $this->m_sError ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" height="7px"><img src="images/space.gif"></td>
                    </tr>
                    <?
                }
                for ($i = 0, $size = is_array($this->m_aColumns) ? count($this->m_aColumns) : 0; $i < $size; $i++) {
                    if (!($this->m_aColumns[$i]->m_nVisibility & CL_VIEW_EDIT) && !($this->m_aColumns[$i]->m_nVisibility & CL_VIEW_READONLYEDIT))
                        continue;
                    $disabled = ($this->m_aColumns[$i]->m_nVisibility & CL_VIEW_READONLYEDIT);
                    ?>
                    <tr>
                        <td class="editfont" valign="top" colspan="2">
                            &nbsp;<?= $this->m_aColumns[$i]->m_sName ?>:
                            <?
                            if ($this->m_aColumns[$i]->m_nMaxSize != -1 && $this->m_aColumns[$i]->m_nMinSize > 0)
                                echo "<span class='red'>*</span>";
                            ?>
                        <td>
                            <?
                            if (!$this->CreateCustomEdit($this->m_aColumns[$i], $row)) {
                                switch ($this->m_aColumns[$i]->m_nType) {
                                    case CT_INTEGER:
                                    case CT_REAL:
                                    case CT_DATE:
                                    case CT_STRING:
                                    case CT_IP:
                                    case CT_CMYKENTRY:
                                        echo "<input " . ($disabled ? "disabled" : "") . " style='width: 250px;' name='" . $this->m_aColumns[$i]->m_sDBName . "' value=\"" . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $this->Escape($row->{$this->m_aColumns[$i]->m_sDBName}) : "") . "\">";
                                        break;
                                    case CT_PASSWORD:
                                        echo "<input " . ($disabled ? "disabled" : "") . " style='width: 250px;' type='text' name='" . $this->m_aColumns[$i]->m_sDBName . "' value=\"" . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $this->Escape($row->{$this->m_aColumns[$i]->m_sDBName}) : "") . "\">";
                                        break;
                                    case CT_TIMESTAMP:
                                        echo "<input " . ($disabled ? "disabled" : "") . " name='" . $this->m_aColumns[$i]->m_sDBName . "' value=\"" . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $this->Escape($row->{$this->m_aColumns[$i]->m_sDBName}) : time()) . "\">";
                                        break;
                                    case CT_TEXT:
                                        echo "<textarea " . ($disabled ? "disabled" : "") . " style='width: 250px; height: 100px;' name='" . $this->m_aColumns[$i]->m_sDBName . "'>" . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $row->{$this->m_aColumns[$i]->m_sDBName} : "") . "</textarea>";
                                        break;
                                    case CT_HTML_EDITOR:
                                        ?>
                                        <script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce.js">
                                        </script>
                                        <script language="javascript" type="text/javascript">
                                            tinyMCE.init({
                                                mode : "exact",
                                                elements : "<?= $this->m_aColumns[$i]->m_sDBName ?>",
                                                theme : "advanced",
                                                plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu",
                                                theme_advanced_buttons1_add_before : "save,separator",
                                                theme_advanced_buttons1_add : "fontselect,fontsizeselect",
                                                theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
                                                theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
                                                theme_advanced_buttons3_add_before : "tablecontrols,separator",
                                                theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print",
                                                theme_advanced_toolbar_location : "top",
                                                theme_advanced_toolbar_align : "left",
                                                //theme_advanced_path_location : "bottom",
                                                plugin_insertdate_dateFormat : "%Y-%m-%d",
                                                plugin_insertdate_timeFormat : "%H:%M:%S",
                                                extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
                                                external_link_list_url : "example_data/example_link_list.js",
                                                external_image_list_url : "example_data/example_image_list.js",
                                                flash_external_list_url : "example_data/example_flash_list.js"
                                            });
                                        </script>
                                        <?
                                        echo "<textarea " . ($disabled ? "disabled" : "") . " rows='40' cols='60' name='" . $this->m_aColumns[$i]->m_sDBName . "'>" . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $row->{$this->m_aColumns[$i]->m_sDBName} : "") . "</textarea>";
                                        break;
                                    case CT_CHECKBOX:
                                        echo "<input " . ($disabled ? "disabled" : "") . " type='checkbox' value='1' name='" . $this->m_aColumns[$i]->m_sDBName . "' " . (isset($row->{$this->m_aColumns[$i]->m_sDBName}) && $row->{$this->m_aColumns[$i]->m_sDBName} > 0 ? "checked" : "") . ">";
                                        break;
                                    case CT_COMBO:
                                        echo "<select " . ($disabled ? "disabled" : "") . " name='" . $this->m_aColumns[$i]->m_sDBName . "' style='width: 100%;'>";
                                        echo "<option value='-1'>None";
                                        $this->FillListSelect($i, isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $row->{$this->m_aColumns[$i]->m_sDBName} : $this->m_aColumns[$i]->m_iDefaultValue);
                                        echo "</select>";
                                        break;
                                    case CT_RADIO:
                                        $value = isset($row->{$this->m_aColumns[$i]->m_sDBName}) ? $row->{$this->m_aColumns[$i]->m_sDBName} : -1;
                                        echo "<table width='100%' border='0'>";
                                        $rs = new CRecordset($this->m_aColumns[$i]->m_sComboSQL);
                                        while ($rs->MoveNext())
                                            echo "<tr><td><input name='" . $this->m_aColumns[$i]->m_sDBName . "' " . ($rs->GetItem('value') == $value ? "selected" : "") . " type='radio' value='" . $rs->GetItem('value') . "'>" . $rs->GetItem('text') . "</td></tr>";
                                        echo "</table>";
                                        break;
                                    case CT_IMAGE:
                                        echo "<input type='hidden' name='" . $this->m_aColumns[$i]->m_sDBName . "_isfile' value=1>";
                                        echo "<input " . ($disabled ? "disabled" : "") . " name='" . $this->m_aColumns[$i]->m_sDBName . "' type='file'>";
                                        break;
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <? } ?>
                <tr>
                    <td colspan="3" align="center" >
                        <input type="submit" value="Save" class="button" />&nbsp;
                        <input type="button" value="Close" onclick="window.close();" class="button" />
                    </td>
                </tr>
            </table>
        </form>
        <?
    }

    function Escape($str) // escape the special html symbols
    {
        $str = str_replace("\"", "&quot;", $str);
        $str = str_replace("<", "&lt;", $str);
        $str = str_replace("<", "&gt;", $str);
        return $str;
    }

    function FillListSelect($col, $value) // fill in the combo box for the CT_COMBO type
    {

        if ($this->m_aColumns[$col]->m_nType != CT_COMBO)
            return;
        $rs = new CRecordset($this->m_aColumns[$col]->m_sComboSQL);
        while ($rs->MoveNext())
            echo "<option " . ($rs->GetItem('value') == $value ? "selected" : "") . " value='" . $rs->GetItem('value') . "'>" . $rs->GetItem('text') . "</option>";
    }

    function AddColumn($sName, $sDBName, $nVisibility = CL_VIEW_EDIT, $nType = CT_STRING, $nMinSize = -1, $nMaxSize = -1, $sExplanatory = "") // add new column
    {
        if ($nType == CT_COMBO || $nType == CT_IMAGE)
            return false;
        $size = count($this->m_aColumns);
        $this->m_aColumns[$size]->m_sName = $sName;
        $this->m_aColumns[$size]->m_sDBName = $sDBName;
        $this->m_aColumns[$size]->m_nType = $nType;
        $this->m_aColumns[$size]->m_nMinSize = $nMinSize;
        $this->m_aColumns[$size]->m_nMaxSize = $nMaxSize;
        $this->m_aColumns[$size]->m_nVisibility = $nVisibility;
        $this->m_aColumns[$size]->m_sExplanatory = $sExplanatory;
        $this->m_sComboSQL = "";
    }

    function AddComboColumn($sName, $sDBName, $sComboDBName, $sComboSQL, $type = CT_COMBO, $nVisibility = CL_VIEW_EDIT, $mandatory = true, $iDefaultValue = -1) // add new column (combo only)
    {
        $size = count($this->m_aColumns);
        $this->m_aColumns[$size]->m_sName = $sName;
        $this->m_aColumns[$size]->m_sDBName = $sDBName;
        $this->m_aColumns[$size]->m_sComboDBName = $sComboDBName;
        $this->m_aColumns[$size]->m_sComboSQL = $sComboSQL;
        $this->m_aColumns[$size]->m_nType = $type;
        $this->m_aColumns[$size]->m_nMinSize = $mandatory ? 1 : -1;
        $this->m_aColumns[$size]->m_nMaxSize = $mandatory ? 1 : -1;
        $this->m_aColumns[$size]->m_nVisibility = $nVisibility;
        $this->m_aColumns[$size]->m_iDefaultValue = $iDefaultValue;
    }

    /*
     * This function is made to handle the file uploads, but there is no time to make it as it has to be, i.e. to add / set
     * a custom width & height when the uploaded file is image, etc. It's separeted and that's a good start when a new logic
     * has to be applied later.
     */

    function AddImageColumn($sName, $sDBName, $nVisibility = CL_VIEW_EDIT, $type = CT_IMAGE, $mandatory = true, $iDefaultValue = -1) // add new column (file only)
    {
        $size = count($this->m_aColumns);
        $this->m_aColumns[$size]->m_sName = $sName;
        $this->m_aColumns[$size]->m_sDBName = $sDBName;
        $this->m_aColumns[$size]->m_nType = $type;
        $this->m_aColumns[$size]->m_nMinSize = $mandatory ? 1 : -1;
        $this->m_aColumns[$size]->m_nMaxSize = $mandatory ? 1 : -1;
        $this->m_aColumns[$size]->m_nVisibility = $nVisibility;
        $this->m_aColumns[$size]->m_iDefaultValue = $iDefaultValue;
    }

    /* function AddHTMLEditorColumn($sName,$sDBName,$nVisibility=CL_VIEW_EDIT,$nType=CT_HTML_EDITOR,$nMinSize=-1,$nMaxSize=-1,$sExplanatory="") // add new editor field column
      {
      if ($nType == CT_COMBO) return false;
      $size = count($this->m_aColumns);
      $this->m_aColumns[$size]->m_sName = $sName;
      $this->m_aColumns[$size]->m_sDBName = $sDBName;
      $this->m_aColumns[$size]->m_nType = $nType;
      $this->m_aColumns[$size]->m_nMinSize = $nMinSize;
      $this->m_aColumns[$size]->m_nMaxSize = $nMaxSize;
      $this->m_aColumns[$size]->m_nVisibility = $nVisibility;
      $this->m_aColumns[$size]->m_sExplanatory = $sExplanatory;
      $this->m_sComboSQL = "";
      } */

    function BuildSql() // build the sql (if m_sSelectSQL is not specified)
    {
        $sql = "";
        if (!strlen($this->m_sSelectSQL)) {
            $sql = "select ";
            for ($i = 0, $sep = '', $size = is_array($this->m_aColumns) ? count($this->m_aColumns) : 0; $i < $size; $i++)
                if (strlen($this->m_aColumns[$i]->m_sDBName) && $this->m_aColumns[$i]->m_nType != CT_LINK) {
                    $sql .= $sep . $this->m_aColumns[$i]->m_sDBName;
                    $sep = ',';
                }
            $sql .= " from " . $this->m_sTableName;
        }
        else
            $sql = $this->m_sSelectSQL;
        if (strlen($this->m_sFilter))
            $sql .= " where " . $this->m_sFilter;
        if (strlen($this->m_sGroupBy))
            $sql .= " group by " . $this->m_sGroupBy;
        if (strlen($this->m_sOrderBy))
            $sql .= " order by " . $this->m_sOrderBy;
        $sql .= " limit " . $this->m_nPageSize . " offset " . ($this->m_nPageSize * $this->m_nCurrentPage);

//    echo nl2br($sql);

        return $sql;
    }

    function OpenRs() // get the total rows count and open the data set
    {
        global $g_oConn;
        $sql = $this->GetCountSQL();
        if (!$sql)
            $sql = "select count(*) from " . $this->m_sTableName . " " . $this->m_sTableAlias;
        if (strlen($this->m_sFilter))
            $sql .= " where " . $this->m_sFilter;

        $this->m_nTotalRecords = $g_oConn->GetValue($sql);

        $this->m_nTotalPages = intval(($this->m_nTotalRecords - 1) / $this->m_nPageSize);
        if ($this->m_nCurrentPage > $this->m_nTotalPages)
            $this->m_nCurrentPage = $this->m_nTotalPages;
        $this->m_oRs = new CRecordset($this->BuildSql());

//    echo nl2br($sql);
    }

    function GetCountSQL()
    {
        return strlen($this->m_sCountSQL) ? $this->m_sCountSQL : false;
    }

    function IsTypeAllowed($type)
    {
        if ($this->m_oFile != null)
            return $this->m_oFile->IsTypeAllowed($this->m_oFile, $type);
    }

    function SetErrorMessage($sMsg = "")
    {
        $this->m_sError = $sMsg;
    }

}
?>