<?php
require_once "library/include.php";
require_once "modules/GeoIP/functions.php";

class CUsers extends CList
{

    function InitiliseList()
    {
        global $_GET, $_COOKIE, $g_oConn;

        $this->AddColumn("Country (UN)", 'country', CL_VIEW_GRID);
        $this->AddColumn("World (UN)", 'world', CL_VIEW_GRID);
        $this->AddColumn("Continent (UN)", 'continent', CL_VIEW_GRID);
        $this->AddColumn("Region (UN)", 'region', CL_VIEW_GRID);
        $this->AddColumn("Other Region (UN)", 'other_region', CL_VIEW_GRID);
        $this->AddColumn("Country Code (ISO)", 'country_code', CL_VIEW_GRID);
        $this->AddColumn("World Code", 'world_code', CL_VIEW_GRID);
        $this->AddColumn("Continent Code", 'continent_code', CL_VIEW_GRID);
        $this->AddColumn("Region Code", 'region_code', CL_VIEW_GRID);
        $this->AddColumn("Other Region Code (UN)", 'other_region_code', CL_VIEW_GRID);
        $this->AddColumn("Country Code", 'country_code', CL_VIEW_GRID);
        $this->AddColumn("Developed or Developing", 'developed_developing', CL_VIEW_GRID);
        $this->AddColumn("Least developed country", 'least_developed_country', CL_VIEW_GRID);
        $this->AddColumn("Landlocked developing country", 'landlocked_developing_country', CL_VIEW_GRID);
        $this->AddColumn("Small island developing state", 'small_island_developing_state', CL_VIEW_GRID);
        $this->AddColumn("Transition country", 'transition_country', CL_VIEW_GRID);


        $delim = "";
        if (isset($_GET['f_id']) && intval($_GET['f_id']) > 0) {
            $this->m_sFilter.= $delim . "h.id=" . intval($_GET['f_id']);
            $delim = " and ";
        }
        
        // search query
        $query = Get("f_q");
        $region_ids_string = "";
        $join_region_tables = "";
        if (strlen($query)) {
            $query = str_replace("OR", "__#__", $query);
            $words = explode("__#__", $query);
            // trim array values
            array_walk($words, create_function('&$val', '$val = trim($val);'));
            $sql = "SELECT GROUP_CONCAT(DISTINCT id) FROM country_region WHERE `name` IN ('" . implode("','", $words) . "');";
            $region_ids_string = $g_oConn->GetValue($sql);
            
            $this->m_sFilter.= <<< EOT
{$delim}
(
    cr4.id IN ({$region_ids_string})
    OR cr4.parent_region_id IN ({$region_ids_string})
    OR cr3.parent_region_id IN ({$region_ids_string})
    OR cr2.parent_region_id IN ({$region_ids_string})
    OR cr1.parent_region_id IN ({$region_ids_string})
)
EOT;
            $delim = " and ";

        }

        $this->m_sSelectSQL = "SELECT 
	cr4.name AS country
	,cr1.name AS world
	,cr2.name AS continent
	,cr3.name AS region
	,cr3_other.name AS other_region
	,cr4.iso_country_code AS country_code

	
	,cr1.code AS world_code
	,cr2.code AS continent_code
	,cr3.code AS region_code
	,cr3_other.code AS other_region_code
	,cr4.code AS country_code
	
	,COALESCE(cr1.developed_developing_region, cr2.developed_developing_region, cr3.developed_developing_region, cr4.developed_developing_region) AS developed_developing

	,IF(cr4.least_developed_country, 'Yes', 'No') AS least_developed_country
	,IF(cr4.landlocked_developing_country, 'Yes', 'No') AS landlocked_developing_country
	,IF(cr4.small_island_developing_state, 'Yes', 'No') AS small_island_developing_state
	,IF(cr4.transition_country, 'Yes', 'No') AS transition_country
	
# World
FROM country_region cr1
# Continent
INNER JOIN country_region cr2 ON cr1.id = cr2.parent_region_id AND cr2.other_region = 0
# Region
INNER JOIN country_region cr3 ON cr2.id = cr3.parent_region_id AND cr3.other_region = 0
# Country
INNER JOIN country_region cr4 ON cr3.id = cr4.parent_region_id AND cr4.other_region = 0
# Other Region
LEFT JOIN country_region cr3_other ON cr3.name = cr3_other.name AND cr3_other.other_region = 1
-- WHERE cr1.other_region = 0
";

        $this->m_sCountSQL = "SELECT 
COUNT(*)	
# World
FROM country_region cr1
# Continent
INNER JOIN country_region cr2 ON cr1.id = cr2.parent_region_id AND cr2.other_region = 0
# Region
INNER JOIN country_region cr3 ON cr2.id = cr3.parent_region_id AND cr3.other_region = 0
# Country
INNER JOIN country_region cr4 ON cr3.id = cr4.parent_region_id AND cr4.other_region = 0
# Other Region
LEFT JOIN country_region cr3_other ON cr3.name = cr3_other.name AND cr3_other.other_region = 1";

        $this->m_sTableName = '';
        $this->m_sTitle = "Country/Region";
        $this->m_sActionURL = "countryregion.php";
        $this->m_sOrderBy = "cr4.name";
        $this->m_nOperation = 0;
        $this->m_nPageSize = 250;
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
StartPage('COUNTRYREGION', 'COUNTRYREGION');
if (isset($_GET['action']) && $_GET['action'] == 'edit')
    $list->ShowEditPage();
else {
    ShowMenu('COUNTRYREGION', 'COUNTRYREGION');
    ?>
    <div class="span-20">
        <form method="get" action="countryregion.php">
            <table>
                <tr>
                    <td class="span-7">
                        <label>Query (world, continent, region, country)</label> <br />
                        <input type="text" name="f_q" id="f_q" value="<?php echo Get("f_q") ?>" class="span-7" /> <br />
                        Note: use "OR" to combine search terms
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