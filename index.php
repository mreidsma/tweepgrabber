<?php

function getUserInfo($user_ids) {
	
	$autocode = 1;
		
	$user_api = file_get_contents("http://api.twitter.com/1/users/lookup.json?user_id=" . $user_ids);
	$user_results = json_decode($user_api, true);
	
	foreach ($user_results as $key => $values) {	

		echo '<tr role="row">';
		echo '<td class="username">' . $values['name'] . ' <span><a href="http://twitter.com/' . $values['screen_name'] . '">@' . $values['screen_name'] . '</a></span></td>';

		// Make links clickable in description (code modified from info@zenworks.it at http://php.net/manual/en/function.preg-match-all.php)

		if($values['url'] != NULL) {
			$bio = $values['description'] . " :: " . $values['url'];
		} else {
			$bio = $values['description'];
		}
		$links = preg_match_all('/http:\/\/[a-z0-9A-Z\-\.]+(?(?=[\/])(.*))/', $bio, $match);

		if($links) {
			$all_links = $match[0];
			for ($j=0; $j<$links; $j++) {
				$bio = str_replace($all_links[$j], '<a href="' . $all_links[$j] . '">' . $all_links[$j] . '</a>', $bio);
			}
		}

		echo '<td class="description">' . $bio . '</td>';

		if($autocode) {
			
			if($bio != NULL) { // 
		
				$searches = "librarian|4,library|4,archivist|4,professor|2,undergrad|1,major|1,alum|1,freshman|1,sophomore|1,junior|1,senior|1,studying|1";

				$search = explode(",", $searches);

				$matched = NULL;
	
				foreach($search as $value) {
		
					if(!$matched) {
		
						$data = explode("|", $value);
			
						if(preg_match("/" . $data[0] . "/i", $bio)) {
							echo '<td class="class">' . $data[1] . '</td>';	
							$matched = 1;
						}
					}
				}
		
					if(!$matched) { 
						echo '<td class="class"></td>'; 
					}
					
			} else { // There is no bio, so we cannot determine what category they fit in.
				
				echo '<td class="class">7</td>';
				
			} 
		}
			
		echo '</tr>';
	}
}


?>

<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Twitter Followers</title>

<link rel="stylesheet" type="text/css" href="css/tweepstyles.css" />

</head>

<body>

<header role="banner">	
<h1>TweepGrabber</h1>

<h2>Who follows whom?</h2>
</header>
	
<form name="tweeps" action="" method="get">

<div class="searchbar">	
	
<input class="required" name="user" required type="text" <?php if(isset($_GET["user"])) { echo 'value="' . $_GET['user'] . '"'; } ?> />
<input type="submit" value="Get Followers" />

</div><!-- End .searchbar -->

<?php 

if(isset($_GET["user"])) {
	
	$tweep_user = $_GET["user"];
	
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th class="username" role="columnheader">Follower</th>';
	echo '<th class="description" role="columnheader">Bio</th>';
	echo '<th class="class" role="columnheader">Code</th>';
	echo '</thead>';
	echo '<tbody id="results">';

	$api = file_get_contents('https://api.twitter.com/1/followers/ids.json?screen_name=' . $tweep_user . '&cursor=-1');

	$results = json_decode($api, true); // second parameter converts from an object to an array

	// var_dump($results); // Show the decoded json array

	$i = 1;
	$t = 0;
	$user_ids = "";

	foreach ($results['ids'] as $key => $values) {
	
		if($t > 99) { // Can only grab 100 users at a time through the API.

			getUserInfo($user_ids); // Run the API call 
	
			$t = 0;
			$user_ids = "";
	
 		} else {

			if($user_ids == NULL) {
				
				$user_ids = $values;
				
			} else {
				
				$user_ids = $user_ids . "," . $values;
			}

			$i++; $t++;

		}
	}

	getUserInfo($user_ids); // Grab the last batch

	$t = 0;
	$user_ids = "";
	
	echo '</tbody>';
	echo '</table>';
	
	echo '<h3><a href="http://twitter.com/' . $tweep_user . '">@' . $tweep_user . '</a> has ' . $i . ' followers</h3>';
	
	echo '<div class="copybutton">Select All</div>';
	

	
}

?>

<div class="learnmore">What is this?</div>
	
<div class="content" role="complementary">
	
<p>While doing research on the difference between perception and reality in University Libraries' Twitter followers, <abbr title="Grand Valley State University">GVSU</abbr> Librarians <a href="http://twitter.com/abby_elizabeth">Abby Bedford</a> and <a href="http://twitter.com/mreidsma">Matthew Reidsma</a> needed a quick tool to grab Twitter followers for specific users on one screen. The result is Tweepgrabber. It&#8217;s real power lies in the ability to parse Twitter bios for keywords and automatically code those users into groups. <!--Just click the Settings tab, input your criteria, and enter a Twitter username. Enjoy!--></p>
	
</div>

</form>

<footer>
	
<p class="bylabs">A <a href="http://gvsu.edu/library/labs"><abbr title="Grand Valley State University">GVSU</abbr> Library Labs</a> Joint.</p>
<small>Built using the <a href="http://api.twitter.com">Twitter <abbr title="Application Programming Interface">API</abbr></a> by <a href="http://matthewreidsma.com">Matthew Reidsma</a> for <a href="http://gvsu.edu/library">Grand Valley State University Libraries</a>. <!--Licensed under the <a href="http://www.gnu.org/copyleft/gpl.html"><abbr title="GNU General Public License">GPL</abbr></a>. Source code available on Github.--></small>

</footer>

<script src="js/jquery.min.js"></script>
<script src="js/scripts.js"></script>

</body>
</html>