<?php

use Joomla\Statistics\Aspect;
use Joomla\Statistics\Model;
use Joomla\Statistics\Renderer;

require_once __DIR__ . '/Aspect.php';
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Renderer.php';

$dbtype   = 'mysql';
$host     = 'mysql';
$user     = 'wepschmiede';
$password = 'toh0ohMeen7U';
$db       = 'crm_new';

$month = $_GET['m'];
$year  = $_GET['y'];
$date = date('F Y', mktime(0, 0, 0, $month, 1, $year));

$model    = new Model(new PDO("$dbtype:dbname=$db;host=$host", $user, $password));
$renderer = new Renderer;

$aspect  = new Aspect(Aspect::REPORTS);
$reports = $model->getDistribution($aspect, $year, $month);
$reports = $reports[0]['count'];

$aspect = new Aspect(Aspect::CMS_VERSION);
$chart1 = $renderer->renderDistribution($aspect, $model->getDistribution($aspect, $year, $month));

$aspect = new Aspect(Aspect::PHP_VERSION);
$chart2 = $renderer->renderDistribution($aspect, $model->getDistribution($aspect, $year, $month));

$aspect = new Aspect(Aspect::PLATFORM);
$chart3 = $renderer->renderDistribution($aspect, $model->getDistribution($aspect, $year, $month));

$aspect = new Aspect(Aspect::DB_TYPE);
$chart4 = $renderer->renderDistribution($aspect, $model->getDistribution($aspect, $year, $month));
?>
<html lang="en-GB">
<head>
	<title>Chart</title>
	<script src="http://localhost:8080/JoomlaStats/assets/Chart.js"></script>
	<style>
		h3 {
			text-align: center;
		}

		div.chart {
			width: 240px;
			display: inline-block;
		}
	</style>
</head>
<body>
<h2>Statistics for <?php echo $date; ?></h2>
<div class="chart">
	<h3>CMS Versions</h3>
	<?php echo $chart1; ?>
</div>
<div class="chart">
	<h3>PHP Versions</h3>
	<?php echo $chart2; ?>
</div>
<div class="chart">
	<h3>Platforms</h3>
	<?php echo $chart3; ?>
</div>
<div class="chart">
	<h3>Database Brands</h3>
	<?php echo $chart4; ?>
</div>
<div>based on <?php echo $reports; ?> reports.</div>
</body>
</html>
