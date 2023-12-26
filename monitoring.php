<?php

include("@/header.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}

$paginaname = 'Monitoring';

?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-js"> <!--<![endif]-->

<style>
@media (min-width: 768px) {
	.col-md-6 {
		max-width: 20%;
	}
}
</style>

<script src="../assets/js/spinner.js"></script>
<div class="page-wrapper">
	<div class="page-content">
		<div class="alert alert-fill-primary">
			<span data-feather="server" class="icon-md text-light mr-2"></span>
			<span><?php echo htmlspecialchars($paginaname); ?></span>
		</div>
		<div class="col-lg-12">
			<div class="row">
				<?php
				$newssql = $odb->query("SELECT methods.name, api.slots FROM methods LEFT JOIN api ON methods.name = api.methods");
				while ($row = $newssql->fetch()) {
					$name = $row['name'];
					$slots = $row['slots'];

					// Get attacks count
					$stmt = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `method` = ? AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
					$stmt->execute([
						$name
					]);
					$attacks = $stmt->fetchColumn();

					if ($slots != NULL) {
						$load = round($attacks / $slots * 100, 2);
						$online = true;
						if ($attacks >= $slots) {
							$color = "bg-warning";
							$prefix = "⚠️ ";
							$suffix = " ⚠️";
						} else {
							$color = "bg-success";
							$prefix = "✅ ";
							$suffix = " ✅";
						}
					} else {
						$slots = 0;
						$load = 100;
						$online = false;
						$color = "bg-danger";
						$prefix = "❌ ";
						$suffix = " ❌";
					}
					
					

				?>
					<div class="col-md-6 grid-margin stretch-card">
						<div class="card">
							<div class="card-body">
								<h5 class="text-center text-uppercase mt-3 mb-4"><?php echo $prefix . htmlspecialchars($name) . $suffix; ?></h5>
								<div class="d-flex align-items-center mb-2">
									<i data-feather="monitor" class="icon-md text-light mr-2"></i>
									<p>Running Attacks: <?php echo $attacks . "/" . $slots; ?></p>
								</div>
								<div class="progress">
									<div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $color; ?>" role="progressbar" style="width: <?php echo $load . "%"; ?>;" aria-valuenow="<?php echo $load; ?>" aria-valuemin=" 0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
				<?
				}
				?>
				</ul>
			</div>
		</div>
	</div>
	</body>

</html>