<?php
include("@/header.php");
$paginaname = 'Hub';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once __DIR__ . "/private/php/SecureJS.php";

?>
<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<script src="../assets/js/spinner.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>

optgroup[label="Free"] {
    color: #ac50f5;
}

optgroup[label="Premium"] {
    color: #ac50f5;
}

optgroup[label="Botnet"] {
    color: #ac50f5;
}

</style>

<?php
$plansql = $odb->prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
$plansql->execute(array(":id" => $_SESSION['ID']));
if ($plansql->rowCount() >= 1) {
	$rowxd = $plansql->fetch();
} else {
	$rowxd = [];
	$rowxd["expire"] = time();
}

$date = date("d/m/Y, h:i a", $rowxd['expire']);
?>
<div class="page-wrapper">
	<div class="page-content">

		<?php
			// 

			//$js = new SecureJS(file_get_contents(__DIR__ . "/private/js/hubl4.min.js"));
			//$js->include();
		?>

		<script type="text/javascript" src="/js.php?filename=hubl4.js&_<?php echo rand(0, 9999); ?>"></script>

		<nav class="page-breadcrumb">
			<div class="row">
				<div class="col-md-12">
					<div id="divall" style="display:inline"></div>
					<div id="div" style="display:inline"></div>
					<div class="alert alert-fill-primary" role="alert">
						<?php
						if (!$user->hasMembership($odb)) {
							echo '<td>WARNING! - You not have an active membership!</td>';
						} else {
							echo '<td>Attack hub available</td>';
						}
						?>
						<img id="image" class="spinner-border spinner-border-sm" role="status" style="display:none" />

					</div>
				</div>
			</div>
		</nav>
		<div class="row flex-grow">
			<div class="col-md-6 grid-margin stretch-card">
				<div class="card">
					<div class="form-group">
						<label class="col-md-3 control-label">Host</label>
						<div class="col-md-12">
							<input type="text" id="host" placeholder="127.0.0.1" class="form-control">
							<span class="help-block">IP Address/Website</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Port</label>
						<div class="col-md-12">
							<input id="port" placeholder="80" class="form-control">
							<span class="help-block">Default: 80</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Time</label>
						<div class="col-md-12">
							<input type="text" id="time" class="form-control">
							<?php
							$plansql = $odb->prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
							$plansql->execute(array(":id" => $_SESSION['ID']));
							$concurentsql = $odb -> prepare("SELECT `concurents` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership`");
							$rowxd = $plansql->fetch();
							if (!$user->hasMembership($odb)) {
								$rowxd = [];
								$rowxd['mbt'] = 0;
								$rowxd['concurrents'] = $concurentsql;
								$rowxd['name'] = 'No membership';
								$date = 'No membership';
							}
							?>
							<span class="help-block">Your max time is <?php echo $rowxd['mbt']; ?> seconds</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Concurrents</label>
						<div class="col-md-12">
							<?php
							$plansql = $odb->prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
							$plansql->execute(array(":id" => $_SESSION['ID']));
							$concurentsql = $odb -> prepare("SELECT `concurents` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership`");
							$rowxd = $plansql->fetch();
							if (!$user->hasMembership($odb)) {
								$rowxd = [];
								$rowxd['mbt'] = 0;
								$rowxd['concurrents'] = 1;// $concurentsql;
								$rowxd['name'] = 'No membership';
								$date = 'No membership';
							}
							?>
							<select class="form-control" id="concurrents" <?php if ($rowxd['concurrents'] <= 1) {echo "disabled";} ?>>
							<?php
							for ($i = 1; $i <= $rowxd['concurrents']; $i++) {
								?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php
							}
							?>
							</select>
							<span class="help-block">Your max concurrents is <?php echo $rowxd['concurrents']; ?> concurrent</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Method</label>
						<div class="col-md-12">
							<select class="form-control" id="method">

								<optgroup label="Free">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '1' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
								</optgroup>

								<optgroup label="Premium">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '2' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										if ($user->isPremium($odb)) {
											$name     = $getInfo['name'];
											$fullname = $getInfo['fullname'];
											echo '<option class="text-warning" value="' . $name . '">' . $fullname . '</option>';
										} else {
											$name     = $getInfo['name'];
											$fullname = $getInfo['fullname'];
											echo '<option class="text-warning" disabled value="' . $name . '">' . $fullname . '</option>';
										}
									}
									?>
								</optgroup>
								<optgroup label="Botnet">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '3' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC))
										if ($user->isPremium($odb)) {
											$name     = $getInfo['name'];
											$fullname = $getInfo['fullname'];
											echo '<option class="text-warning" value="' . $name . '">' . $fullname . '</option>';
										} else {
											$name     = $getInfo['name'];
											$fullname = $getInfo['fullname'];
											echo '<option class="text-warning" disabled value="' . $name . '">' . $fullname . '</option>';
										}
									?>
								</optgroup>

							</select>
						</div>
					</div>
					
					<div class="form-group form-actions">
						<div class="col-xs-2 text-center">
							<button type="button" id="launch" onclick="start()" class="btn btn-effect-ripple btn-sm btn-primary">Launch Attack<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>
                            				<button type="button" id="launch" onclick="window.open('http:\/\/64.226.123.34\/', '_blank');" class="btn btn-effect-ripple btn-sm btn-primary">HEXDSTAT<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>
						</div>
					
					</div>
				
				</div>
			</div>

			<div class="col-md-6 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<span <?php if ($user->isAdmin($odb)) {
									echo 'class="tip" onclick="adminattacks()" title="Click for admin mode" style="cursor:pointer"';
								} ?>><i data-feather="sliders"></i></span> <img id="attacksimage" class="spinner-border text-danger" style="display:none" />
					</div>
					<div style="position: relative; width: auto" class="slimScrollDiv">
						<div id="attacksdiv" style="display:inline-block;width:100%"></div>
					</div>
				</div>
			</div>
		</div>

		<div data-clickadilla-banner="376322"></div>
	</div>

	<?php include("@/footer.php"); ?>

	
	</body>
</html>