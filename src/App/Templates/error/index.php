<?php
/**
 * Created by PhpStorm.
 * User: Abnet Kebede
 * Date: 2/5/2020
 * Time: 3:52 PM
 */

?>

<html lang="en">
<head>
    <title>Error</title>
    <link rel="stylesheet" href="<?php print "$main_path/css/bootstrap.min.css"; ?>">
    <link rel="stylesheet" href="<?php print "$main_path/css/main.css"; ?>">
</head>
<body>
<br>
<div class="container shadow p-3 mb-5 rounded mycolor-white-level-2">
    <nav class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">
            <strong><i>Line</i></strong> <i>Framework</i>
        </span>
    </nav>
    <br>
    <div class="jumbotron bg-danger text-white">
        <h1 class="display-4">
        <?php
            switch($request["title"]){
                case E_NOTICE:
                    print "Error Notice";
                    break;
                case E_WARNING:
                    print "Error Warning";
                    break;
                case E_ERROR:
                    print "Fatal Error";
                    break;
                default:
                    print $request["title"];
            }
        ?>
        </h1>
        <hr class="my-4">
        <p class="lead">
            <?php print "Error occurred in file ".$request["file"]; ?>
        </p>
    </div>
    <div class="container text_color_red">
        <?php print $request["description"]; ?>
    </div>
</div>

</body>
</html>
