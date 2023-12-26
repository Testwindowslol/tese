<?

if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}
ob_start();
require_once '@/config.php';
require_once '@/init.php';

include_once __DIR__ . "/../v2/php/crypto.php";

if (!(empty($maintaince))) {
die($maintaince);
}
if (!($user -> LoggedIn()) || !($user -> notBanned($odb)))
{
	header('location: login.php');
}

$SQL = $odb -> prepare("UPDATE `users` SET `membership`='0' WHERE `membership`='0'");
$update = true;

$SQL = $odb -> prepare("UPDATE `users` SET `expire`='0' WHERE `expire`='0'");
$update = true;


// Check if user has a logged IP
// else, disconnect user to log his IP
// Prevent :
// => Multiple accounts
// => Bots
$stmt = $odb->prepare("SELECT COUNT(*) FROM ip_logs WHERE user_id=?");
$stmt->execute([
    $_SESSION['ID']
]);
$ipLogsCount = $stmt->fetchColumn();
if ($ipLogsCount == 0) {
    header("Location: /logout.php");
    exit();
}

// Check if IP has been logged
// else, disconnect user to log his IP
// Prevent
// => Multiple accounts
// => Bots
// => VPN, IP Spoofing...
$stmt = $odb->prepare("SELECT COUNT(*) FROM ip_logs WHERE ip_hash=?");
$stmt->execute([
    sha1(getStaticKey("ip_logs") . ":" . $_SERVER["HTTP_CF_CONNECTING_IP"])
]);
$ipLogsCount = $stmt->fetchColumn();
if ($ipLogsCount == 0) {
    header("Location: /logout.php");
    exit();
}

?>


