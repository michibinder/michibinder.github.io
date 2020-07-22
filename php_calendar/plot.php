<?php

// if (isset($_GET['plotlist'])) {
//     $available_plots = $_GET['plotlist'];
// } else {   
// }

// Get the list of all files with .jpg extension in the directory and safe it in an array named $available_plots
$dir = "../temp_plots/*.png";
$available_plots = glob( $dir );
natsort($available_plots);

// Get index of requested plot by checking arguments passed via url link
if (isset($_GET['index'])) {
    $i = $_GET['index'];
} else {
    // Month of last plot
    $i = 0;
}

$titel = "CORAL";

?>

<html>
	<head>
		<title><?php echo $titel ?></title>
	</head>
	<body>
		<?php 
        echo "<img src='" . $available_plots[$i] . "' />";
		?>
		
	</body>
</html>