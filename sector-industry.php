<?php

require_once dirname(__FILE__) . '/config/database.php';
require_once dirname(__FILE__) . '/lib/libdb.php';
require_once dirname(__FILE__) . '/lib/simple_html_dom.php';
require_once dirname(__FILE__) . '/lib/excel_reader2.php';
require_once dirname(__FILE__) . '/lib/include.php';

echo "\nStarting...\n";

$data = new Spreadsheet_Excel_Reader("files/ICB_Structure.xls", true, 'UTF-8');

#################

$parent_id = 0;
$level = 0;
$level_parent = array();
for ($row = 3; $row <= $data->rowcount(); $row++) {
    for ($col = 1; $col <= $data->colcount(); $col++) {
        // All columns
        $val = trim($data->val($row, $col));

        $name = "";
        $definition = "";

        // first column
        if ($col == 1 && strlen($val))
            $parent_id = 0;

        // names & level
        if ($col != 5 && strlen($val))
            $name = $val;

        // definition
        if ($col == 5 && strlen($val))
            $definition = $val;

        $level = $col;

        // insert
        if ($col != 5 && strlen($name)) {
            $parent_id = intval($g_oConn->GetValue("SELECT id FROM sector_industry WHERE `name` = '" . mysql_real_escape_string($name) . "';"));
            if (!$parent_id) {
                $sql = "INSERT INTO sector_industry (`name`,definition,parent_id,`level`) VALUES (
                  '" . mysql_real_escape_string($name) . "'
                  ,'" . mysql_real_escape_string($definition) . "'
                  ," . (isset($level_parent[$level - 1]) ? $level_parent[$level - 1] : 0) . "
                  ," . intval($level) . "
                );";
                $g_oConn->Execute($sql);
                $parent_id = $g_oConn->GetLastId();
            }

            $level_parent[$level] = $parent_id;
        }

        // update definition
        if ($col == 5 && strlen($definition)) {
            $sql = "UPDATE sector_industry SET 
                definition = '" . mysql_real_escape_string($definition) . "' 
              WHERE
                id = " . intval($parent_id) . ";";
            $g_oConn->Execute($sql);
        }
    }

    // test
//  if ($row == 10)
//    break;

    echo "\rParsed row #: " . $row . "\r";
}

echo "\nDone!";
?>