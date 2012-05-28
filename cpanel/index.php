<?
require_once "library/include.php";

$error = "";
$bBadLogin = false;
$currUrlDir = str_replace("index.php", "", $_SERVER['REQUEST_URI']);

if (isset($_POST['action']) && $_POST['action'] == "login") {
    if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
        $error = "Confirmation code is not correct";
        $bBadLogin = true;
    }

    $sql = "select id,rights,blocked from admin where user='" . $_POST['username'] . "' and pass='" . $_POST['password'] . "'";
    $tolog = new CRecordset($sql);
    if ($bBadLogin == false && $tolog->MoveNext()) {
        if (intval($tolog->GetItem('blocked')) <> 0) {
            $error = "Your login is banned. Please contact your site administrator.";
            $sql = "insert into admin_log (date,ip,username,password,status) values (now(),'";
            $sql .= $_SERVER['REMOTE_ADDR'] . "','" . $_POST['username'] . "','" . $_POST['password'] . "',2)";
            $g_oConn->Execute($sql);
        } else {
            $id = $tolog->GetItem('id');
            $rights = intval($tolog->GetItem('rights'));
            $timeout = 1500; // minutes
            $val = isset($_POST['listsize']) ? intval($_POST['listsize']) : 15;
            $_SESSION['rows'] = ($val ? $val : 15);
            $_SESSION['uid'] = $id;
            $_SESSION['rights'] = $rights;
            $sql = "insert into admin_log (date,ip,username,password,status) values (now(),'";
            $sql .= $_SERVER['REMOTE_ADDR'] . "','" . $_POST['username'] . "','" . $_POST['password'] . "',1)";
            $g_oConn->Execute($sql);

            $sql = "update admin set lastloginip='" . $_SERVER['REMOTE_ADDR'] . "', lastlogin=now() where id=" . $id . ";";
            $g_oConn->Execute($sql);
            ?>
            <script>
                window.location='dashboard.php';
            </script>
            <?
            die();
        }
    } else {
        $error = strlen($error) ? $error : "Incorrect login/password.";
        $sql = "insert into admin_log (date,ip,username,password,status) values (now(),'";
        $sql .= $_SERVER['REMOTE_ADDR'] . "','" . $_POST['username'] . "','" . $_POST['password'] . "',0)";
        $g_oConn->Execute($sql);
    }
}

$val = $g_oConn->GetValue("select count(id) from admin");
?>
<html>
    <head>
        <title><?php echo $BRANDING_URL_CAMELCASE ?> Administration</title>

        <!-- Framework CSS -->
        <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
        <!--[if lt IE 10]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection"><![endif]-->
        <link rel="stylesheet" href="css/buttons2.css" type="text/css" media="screen, projection" />
    </head>
    <body onload="document.loginform.username.focus();">
        <div class="container">
            <hr class="space" />

            <div class="span-24">
                <div class="span-4">&nbsp;</div>

                <div class="span-12 caption">
                    <div class="box">
                        <?php echo $BRANDING_URL_CAMELCASE ?> Administration
                    </div>
                    <? if (isset($error) && strlen($error)): ?>
                        <div class="error">
                            <?php echo $error ?>&nbsp;
                        </div>
                    <? endif; ?>

                    <form method="post" name="loginform" action="<?= $_SERVER['PHP_SELF'] ?>" class="box">
                        <input name="action" type="hidden" value="login" />
                        <input name="listsize" type="hidden" value="15" />

                        <p>
                            <label class="span-4">Username:</label>
                            <input type="text" name="username" value="<?php echo Post("username") ?>" class="span-6" />
                        </p>

                        <div class="clear"></div>

                        <p>
                            <label class="span-4">Password:</label>
                            <input type="password" name="password" class="span-6" />
                        </p>

                        <div class="clear"></div>

                        <p>
                            <label class="span-4">Confirmation Code:</label>
                            <img src="library/captcha.php" id="captcha" /><br/>

                            <label class="span-4">&nbsp;</label>
                            <a href="#" onclick="
                                document.getElementById('captcha').src='library/captcha.php?'+Math.random();
                                document.getElementById('captcha-form').focus();"
                               id="change-image">Not readable? Change text.</a>
                        </p>

                        <div class="clear"></div>

                        <p>
                            <label class="span-4">Enter Confirmation Code:</label>
                            <input type="text" name="captcha" id="captcha-form" class="span-6" />
                        </p>

                        <div class="clear"></div>

                        <p>
                            <label class="span-4">&nbsp;</label>
                            <input class="button" type="submit" value="Login" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
<?php $g_oConn->Close(); ?>