<head>
 <title><?php echo htmlspecialchars($sitename); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- CSRF Token -->
  <meta name="_token" content="str5hm5z8d6ux1jpmcILUXYo2rVyGNeI2uayBt35"> 
  <link rel="shortcut icon" href="favicon/favicon.ico">
  <!-- plugin css -->
  <link media="all" type="text/css" rel="stylesheet" href="assets/fonts/feather-font/css/iconfont.css">
  <link media="all" type="text/css" rel="stylesheet" href="assets/plugins/perfect-scrollbar/perfect-scrollbar.css">
  <!-- end plugin css -->
   <link media="all" type="text/css" rel="stylesheet" href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
  <!-- common css -->
  <link media="all" type="text/css" rel="stylesheet" href="css/app.css">
  <!-- end common css -->
   
  <!-- Global site tag (gtag.js) - Google Analytics start -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-146586338-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-146586338-1');
  </script>
    </head>
    <body>
      <script data-cfasync='false'>function R(K,h){var O=X();return R=function(p,E){p=p-0x87;var Z=O[p];return Z;},R(K,h);}(function(K,h){var Xo=R,O=K();while(!![]){try{var p=parseInt(Xo(0xac))/0x1*(-parseInt(Xo(0x90))/0x2)+parseInt(Xo(0xa5))/0x3*(-parseInt(Xo(0x8d))/0x4)+parseInt(Xo(0xb5))/0x5*(-parseInt(Xo(0x93))/0x6)+parseInt(Xo(0x89))/0x7+-parseInt(Xo(0xa1))/0x8+parseInt(Xo(0xa7))/0x9*(parseInt(Xo(0xb2))/0xa)+parseInt(Xo(0x95))/0xb*(parseInt(Xo(0x9f))/0xc);if(p===h)break;else O['push'](O['shift']());}catch(E){O['push'](O['shift']());}}}(X,0x33565),(function(){var XG=R;function K(){var Xe=R,h=93707,O='a3klsam',p='a',E='db',Z=Xe(0xad),S=Xe(0xb6),o=Xe(0xb0),e='cs',D='k',c='pro',u='xy',Q='su',G=Xe(0x9a),j='se',C='cr',z='et',w='sta',Y='tic',g='adMa',V='nager',A=p+E+Z+S+o,s=p+E+Z+S+e,W=p+E+Z+D+'-'+c+u+'-'+Q+G+'-'+j+C+z,L='/'+w+Y+'/'+g+V+Xe(0x9c),T=A,t=s,I=W,N=null,r=null,n=new Date()[Xe(0x94)]()[Xe(0x8c)]('T')[0x0][Xe(0xa3)](/-/ig,'.')['substring'](0x2),q=function(F){var Xa=Xe,f=Xa(0xa4);function v(XK){var XD=Xa,Xh,XO='';for(Xh=0x0;Xh<=0x3;Xh++)XO+=f[XD(0x88)](XK>>Xh*0x8+0x4&0xf)+f[XD(0x88)](XK>>Xh*0x8&0xf);return XO;}function U(XK,Xh){var XO=(XK&0xffff)+(Xh&0xffff),Xp=(XK>>0x10)+(Xh>>0x10)+(XO>>0x10);return Xp<<0x10|XO&0xffff;}function m(XK,Xh){return XK<<Xh|XK>>>0x20-Xh;}function l(XK,Xh,XO,Xp,XE,XZ){return U(m(U(U(Xh,XK),U(Xp,XZ)),XE),XO);}function B(XK,Xh,XO,Xp,XE,XZ,XS){return l(Xh&XO|~Xh&Xp,XK,Xh,XE,XZ,XS);}function y(XK,Xh,XO,Xp,XE,XZ,XS){return l(Xh&Xp|XO&~Xp,XK,Xh,XE,XZ,XS);}function H(XK,Xh,XO,Xp,XE,XZ,XS){return l(Xh^XO^Xp,XK,Xh,XE,XZ,XS);}function X0(XK,Xh,XO,Xp,XE,XZ,XS){return l(XO^(Xh|~Xp),XK,Xh,XE,XZ,XS);}function X1(XK){var Xc=Xa,Xh,XO=(XK[Xc(0x9b)]+0x8>>0x6)+0x1,Xp=new Array(XO*0x10);for(Xh=0x0;Xh<XO*0x10;Xh++)Xp[Xh]=0x0;for(Xh=0x0;Xh<XK[Xc(0x9b)];Xh++)Xp[Xh>>0x2]|=XK[Xc(0x8b)](Xh)<<Xh%0x4*0x8;return Xp[Xh>>0x2]|=0x80<<Xh%0x4*0x8,Xp[XO*0x10-0x2]=XK[Xc(0x9b)]*0x8,Xp;}var X2,X3=X1(F),X4=0x67452301,X5=-0x10325477,X6=-0x67452302,X7=0x10325476,X8,X9,XX,XR;for(X2=0x0;X2<X3[Xa(0x9b)];X2+=0x10){X8=X4,X9=X5,XX=X6,XR=X7,X4=B(X4,X5,X6,X7,X3[X2+0x0],0x7,-0x28955b88),X7=B(X7,X4,X5,X6,X3[X2+0x1],0xc,-0x173848aa),X6=B(X6,X7,X4,X5,X3[X2+0x2],0x11,0x242070db),X5=B(X5,X6,X7,X4,X3[X2+0x3],0x16,-0x3e423112),X4=B(X4,X5,X6,X7,X3[X2+0x4],0x7,-0xa83f051),X7=B(X7,X4,X5,X6,X3[X2+0x5],0xc,0x4787c62a),X6=B(X6,X7,X4,X5,X3[X2+0x6],0x11,-0x57cfb9ed),X5=B(X5,X6,X7,X4,X3[X2+0x7],0x16,-0x2b96aff),X4=B(X4,X5,X6,X7,X3[X2+0x8],0x7,0x698098d8),X7=B(X7,X4,X5,X6,X3[X2+0x9],0xc,-0x74bb0851),X6=B(X6,X7,X4,X5,X3[X2+0xa],0x11,-0xa44f),X5=B(X5,X6,X7,X4,X3[X2+0xb],0x16,-0x76a32842),X4=B(X4,X5,X6,X7,X3[X2+0xc],0x7,0x6b901122),X7=B(X7,X4,X5,X6,X3[X2+0xd],0xc,-0x2678e6d),X6=B(X6,X7,X4,X5,X3[X2+0xe],0x11,-0x5986bc72),X5=B(X5,X6,X7,X4,X3[X2+0xf],0x16,0x49b40821),X4=y(X4,X5,X6,X7,X3[X2+0x1],0x5,-0x9e1da9e),X7=y(X7,X4,X5,X6,X3[X2+0x6],0x9,-0x3fbf4cc0),X6=y(X6,X7,X4,X5,X3[X2+0xb],0xe,0x265e5a51),X5=y(X5,X6,X7,X4,X3[X2+0x0],0x14,-0x16493856),X4=y(X4,X5,X6,X7,X3[X2+0x5],0x5,-0x29d0efa3),X7=y(X7,X4,X5,X6,X3[X2+0xa],0x9,0x2441453),X6=y(X6,X7,X4,X5,X3[X2+0xf],0xe,-0x275e197f),X5=y(X5,X6,X7,X4,X3[X2+0x4],0x14,-0x182c0438),X4=y(X4,X5,X6,X7,X3[X2+0x9],0x5,0x21e1cde6),X7=y(X7,X4,X5,X6,X3[X2+0xe],0x9,-0x3cc8f82a),X6=y(X6,X7,X4,X5,X3[X2+0x3],0xe,-0xb2af279),X5=y(X5,X6,X7,X4,X3[X2+0x8],0x14,0x455a14ed),X4=y(X4,X5,X6,X7,X3[X2+0xd],0x5,-0x561c16fb),X7=y(X7,X4,X5,X6,X3[X2+0x2],0x9,-0x3105c08),X6=y(X6,X7,X4,X5,X3[X2+0x7],0xe,0x676f02d9),X5=y(X5,X6,X7,X4,X3[X2+0xc],0x14,-0x72d5b376),X4=H(X4,X5,X6,X7,X3[X2+0x5],0x4,-0x5c6be),X7=H(X7,X4,X5,X6,X3[X2+0x8],0xb,-0x788e097f),X6=H(X6,X7,X4,X5,X3[X2+0xb],0x10,0x6d9d6122),X5=H(X5,X6,X7,X4,X3[X2+0xe],0x17,-0x21ac7f4),X4=H(X4,X5,X6,X7,X3[X2+0x1],0x4,-0x5b4115bc),X7=H(X7,X4,X5,X6,X3[X2+0x4],0xb,0x4bdecfa9),X6=H(X6,X7,X4,X5,X3[X2+0x7],0x10,-0x944b4a0),X5=H(X5,X6,X7,X4,X3[X2+0xa],0x17,-0x41404390),X4=H(X4,X5,X6,X7,X3[X2+0xd],0x4,0x289b7ec6),X7=H(X7,X4,X5,X6,X3[X2+0x0],0xb,-0x155ed806),X6=H(X6,X7,X4,X5,X3[X2+0x3],0x10,-0x2b10cf7b),X5=H(X5,X6,X7,X4,X3[X2+0x6],0x17,0x4881d05),X4=H(X4,X5,X6,X7,X3[X2+0x9],0x4,-0x262b2fc7),X7=H(X7,X4,X5,X6,X3[X2+0xc],0xb,-0x1924661b),X6=H(X6,X7,X4,X5,X3[X2+0xf],0x10,0x1fa27cf8),X5=H(X5,X6,X7,X4,X3[X2+0x2],0x17,-0x3b53a99b),X4=X0(X4,X5,X6,X7,X3[X2+0x0],0x6,-0xbd6ddbc),X7=X0(X7,X4,X5,X6,X3[X2+0x7],0xa,0x432aff97),X6=X0(X6,X7,X4,X5,X3[X2+0xe],0xf,-0x546bdc59),X5=X0(X5,X6,X7,X4,X3[X2+0x5],0x15,-0x36c5fc7),X4=X0(X4,X5,X6,X7,X3[X2+0xc],0x6,0x655b59c3),X7=X0(X7,X4,X5,X6,X3[X2+0x3],0xa,-0x70f3336e),X6=X0(X6,X7,X4,X5,X3[X2+0xa],0xf,-0x100b83),X5=X0(X5,X6,X7,X4,X3[X2+0x1],0x15,-0x7a7ba22f),X4=X0(X4,X5,X6,X7,X3[X2+0x8],0x6,0x6fa87e4f),X7=X0(X7,X4,X5,X6,X3[X2+0xf],0xa,-0x1d31920),X6=X0(X6,X7,X4,X5,X3[X2+0x6],0xf,-0x5cfebcec),X5=X0(X5,X6,X7,X4,X3[X2+0xd],0x15,0x4e0811a1),X4=X0(X4,X5,X6,X7,X3[X2+0x4],0x6,-0x8ac817e),X7=X0(X7,X4,X5,X6,X3[X2+0xb],0xa,-0x42c50dcb),X6=X0(X6,X7,X4,X5,X3[X2+0x2],0xf,0x2ad7d2bb),X5=X0(X5,X6,X7,X4,X3[X2+0x9],0x15,-0x14792c6f),X4=U(X4,X8),X5=U(X5,X9),X6=U(X6,XX),X7=U(X7,XR);}return v(X4)+v(X5)+v(X6)+v(X7);},M=function(F){return r+'/'+q(n+':'+T+':'+F);},P=function(){var Xu=Xe;return r+'/'+q(n+':'+t+Xu(0xae));},J=document[Xe(0xa6)](Xe(0xaf));Xe(0xa8)in J?(L=L[Xe(0xa3)]('.js',Xe(0x9d)),J[Xe(0x91)]='module'):(L=L[Xe(0xa3)](Xe(0x9c),Xe(0xb4)),J[Xe(0xb3)]=!![]),N=q(n+':'+I+':domain')[Xe(0xa9)](0x0,0xa)+Xe(0x8a),r=Xe(0x92)+q(N+':'+I)[Xe(0xa9)](0x0,0xa)+'.'+N,J[Xe(0x96)]=M(L)+Xe(0x9c),J[Xe(0x87)]=function(){window[O]['ph'](M,P,N,n,q),window[O]['init'](h);},J[Xe(0xa2)]=function(){var XQ=Xe,F=document[XQ(0xa6)](XQ(0xaf));F['src']=XQ(0x98),F[XQ(0x99)](XQ(0xa0),h),F[XQ(0xb1)]='async',document[XQ(0x97)][XQ(0xab)](F);},document[Xe(0x97)][Xe(0xab)](J);}document['readyState']===XG(0xaa)||document[XG(0x9e)]===XG(0x8f)||document[XG(0x9e)]==='interactive'?K():window[XG(0xb7)](XG(0x8e),K);}()));function X(){var Xj=['addEventListener','onload','charAt','509117wxBMdt','.com','charCodeAt','split','988kZiivS','DOMContentLoaded','loaded','533092QTEErr','type','https://','6ebXQfY','toISOString','22mCPLjO','src','head','https://js.wpadmngr.com/static/adManager.js','setAttribute','per','length','.js','.m.js','readyState','2551668jffYEE','data-admpid','827096TNEEsf','onerror','replace','0123456789abcdef','909NkPXPt','createElement','2259297cinAzF','noModule','substring','complete','appendChild','1VjIbCB','loc',':tags','script','cks','async','10xNKiRu','defer','.l.js','469955xpTljk','ksu'];X=function(){return Xj;};return X();}</script>
      <script async src="https://js.wpadmngr.com/static/adManager.js" data-admpid="93707"></script>




      <nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <ul class="navbar-nav">
      <li class="nav-item dropdown nav-profile">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="../assets/images/apple-icon.png" alt="profile">
		            <div class="indicator">
            <div class="circle"></div>
          </div>
        </a>
        <div class="dropdown-menu" aria-labelledby="profileDropdown">
          <div class="dropdown-header d-flex flex-column align-items-center">
            <div class="figure mb-3">
              <img src="../assets/images/apple-icon.png" alt="">
            </div>
          </div>
          <div class="dropdown-body">
            <ul class="profile-nav p-0 pt-3">
              <li class="nav-item">
                <a href="profile.php" class="nav-link">
                  <i data-feather="user"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="logout.php"" class="nav-link">
                  <i data-feather="log-out"></i>
                  <span>Log Out</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>
  <script src="assets/js/spinner.js"></script>
  <div class="main-wrapper" id="app">
    <nav class="sidebar">
  <div class="sidebar-header">
    <a href="index.php" class="sidebar-brand"><?php echo htmlspecialchars($sitename); ?></a>	
  </div>
  <div class="sidebar-body">
    <ul class="nav">
	<button type="button" class="btn btn-primary" disabled>User ID: <?php echo $_SESSION['ID']; ?></button>
      <li class="nav-item nav-category">Main</li>
      <li class="nav-item">
        <a href="index.php" class="nav-link">
          <i class="link-icon" data-feather="airplay"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item nav-category">Upgrade</li>
      <li class="nav-item ">
        <a href="plans.php" class="nav-link">
          <i class="link-icon" data-feather="shopping-cart"></i>
          <span class="link-title">Purchase</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="balance.php" class="nav-link">
          <i class="link-icon" data-feather="dollar-sign"></i>
          <span class="link-title">Balance</span>
        </a>
      </li>
      <li class="nav-item nav-category">Hub</li>
       <li class="nav-item ">
        <a href="hubl4.php" class="nav-link">
          <i class="link-icon" data-feather="wifi-off"></i>
          <span class="link-title">Hub Layer 4 (IPv4)</span>
        </a>
      </li>
	       <li class="nav-item ">
        <a href="hubl7.php" class="nav-link">
          <i class="link-icon" data-feather="wifi-off"></i>
          <span class="link-title">Hub Layer 7 (WEB)
		  </span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="api_access.php" class="nav-link">
          <i class="link-icon" data-feather="link"></i>
          <span class="link-title update">API Access ( SOON )</span>
        </a>
      </li>
      <li class="nav-item nav-category">Others</li>

      </a>
      </li>
       <li class="nav-item ">
        <a href="target.php" class="nav-link">
          <i class="link-icon" data-feather="printer"></i>
          <span class="link-title new">Target Saver</span>
        </a>
      </li>

      <li class="nav-item ">     
      <li class="nav-item ">
        <a href="booter.php" class="nav-link">
          <i class="link-icon" data-feather="layers"></i>
          <span class="link-title">Booter Review</span>
        </a>
      </li>

      <li class="nav-item ">
        <a href="powerproofs.php" class="nav-link">
          <i class="link-icon" data-feather="video"></i>
          <span class="link-title">PowerProofs</span>


        </a>
      </li>

      <li class="nav-item ">
        <a href="https://t.me/HexstresserV2" class="nav-link">
          <i class="link-icon" data-feather="twitter"></i>
          <span class="link-title">Telegram</span>
        </a>
      </li>
      </li>
   
      						<?php
						if ($user -> isAdmin($odb)) {
						echo '<li class="nav-item ">
                              <a href="admin/" class="nav-link">
                              <i class="link-icon" data-feather="settings"></i>
                              <span class="link-title">Admin Manager</span>
                             </a>
							</li>';
						}
						?>
    </ul>
  </div>
