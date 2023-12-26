<?php
include("@/header.php");
$paginaname = 'Dashboard';


?>
<!DOCTYPE html>
<!--[if IE 12]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 12]><!--> <html class="no-js"> <!--<![endif]-->
<script src="../assets/js/spinner.js"></script>
  <div class="page-wrapper">
     <div class="page-content">
        <div class="profile-page tx-13">
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="profile-header">
        <div class="cover">
          <div class="gray-shade"></div>
          <figure>
            <img src="../assets/images/Animation.gif" class="img-fluid" alt="profile cover">
          </figure>
          <div class="cover-body d-flex justify-content-between align-items-center">
            <div>
              <img class="profile-pic" src="../assets/images/apple-icon.png" alt="profile">
			    </div>
          </div>
        </div>
        <div class="header-links">
        </div>
      </div>
    </div>
  </div>
  <div class="row profile-body">
    <!-- left wrapper start -->
	<?php
									$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
									$plansql -> execute(array(":id" => $_SESSION['ID']));
									$rowxd = $plansql -> fetch(); 
									$date = date("d/m/Y, h:i a", $rowxd['expire']);

									if (!$user->hasMembership($odb))
									{
									$rowxd['mbt'] = 0;
									$rowxd['concurrents'] = 0;
									$rowxd['name'] = 'No membership';
									$date = 'No membership';
									}
									?>
    <div class="col-12">
      <div class="card rounded">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="card-title mb-2"><span style="color: #6412a3;">Membership</span></h6>
          </div>
		  <label class="tx-40 mb-0 ">User ID:<span style="color: #6412a3;"> <?php echo $_SESSION['ID']; ?></span></label>
          <div class="mt-3">

<?php
                $user_id = $_SESSION['ID'];

                $selectBalanceQuery = "SELECT credits FROM user_balance WHERE user_id = :user_id";
                $stmt = $odb->prepare($selectBalanceQuery);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $balance = $stmt->fetchColumn();
                ?>
      <label class="tx-40 mb-0 ">User Balance:<span style="color: #6412a3;"> $<?php echo number_format($balance, 2); ?></span></label>
          <div class="mt-3">

            <label class="tx-40 mb-0">Plan: <span style="color: #6412a3;"> <?php echo htmlspecialchars($rowxd['name']); ?> <a data-original-title="Upgrade" href="purchase.php" data-toggle="tooltip" title=""><i class="fa fa-chevron-up"></i></a></span></label>
          </div>
          <div class="mt-3">
            <label class="tx-40 mb-0 ">Expired: <span style="color: #6412a3;"> <?php echo $date; ?></span></label>
          </div>
          <div class="mt-3">
            <label class="tx-40 mb-0">Attack Time: <span style="color: #6412a3;"> <?php
											if (!$user->hasMembership($odb))
											{
												echo '<td>No membership</td>';
											} else {
											?>
											<td><?php echo $rowxd['mbt']; ?> seconds</td>
											<?php } ?></span></label>
          </div>
          <div class="mt-3">
            <label class="tx-40  mb-0">Concurrent: <span style="color: #6412a3;"> <?php
											if (!$user->hasMembership($odb))
											{
												echo '<td>No membership</td>';
											} else {
											?>
											<td><?php echo $rowxd['concurrents']; ?></td>
											<?php } ?></span></label>
      </div>
	            <div class="mt-3">
            <label class="tx-40  mb-0">Premium Methods: 					<span style="color: #6412a3;">						<?php
											if ($user->isPremium($odb))
											{
												echo '<td>Yes</td>';
											}
											else
											{
												echo '<td>No</td>';
											}
											?></span></label>
      </div>
     </div>
    </div>
   </div>
  </div>
   </div>
    </div>
  </div>
		 <?php include("@/footer.php"); ?>
	 </div>

<!-- Mirrored from www.nobleui.com/laravel/template/dark/general/profile by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 07 Feb 2020 02:15:16 GMT -->
</html>