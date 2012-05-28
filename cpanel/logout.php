<?
require "library/include.php";

// clear sessions
if (isset($_SESSION["order"])) {
    $_SESSION["order"] = "";
    unset($_SESSION["order"]);
}

// clearing cookies
$_SESSION['rows'] = "";
$_SESSION['uid'] = "";
$_SESSION['rights'] = "";

unset($_SESSION['rows']);
unset($_SESSION['uid']);
unset($_SESSION['rights']);
?>
<script>window.location='index.php';</script>