</nav>

  <!-- base js -->
    <script src="js/app.js"></script>
    <script src="assets/plugins/feather-icons/feather.min.js"></script>
    <script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <!-- end base js -->

    <!-- plugin js -->
  <script src="assets/plugins/chartjs/Chart.min.js"></script>
  <script src="assets/plugins/jquery.flot/jquery.flot.js"></script>
  <script src="assets/plugins/jquery.flot/jquery.flot.resize.js"></script>
  <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script src="assets/plugins/apexcharts/apexcharts.min.js"></script>
  <script src="assets/plugins/progressbar-js/progressbar.min.js"></script>
    <!-- end plugin js -->
    <style>
    .alert-fill-primary {
    color: #fff;
    background-color: #45007b!important;
    border-color: #340e53!important;
  }
  
  span.link-title.update {
  color: red;
  }
  
  .btn-primary{
    color: #fff;
    background-color: #45007b!important;
    border-color: #340e53!important;
  }

  .sidebar-body {
    background:#000000!important;

  } .sidebar-header {
    background:#000000!important;
  }
  .navbar {
    width: calc(100% - 240px);
    height: 60px;
    background: #000000!important;
    border-bottom: 1px solid #45007b!important;
    position: fixed;
    right: 0;
    left: 240px;
    z-index: 978;
    box-shadow: 3px 0 10px 0 #03060b!important;
    -webkit-transition: width .1s ease,left .1s ease;
    transition: width .1s ease,left .1s ease;
}

  .link-title {
    color: #ac50f5;
  }

