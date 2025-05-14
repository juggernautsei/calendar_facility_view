<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// Define navigation structure
$navigation = [
    [
	'url' => '../welcome.php',
	'text' => xlt('Home'),
	'show_on' => ['*'], // Show on all pages
	'hide_on' => ['welcome.php', 'moduleConfig.php'] // Hide on specific pages
    ],
    [
	'url' => 'public/setOutboundMessage.php',
	'text' => xlt('Set Outbound Message'),
	'show_on' => ['*'],
	'hide_on' => ['setOutboundMessage.php']
    ],
    [
	'url' => '#',
	'text' => xlt('Help'),
	'show_on' => ['*'],
	'hide_on' => []
    ]
];

// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);

// Helper function to determine if a nav item should be displayed
function shouldDisplayNavItem($item, $currentPage) {
    // Hide if current page is in hide_on list
    if (in_array($currentPage, $item['hide_on'])) {
	return false;
    }

    // Show if show_on contains '*' or current page
    return in_array('*', $item['show_on']) || in_array($currentPage, $item['show_on']);
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
	    data-target="#navbarNav" aria-controls="navbarNav"
	    aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
	<ul class="navbar-nav">
	    <?php foreach ($navigation as $item): ?>
		<?php if (shouldDisplayNavItem($item, $currentPage)): ?>
		    <li class="nav-item">
			<a class="nav-link" href="<?php echo htmlspecialchars($item['url']); ?>">
			    <?php echo $item['text']; ?>
			</a>
		    </li>
		<?php endif; ?>
	    <?php endforeach; ?>
	</ul>
    </div>
</nav>


