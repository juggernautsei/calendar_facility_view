<?php

/**
 *
 *  package   OpenEMR
 *  link      https://affordablecustomehr.como
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__) . "/../vendor/autoload.php";

use Juggernaut\Text\Module\Controllers\MonthlyIncomeDataPoints;
use Juggernaut\Text\Module\Controllers\Database;

use OpenEMR\Core\Header;

$genDatapoints = new MonthlyIncomeDataPoints();
$data = new Database();
$firstInsuranceCompany = $data::firstInsuaranceCompany();

$selectedCompany = $_POST['icompany'];

if (!empty($_POST['icompany'])) {
    $dataPointsToDisplay = $genDatapoints->buildDataPoints($_POST['icompany']);
} else {
    $dataPointsToDisplay = $genDatapoints->buildDataPoints($firstInsuranceCompany['id']);
}

$points = '"Month,Total Deposited\n"' . " +\r";
$points .= $dataPointsToDisplay;

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Graphical Income Report"); ?></title>
    <?php Header::setupHeader(['common', 'dygraphs'])?>
    <link rel="stylesheet" src="/public/assets/modified/dygraphs-2-0-0/dygraph.css" />
</head>
<body>

    <div class="container-lg mt-5">
        <h2 class="m-4"><?php echo xlt('Insurance Monthly Income'); ?></h2>
        <form class="m-4 form" method="post">
                <?php
                        $companies = $data::insuranceCompanies($selectedCompany);
                        echo $companies;
                ?>
            <input type="submit" value="Submit">
        </form>
        <div id="graphdiv">
        </div>
        <div class="mt-3">
            <p><strong><?php

                    echo $firstInsuranceCompany['name'];
                    ?></strong></p>
        </div>
		<div class="mt-5">
            &copy; <?php echo date('Y') . xlt(" Juggernaut Systems Express") ?>
		</div>

    </div>
<script>
    g = new Dygraph(
        document.getElementById("graphdiv"),
        <?php echo substr($points, 0, -2); ?>
    );
</script>

</body>
</html>
