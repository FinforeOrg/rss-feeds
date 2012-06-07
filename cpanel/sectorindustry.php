<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    private $runningId;

    function InitiliseList()
    {
        global $_GET, $_COOKIE, $g_oConn;

        $this->AddColumn("", 'root', CL_VIEW_GRID);
        $this->AddColumn("Industry", 'Industry', CL_VIEW_GRID);
        $this->AddColumn("Supersector", 'Supersector', CL_VIEW_GRID);
        $this->AddColumn("Sector", 'Sector', CL_VIEW_GRID);
        $this->AddColumn("Subsector", 'Subsector', CL_VIEW_GRID);
        $this->AddColumn("Definition", 'Definition', CL_VIEW_GRID);

        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }

        if (isset($_GET['f_name']) && strlen($_GET['f_name']) > 0) {
            $this->m_sFilter.= $delim . "(
                si1.name LIKE '%" . Get("f_name") . "%' 
                OR si2.name LIKE '%" . Get("f_name") . "%' 
                OR si3.name LIKE '%" . Get("f_name") . "%' 
                OR si4.name LIKE '%" . Get("f_name") . "%')";
            $delim = " and ";
        }

        $this->m_sSelectSQL = "SELECT 
    si1.id
	,si1.name AS 'Industry'
	,si2.name AS 'Supersector'
	,si3.name AS 'Sector'
	,si4.name AS 'Subsector'
	,si4.definition AS 'Definition'
FROM sector_industry si1
INNER JOIN sector_industry si2 ON si1.id = si2.parent_id AND si2.level = 2
INNER JOIN sector_industry si3 ON si2.id = si3.parent_id AND si3.level = 3
INNER JOIN sector_industry si4 ON si3.id = si4.parent_id AND si4.level = 4";

        $this->m_sCountSQL = "SELECT 
COUNT(*)
FROM sector_industry si1
INNER JOIN sector_industry si2 ON si1.id = si2.parent_id AND si2.level = 2
INNER JOIN sector_industry si3 ON si2.id = si3.parent_id AND si3.level = 3
INNER JOIN sector_industry si4 ON si3.id = si4.parent_id AND si4.level = 4";

        $this->m_sTableName = 'sector_industry';
        $this->m_sTitle = "Sector/Industry";
        $this->m_sActionURL = "sectorindustry.php";
        $this->m_sOrderBy = "si1.id";
        $this->m_nOperation = 0;
        $this->m_nPageSize = 120;
    }

    function GetCellEntry($dbname, $value, &$rs, $column)
    {

        if ($dbname == "root") {
            if (!strlen($this->m_sFilter)) {
                if ($this->runningId != $rs->GetItem("id")) {
                    $value = "<img src='images/btnPlusSel.gif' border='0' alt='' title='Top level' />";
                    $this->runningId = $rs->GetItem("id");
                }
                else
                    $value = "&vellip;";
            }
        }

        return $value;
    }

}

$list = new CUsers();
StartPage('SECTORINDUSTRY', 'SECTORINDUSTRY');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('SECTORINDUSTRY', 'SECTORINDUSTRY');
    ?>
    <div class="span-20">
        <form method="get" action="sectorindustry.php">
            <table>
                <tr>
                    <td class="span-7">
                        <label>Query (sector/industry name)</label> <br />
                        <input type="text" name="f_name" id="f_name" value="<?php echo Get("f_name") ?>" class="span-7" /> <br />
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