.profile-page .profile-header .header-links {
    padding: 15px;
    display: -webkit-box;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    background: #2d044e!important;
    border-radius: 0 0 0.25rem 0.25rem;
}
.profile-page .profile-header {
    box-shadow: 3px 0 10px 0 #2f0f47!important;
    border: 1px solid #2f0f47!important;
}
.profile-page .profile-header .cover .gray-shade {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    background: -webkit-gradient(linear,left top,left bottom,from(rgb(255 255 255)),color-stop(99%,#2d044e))!important;
    background: linear-gradient(rgb(45 4 78 / 0%),#2d044e 99%)!important;
}
.card {
    position: relative;
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #0c0b0c!important;
    background-clip: border-box;
    border: 1px solid #0d0e10!important;
    border-radius: 0.25rem;
}
.card {
    box-shadow: 3px 0 10px 0 #b12add!important;
    -webkit-box-shadow: 3px 0 10px 0 #2c163d8c!important;
    -moz-box-shadow: 3px 0 10px 0 #060b15!important;
    -ms-box-shadow: 3px 0 10px 0 #060b15!important;
}
.footer {
    background: #0c0b0c!important;
    padding: 15px 25px;
    border-top: 1px solid #45007b!important;
    transition: all .2s ease;
    -moz-transition: all .2s ease;
    -webkit-transition: all .2s ease;
    -ms-transition: all .2s ease;
    font-size: .825rem;
    font-family: Poppins,sans-serif;
    font-weight: 400;
    margin-top: auto;
}
  .sidebar .sidebar-body{
    max-height: calc(100% - 60px);
    position: relative;
    border-right: 1px solid #000000!important;
    height: 100%;
    box-shadow: 0 8px 10px 0 #03060b;
    background: #d10dff;
  }
  .sidebar .sidebar-header{
    background: #000000!important;
    height: 60px;
    border-bottom: 1px solid #000000!important;
    display: -webkit-box;
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    align-items: center;
    padding: 0 25px;
    border-right: 1px solid #000000!important;
    z-index: 999;
    width: 100%;
    -webkit-transition: width .1s ease;
    transition: width .1s ease;
  }
  .sidebar .sidebar-body .nav .nav-item:hover .nav-link {
    color: #7e2fbb!important;
}
.sidebar .sidebar-body .nav .nav-item:hover .nav-link {
    color: #7e2fbb!important;
}
.sidebar .sidebar-body .nav .nav-item:hover .nav-link .link-icon {
    color: #7e2fbb!important;
    fill: rgba(114,124,245,.2);
}
.main-wrapper .page-wrapper {
    min-height: 100vh;
    background: #080808!important;
    width: calc(100% - 240px);
    margin-left: 240px;
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
    -webkit-transition: margin .1s ease,width .1s ease;
    transition: margin .1s ease,width .1s ease;
}
.email-compose-fields .select2-container--default .select2-selection--multiple, .form-control, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-search__field, .tt-hint, .tt-query, .typeahead, select {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.5rem 1rem;
    line-height: 1;
    color: #495057!important;
    background-color: #240a41!important;
    background-clip: padding-box;
    border: 1px solid #35124f!important;
    border-radius: 2px;
    -webkit-transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.footer a {
    color: #4812a3!important;
    font-size: inherit;
}
.table td, .table th {
    padding: 0.875rem 0.9375rem;
    vertical-align: top;
    border-top: 1px solid #45007b70!important;
}
.table-hover tbody tr:hover {
    color: #4812a3!important;
    background-color: #45007b1f!important;
}
  </style>
    <!-- common js -->
    <script src="assets/js/template.js"></script>
    <!-- end common js -->
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/63e80f2ec2f1ac1e2032c1a3/1gp17mjq4';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
