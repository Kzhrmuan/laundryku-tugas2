
<?php
session_start();

if (isset($_GET["lang"])) {
    if ($_GET["lang"] === "id" || $_GET["lang"] === "en") {
        $_SESSION["bahasa"] = $_GET["lang"];
    }
}

header("Location: index.php");
exit;
?>
