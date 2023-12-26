<?php
include("@/header.php");
$paginaname = 'PowerProof';
?>

<!DOCTYPE html>
<html>
<script src="../assets/js/spinner.js"></script>

<div class="page-wrapper">
	<div class="page-content">
		<style type="text/css">
			.video-container {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
			}

			video {
				margin: 10px;
				border-radius: 10px;
				max-width: 30%;
				height: auto;
			}
		</style>
	</head>

	<body>
		<h1>&#8195 &#8195 &#8195 &#8195 &#8195 &#8195 &#8195 &#8195 &#8195 &#8195Free - L4 & L7</h1>
		<h1>&#8195</h1>
		<div class="video-container">

			<?php
				echo "<br>";
				// Change the video source URLs to your own video files
				$video_urls = array(
					"https://cdn.discordapp.com/attachments/939011066065735711/1104063182005338192/sans-titre_gxebHC1M.mp4",
					"https://cdn.discordapp.com/attachments/939011066065735711/1104063252821966888/sans-titre-2-1-trim_Ktha4Ehb.mp4",
					"./video/1.mp4",
					"./video/2.mp4",
					"https://cdn.discordapp.com/attachments/939011066065735711/1104411459426988062/0506_MDyQCYnV.mp4",
					"./video/Design sans titre.mp4");


				// Loop through each video URL
				
				foreach ($video_urls as $video_url) {
					// Create the video element with default quality
					echo '<video width="3840" height="2160" controls>';
					echo '<source src="' . $video_url . '" type="video/mp4">';
					echo 'Your device does not support the video.';
					echo '</video>';

				}
			?>
		</div>

		<script>
			// Function to change the quality of the video
			function changeQuality(select) {
				// Get the selected quality value
				var selected_quality = select.value;

				// Get the video element that is a sibling of the quality selector
				var video = select.previousSibling;

				// Set the new width and height attributes of the video element
				video.setAttribute("width", selected_quality.split("x")[0]);
				video.setAttribute("height", selected_quality.split("x")[1]);
			}

			function changeQuality(quality) {
  				var videos = document.getElementsByTagName("video");
  				for (var i = 0; i < videos.length; i++) {
    				  var video = videos[i];
    				  var sources = video.getElementsByTagName("source");
    				  var sourceUrl = "";
    				  for (var j = 0; j < sources.length; j++) {
      				    var source = sources[j];
      				    if (source.type == "video/mp4") {
        		              sourceUrl = source.src;
        			      break;
      				    }
    				  }
    				  var newSourceUrl = "";
    				  if (quality == "144p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_144p.mp4");
    				  } else if (quality == "240p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_240p.mp4");
    				  } else if (quality == "320p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_320p.mp4");
    				  } else if (quality == "720p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_720p.mp4");
    				  } else if (quality == "1080p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_1080p.mp4");
    				  } else if (quality == "2160p") {
      				    newSourceUrl = sourceUrl.replace(".mp4", "_2160p.mp4");
    				  }
    				  if (newSourceUrl != "") {
      				    video.pause();
      				    video.innerHTML = '<source src="' + newSourceUrl + '" type="video/mp4">';
      				    video.load();
      				    video.play();
    				  }
  				}
			      }
		</script>

	</body>
</html>