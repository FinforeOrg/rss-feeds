<?
# rights - pow(2, xxx)
define('SUPPORT', 1);
define('ADMIN', 2);
define('SALESMAN', 4);

$g_aALLOWEDPAGES = array(
    SUPPORT => array('members.php', 'cities.php')
);

// array that defines system menu
$sysmenu = array(
    'DASHBOARD' => array(
        'name' => "Dashboard",
        'link' => "dashboard.php",
        'rights' => ADMIN | SUPPORT | SALESMAN,
        'submenu' => array()
    ),
//    'SAMPLE' => array(
//        'name' => "Sample",
//        'link' => "sample.php",
//        'rights' => ADMIN,
//        'submenu' => array(
//            'SAMPLE' => array(
//                'name' => "Sample",
//                'link' => "sample.php",
//                'rights' => ADMIN,
//            ),
//        )
//    ),
    'FEEDS' => array(
        'name' => "Feeds",
        'link' => "rssfeeds.php",
        'rights' => ADMIN,
        'submenu' => array(
            'RSSFEEDS' => array(
                'name' => "All Feeds",
                'link' => "rssfeeds.php",
                'rights' => ADMIN,
            ),
            'TWITTERFEEDS' => array(
                'name' => "Twitter Feeds",
                'link' => "twitterfeeds.php",
                'rights' => ADMIN,
            ),
            'MAINCATEGORIES' => array(
                'name' => "Categories",
                'link' => "maincategories.php",
                'rights' => ADMIN,
            ),
            'MAINURLS' => array(
                'name' => "Source URLs",
                'link' => "mainurls.php",
                'rights' => ADMIN,
            ),
            'URLCATEGORIES' => array(
                'name' => "Feed Categories and Tags",
                'link' => "urlcategories.php",
                'rights' => ADMIN,
            ),
        )
    ),
    'COUNTRYREGION' => array(
        'name' => "Country/Region",
        'link' => "countryregion.php",
        'rights' => ADMIN,
        'submenu' => array(
            'COUNTRYREGION' => array(
                'name' => "Country/Region",
                'link' => "countryregion.php",
                'rights' => ADMIN,
            ),
        )
    ),
    'ADMINMANAGEMENT' => array(
        'name' => "Administrators",
        'link' => "admins.php",
        'rights' => ADMIN,
        'submenu' => array(
            'ADMINS' => array(
                'name' => "Admins",
                'link' => "admins.php",
                'rights' => ADMIN,
            ),
            'ADMINSLOG' => array(
                'name' => "Log",
                'link' => "adminslog.php",
                'rights' => ADMIN,
            ),
        )
    ),
    'LOGOUT' => array(
        'name' => "Logout",
        'link' => "logout.php",
        'rights' => ADMIN | SUPPORT | SALESMAN,
        'submenu' => array()
    ),
);

// shows menu using the previously defines menu array
function ShowMenu($menu, $submenu)
{
    global $sysmenu, $_SESSION, $g_oConn;
    ?>
    <div id="main-menu">
        <div id="main-menu-subline"></div>
        <ul class="sf-menu sf-navbar sf-js-enabled sf-shadow" id="sample-menu-4">
            <?php foreach ($sysmenu as $key => $value): ?>
                <?php if (($value['rights'] & $_SESSION['rights']) == $_SESSION['rights']): ?>
                    <li class="<?= $key == $menu ? "current" : "" ?>">
                        <?php if (is_array($sysmenu[$key]['submenu']) && sizeof($sysmenu[$key]['submenu'])): ?>
                            <a href="<?= $value['link'] ?>" class="sf-with-ul"><?= strtoupper($value['name']); ?><span class="sf-sub-indicator"> »</span></a>
                        <?php else: ?>
                            <a href="<?= $value['link'] ?>"><?= strtoupper($value['name']); ?></a>
                        <?php endif; ?>

                        <?php if (is_array($sysmenu[$key]['submenu']) && sizeof($sysmenu[$key]['submenu'])): ?>
                            <ul>
                                <?php foreach ($sysmenu[$key]['submenu'] as $key2 => $value2): ?>
                                    <?php if (($value2['rights'] & $_SESSION['rights']) == $_SESSION['rights']): ?>
                                        <li class="<?= $key2 == $submenu ? "current" : "" ?>"><a href="<?= $value2['link'] ?>"><?= $value2["name"] ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="clear"></div>
    <?php
}

