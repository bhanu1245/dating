<?php

header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Licenses</title>

    <style type="text/css">

        html {
            background-color: #fff;
        }

        body {
            padding: 20px 15px;
        }

        h2, h1 {
            padding-top: 20px;
            display: block;
            font-weight: bold;
            font-size: 16px;
        }

        h3 {
            padding-top: 20px;
            display: block;
            font-weight: normal;
            font-size: 14px;
        }

        p {
            margin-bottom: 20px;
            margin-top: 20px;
            display: block;
        }

        span {
            display: block;
            margin-left: 20px;
            margin-top: 10px;
        }

    </style>

</head>
<body>
<?php

if (file_exists("html/privacy/".$LANG['lang-code'].".inc.php")) {

    include_once("html/privacy/".$LANG['lang-code'].".inc.php");

} else {

    include_once("html/privacy/en.inc.php");
}
?>

</body>
</html>