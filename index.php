<?php
include("@/header.php");
$paginaname = 'Dashboard';
?>
<!DOCTYPE html>
<html class="no-js">
<head>
    <script src="../assets/js/spinner.js"></script>
</head>
<body>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="alert alert-fill-primary">
                <span data-feather="home" class="icon-md text-light mr-2"></span>
                <span><?php echo htmlspecialchars($paginaname); ?></span>
            </div>
            <div class="row">
                <div class="col-12 col-xl-12 stretch-card">
                    <div class="row flex-grow">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0">Registred Members</h6>
                                        <div class="dropdown mb-2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h3 class="mb-2"><?php echo $stats->totalUsers($odb); ?></h3>
                                            <div class="d-flex align-items-baseline">
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 60%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0">Servers</h6>
                                        <div class="dropdown mb-2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h3 class="mb-2"><?php echo $stats->serversonline($odb); ?></h3>
                                            <div class="d-flex align-items-baseline">
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 45%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0">Running Attack</h6>
                                        <div class="dropdown mb-2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h3 class="mb-2"><?php echo $stats->runningBoots($odb); ?></h3>
                                            <div class="d-flex align-items-baseline">
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 45%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0">Recently Attack</h6>
                                        <p class="text-muted">in the last 2 weeks</p>
                                        <div class="dropdown mb-2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h3 class="mb-2"><?php echo $stats->recentAttacks($odb); ?></h3>
                                            <div class="d-flex align-items-baseline">
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 50%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">News</h6>
                            <div id="content">
                                <?php
                                $newssql = $odb->query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 2");
                                while ($row = $newssql->fetch()) {
                                    $id = $row['ID'];
                                    $title = $row['title'];
                                    $content = $row['content'];
                                    $autor = $row['author'];
                                    echo '
                                        <div id="content">
                                    <ul class="timeline">
                                    <li class="event"> ' . htmlspecialchars($content) . '</li>                                                      
                                       </ul>
                                    </div>
                                    ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <style type="text/css">
  .row {
  margin-right: -10px;
  margin-left: -10px; 
  }

	.card-box {
		background-color: #82219c;
    border: 2px solid #2c3136;
    background-color: #0c1427;
    border: 2px solid #181f2d;
    padding: 20px;
    margin-bottom: 20px; 
    border-radius:21px 21px 21px 21px;
	}
  .tab-pane{display:none}
  .col-md-12 {
  width:100%
 }
 .m-b-0 {
  margin-bottom: 0 !important; }
  .p-20 {
  padding: 20px; }

  .widget-inline-box {
    border-right: 1px solid #1c1f22!important;
  }
  .widget-inline {
    border-right: 1px solid #1c1f22;
  }
  .widget-inline-box {
    border-right: 1px solid #1c1f22;

  }


  </style>
        <script src="assets/js/plugins/chartjs/Chart.min.js"></script>
<?php
		$onedays = time() - 86400;

		$twodays = time() - 172800;
		$twodays_after = htmlspecialchars($twodays + 86400);

		$threedays = time() - 259200;
		$threedays_after = htmlspecialchars($threedays + 86400);

		$fourdays = time() - 345600;
		$fourdays_after = htmlspecialchars($fourdays + 86400);

		$fivedays = time() - 432000;
		$fivedays_after = htmlspecialchars($fivedays + 86400);

		$sixdays = time() - 518400;
		$sixdays_after = htmlspecialchars($sixdays + 86400);

		$sevendays = time() - 604800;
		$sevendays_after = htmlspecialchars($sevendays + 86400);

        $eightdays = time() - 691200;
		$eightdays_after = htmlspecialchars($eightdays + 86400);
		
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` > :ggd");
		$SQL -> execute(array(":ggd" => $onedays));
		$count_one = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $twodays, ":after" => $twodays_after));
		$count_two = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $threedays, ":after" => $threedays_after));
		$count_three = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $fourdays, ":after" => $fourdays_after));
		$count_four = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $fivedays, ":after" => $fivedays_after));
		$count_five = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $sixdays, ":after" => $sixdays_after));
		$count_six = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $sevendays, ":after" => $sevendays_after));
		$count_seven = $SQL->fetchColumn(0);
		
		$dayone = htmlspecialchars(date('d/m/Y', $onedays));
		$daytwo = htmlspecialchars(date('d/m/Y', $twodays));
		$daythree = htmlspecialchars(date('d/m/Y', $threedays));
		$dayfour = htmlspecialchars(date('d/m/Y', $fourdays));
		$dayfive = htmlspecialchars(date('d/m/Y', $fivedays));
		$daysix = htmlspecialchars(date('d/m/Y', $sixdays));
		$dayseven = htmlspecialchars(date('d/m/Y', $sevendays));

		?>
  <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    loadchart();

}, false);


function loadchart() {
	var ctx = $("#test").get(0).getContext("2d");
	
	var data = {
		labels: ["<?php echo htmlspecialchars($dayseven); ?>", "<?php echo htmlspecialchars($daysix); ?>", "<?php echo htmlspecialchars($dayfive); ?>", "<?php echo htmlspecialchars($dayfour); ?>", "<?php echo htmlspecialchars($daythree); ?>", "<?php echo htmlspecialchars($daytwo); ?>", "<?php echo htmlspecialchars($dayone); ?>"],
		datasets: [
			{
				label: 'Last 7 days attacks',
				fillColor: '#555757',
				strokeColor: '#555757',
				pointColor: '#555757',
				pointStrokeColor: '#fff',
				pointHighlightFill: '#555757',
				pointHighlightStroke: '#555757',
				data: [<?php echo htmlspecialchars($count_seven); ?>, <?php echo htmlspecialchars($count_six); ?>, <?php echo htmlspecialchars($count_five); ?>, <?php echo htmlspecialchars($count_four); ?>, <?php echo htmlspecialchars($count_three); ?>, <?php echo htmlspecialchars($count_two); ?>, <?php echo htmlspecialchars($count_one); ?>]
			}
		]
	}

	var myNewChart = new Chart(ctx).Line(data, {
		scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		scaleFontColor: '#555757',
		scaleFontStyle: '1',
		tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		tooltipCornerRadius: 2,
		maintainAspectRatio: false,
		tooltipTemplate: "<%if (label){%><%=label%> - <%}%><%= value %> Attacks",
		responsive: true
	});
}
</script>
      <!-- google maps api -->
      <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <!-- Gmaps file -->
        <script src="assets/plugins/gmaps/gmaps.min.js"></script>

        <!-- Google map Init -->
        <script src="assets/pages/jquery.gmaps.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="assets/plugins/jvectormap/gdp-data.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-us-aea-en.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-uk-mill-en.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-au-mill.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-us-il-chicago-mill-en.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-ca-lcc.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-de-mill.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-in-mill.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-asia-mill.js"></script>
        <script src="assets/pages/jquery.jvectormap.init.js"></script>

        <!-- Datatable js -->
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/jszip.min.js"></script>
        <script src="assets/plugins/datatables/pdfmake.min.js"></script>
        <script src="assets/plugins/datatables/vfs_fonts.js"></script>
        <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
        <script src="assets/plugins/datatables/buttons.print.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.scroller.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.colVis.js"></script>
        <script src="assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>

        <!-- init -->
        <script src="assets/pages/jquery.datatables.init.js"></script>

	 <?php include("@/footer.php"); ?>
	 <?php include("@/freeplans.php"); ?>
</body>