// shows menu using the previously defines menu array
function ShowMenuOLD($menu, $submenu)
{
    global $sysmenu, $_SESSION, $g_oConn;
    ?>
    <div id="memMenuHolder">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td>	
                    <table cellpadding="0" cellspacing="0" border="0" id="memMainMenu">
                        <tr>					
                            <?
                            $width = is_array($sysmenu) && count($sysmenu) ? intval(100 / count($sysmenu)) : 100;
                            if (is_array($sysmenu))
                                $i = 0;
                            $count = count($sysmenu);
                            foreach ($sysmenu as $key => $value) {
                                $i++;
                                if (($value['rights'] & $_SESSION['rights']) == $_SESSION['rights']) {
                                    ?>
                                    <td onclick="parent.location='<?= $value['link'] ?>'" class="<?= $key == $menu ? "tabactive" : "tab" ?>">
                                        <?= strtoupper($value['name']); ?>
                                    </td>
                                    <?
                                    if ($i != $count) {
                                        ?>
                                        <td width="1px" class="tabdelim"></td>
                                        <?
                                    }
                                }
                            }
                            ?>
                        </tr>
                    </table>	
                </td>
            </tr>
        </table>
        <div class="submenu">
            <table align="left" cellpadding="0" cellspacing="0" border="0" id="memSubMenu">	
                <tr>
                    <?
                    $count = count($sysmenu[$menu]['submenu']);
                    $i = 0;
                    if (is_array($sysmenu[$menu]['submenu']))
                        foreach ($sysmenu[$menu]['submenu'] as $key => $value) {
                            if (($value['rights'] & $_SESSION['rights']) == $_SESSION['rights']) {
                                ?>
                                <td height="31px" align="left">
                                    <a href="<?= $value['link'] ?>" class="txtbase <?= $key == $submenu ? "txtgreen" : "txtgrey" ?> txt12"><?= $value["name"] ?></a>
                                </td>			
                                <?
                                if ($count != ++$i) {
                                    ?>
                                    <td class="txtbase lsp algn_c" width="20px" align="left" style="padding:0px 5px;color:#666;">&nbsp;|&nbsp;</td>
                                    <?
                                }
                            }
                        }
                    ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?
}

// shows the page header
function StartPage($menu, $submenu, $onload = false)
{
    global $_SESSION, $sysmenu, $g_oConn, $g_aALLOWEDPAGES;
    global $BRANDING_URL_CAMELCASE;

    if (!isset($_SESSION['uid'])) {
        header("Location: index.php");
        die();
    }

    $parts = explode('/', $_SERVER['SCRIPT_NAME']);
    $currPageName = $parts[count($parts) - 1];

//  echo $sysmenu[$menu]['rights']; die();

    $rights = 0;
    if (isset($sysmenu[$menu]['rights']) && intval($sysmenu[$menu]['rights']))
        $rights = $sysmenu[$menu]['rights'];
    else
        header("Location: index.php");

    if (($rights & $_SESSION['rights']) != $_SESSION['rights']) {
        header("Location: index.php");
        die();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title><?php echo $BRANDING_URL_CAMELCASE ?> Administration</title>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />

            <!-- Framework CSS -->
            <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen, projection" />
            <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
            <!--[if lt IE 10]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection"><![endif]-->
            <link rel="stylesheet" href="css/buttons2.css" type="text/css" media="screen, projection" />

            <!-- Admin CSS -->
            <link rel="stylesheet" href="css/styles.css" type="text/css" media="screen, projection" />
            <link href="css/south-street/jquery-ui-1.8.7.custom.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" media="screen" href="js/superfish-1.4.8/css/superfish.css" /> 
            <link rel="stylesheet" media="screen" href="js/superfish-1.4.8/css/superfish-navbar.css" /> 

            <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
            <script type="text/javascript" src="js/jquery-ui-1.8.7.custom.min.js"></script>
            <script type="text/javascript" src="js/superfish-1.4.8/js/superfish.js"></script>
            <script type="text/javascript" src="js/superfish-1.4.8/js/hoverIntent.js"></script> 
            <script> 
                $(document).ready(function(){ 
                    $("ul.sf-menu").superfish({ 
                        pathClass:  'current' 
                    }); 
                }); 

                function confirmAction(strURL,action)
                {
                    var ischk = false;
                    for (i=0;i<document.FRM_LIST.elements.length;i++) 
                    {
                        var obj = document.FRM_LIST.elements[i];
                        if ('chk' == obj.name.substr(0, 3)) ischk |= obj.checked;
                    }
                    if (!ischk) 
                    {
                        alert('Nothing selected');
                        return false;
                    }
                    if (confirm('Are you sure want to ' + action + ' this item(s)?'))
                    {
                        switch (action)
                        {
                            case 'delete': document.FRM_LIST.ls_action.value = '1'; break;
                            case 'approve': document.FRM_LIST.ls_action.value = '2'; break;
                            case 'reject': document.FRM_LIST.ls_action.value = '3'; break;
                            case 'block': document.FRM_LIST.ls_action.value = '4'; break;
                            case 'unblock': document.FRM_LIST.ls_action.value = '5'; break;
                            case 'mark as read': document.FRM_LIST.ls_action.value = '6'; break;
                            case 'mark as new': document.FRM_LIST.ls_action.value = '7'; break;
                        }
                        document.FRM_LIST.submit();
                        return true;
                    }
                    else return false;
                }

                function checkAll()
                {
                    for (i=0;i<document.FRM_LIST.elements.length;i++) 
                    {
                        var obj = document.FRM_LIST.elements[i];
                        if ('chk' == obj.name.substr(0, 3))
                            obj.checked = document.FRM_LIST.selectall.checked;
                    }
                }
                function popupEdit(strURL,wd,hg) 
                {
                    var strName = 'help';
                    var helpWidth = wd;
                    var helpHeight = hg; 
                    var strFeatures;
                    strFeatures = 'scrollbars=1,' + 'width=' + helpWidth + ',height=' + helpHeight;
                    popupWindow = window.open(strURL, strName, strFeatures);
                }
            </script>
        </head>
        <body<?php echo strlen($onload) > 0 ? ' onload="' . $onload . '"' : "" ?>>
            <div>
                <?php
            }

            function TerminatePage()
            {
                global $g_oConn;
                $g_oConn->Close();
                ?>
            </div>
        </body>
    </html>
    <?php
}
?>