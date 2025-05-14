<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

use OpenEMR\Core\Header;
use Juggernaut\Text\Module\App\Model\NotificationModel;

$page = $_SERVER['PHP_SELF'];
$sec = "20";


$phone = new NotificationModel();
$number = $phone->getPatientCell();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
	  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="<?php echo $sec ?>;URL='<?php echo $page ?>'">
    <title><?php echo xlt('Notifications'); ?></title>
    <?php Header::setupHeader(['common', 'datatables', 'datatables-dt', 'datatables-bs']) ?>
    <script>
	$(function () {
	    $('#notification').DataTable({
		order: [[0, 'desc']],
	    });
	});
    </script>
</head>
<body>
    <div class="container-fluid main-container mx-auto">
	    <div class="mt-3">
		<div style="float: left">
		    <h1><?php echo xlt('Notifications'); ?></h1>
		</div>
		<?php if (!empty($number['phone_cell']) ) { ?>
		<div id="contactpatient" style="float: right">
		    <button id="initiate" class="btn btn-primary"><?php echo xlt('Text Patient'); ?></button>
		</div>
		<?php } elseif(!empty($_SESSION['pid'])) {
		    echo "<div style='float: right'>";
		    echo "<h4 class='text-red'>" . xlt('No Cell Number in chart') . "</h4>";
		    echo "</div>";
		} ?>
	    </div>
	    <div class="mt-3 w-auto">
		<table class="table table-striped" id="notification">
		    <thead>
			<th scope="col"><?php echo xlt('Date'); ?></th>
			<th scope="col"><?php echo xlt('From'); ?></th>
			<th scope="col"><?php echo xlt('Name'); ?></th>
			<th scope="col"><?php echo xlt('Sent By'); ?></th>
            <th scope="col"><?php echo xlt('Status'); ?></th>
			<th scope="col"><?php echo xlt('Message'); ?></th>
			<th scope="col"><?php echo xlt('Reply'); ?></th>
		    </thead>
		    <tbody>
		    <?php
			foreach ($this->params as $item) {
			    print "<tr>";
			    print "<td>";
			    print substr($item['date'], 0,-7);
			    print "</td>";
			    print "<td>";
			    print $item['fromnumber'];
			    print "</td>";
			    print "<td>";
			    print $item['name'];
			    print "</td>";
			    print "<td>";
			    print $item['provider_id'];
			    print "</td>";
                print "<td>";
                print $item['status'];
                print "</td>";
			    print "<td style='width: 600px'>";
			    print $item['text'];
			    print "</td>";
			    print "<td>";
			    $phone = substr($item['fromnumber'], 2);
			    print "<button  class='fas fa-share-square' style='font-size:46px color:blue' onclick='sendReply($phone)'> " . xlt('Reply') . "</button>";
			    print "</td>";
			    print "</tr>";
			}

		    ?>
		    </tbody>
		</table>
	    </div>
    </div>
<script>
    function sendReply(phone) {
	let title = <?php echo xlj("Message Reply"); ?>;
	let url = '../../public/index.php/individuals?phone=' + phone;
	dlgopen(url, '_blank', 800, 600, '', title);
	return false;
    }
    function textActivePatient() {
	let phone = "<?php echo $number['phone_cell'] ?? ''; ?>";
	let title = <?php echo xlj("Initiate Conversation"); ?>;
	let url = '../../public/index.php/individuals?phone=' + phone;

	dlgopen(url, '_blank', 800, 600, '', title);
	return false;
    }
    <?php if (!empty($number['phone_cell']) ) { ?>
    document.getElementById('initiate').addEventListener('click', textActivePatient);
    <?php } ?>
</script>
</body>
<div class="p-5">
<i><?php echo xlt('Juggernaut Systems Express'); ?></i> &copy; 2020 <?php  echo " - " . date('Y'); ?>
</div>
</html>

