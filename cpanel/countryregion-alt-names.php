<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE, $g_oConn;

        $this->AddComboColumn(
                "Country (UN)"
                , "country_region_code"
                , "country_name"
                , "SELECT code AS `value`, `name` AS `text` FROM country_region ORDER BY name ASC"
                , CT_COMBO, CL_VIEW_GRID | CL_VIEW_EDIT, false
        );


        $this->AddColumn("Country Code (ISO)", 'country_region_code', CL_VIEW_GRID);
        $this->AddColumn("Alternative Name", 'name', CL_VIEW_GRID | CL_VIEW_EDIT);

        $delim = "";
        if (isset($_GET['f_code']) && intval($_GET['f_code']) > 0) {
            $this->m_sFilter.= $delim . "can.country_region_code=" . intval($_GET['f_code']);
            $delim = " and ";
        }

        if (isset($_GET['f_alt_name']) && strlen($_GET['f_alt_name']) > 0) {
            $this->m_sFilter.= $delim . "can.name LIKE '%" . $_GET['f_alt_name'] . "%'";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "SELECT
	can.id
	,c.name AS country_name
	,can.country_region_code
	,can.name
FROM country_region_alt_names can
INNER JOIN country_region c ON c.code = can.country_region_code";

        $this->m_sCountSQL = "SELECT 
COUNT(*)	
FROM country_region_alt_names can
INNER JOIN country_region c ON c.code = can.country_region_code";

        $this->m_sTableName = 'country_region_alt_names';
        $this->m_sTitle = "Country/Region Alternative Names";
        $this->m_sActionURL = "countryregion-alt-names.php";
        $this->m_sOrderBy = "c.name,can.name";
        $this->m_iWidth = 700;
//        $this->m_nOperation = 0;
        $this->m_nPageSize = 30;
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
StartPage('COUNTRYREGION', 'COUNTRYREGIONALT');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('COUNTRYREGION', 'COUNTRYREGIONALT');
    ?>
    <div class="span-20">
        <form method="get" action="countryregion-alt-names.php">
            <table>
                <tr>
                    <td>
                        <label>Country</label> <br />
                        <select name="f_code">
                            <option value="-1">Any</option>
    <?php FillCombo("SELECT code AS `value`, `name` AS `text` FROM country_region ORDER BY `name`;", intval(Get("f_code"))); ?>
                        </select>
                    </td>
                    <td>
                        <label>Alternative Name</label> <br />
                        <input type="text" class="span-7" name="f_alt_name" value="<?= isset($_GET['f_alt_name']) ? $_GET['f_alt_name'] : "" ?>" />
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