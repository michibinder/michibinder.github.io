<?php
// Set your timezone!!
date_default_timezone_set('Europe/Munich');

// Get the list of all files with .jpg extension in the directory and safe it in an array named $available_plots
chdir(__DIR__);
$dir = "../temp_plots/*.png"; // starts from dir of index.php!!!!
$available_plots = glob( $dir );
natsort($available_plots);

// get date of last plot
$last_plot = basename($available_plots[array_key_last($available_plots)]);
$last_date_string = substr($last_plot, 0,8);
$last_date = date_create_from_format('Ymd', $last_date_string);
$date_of_last = date_format($last_date, 'Y-m-j');

// Generate array with dates of measurements
$plot_dates = array();
$plot_dates_and_time = array();
foreach( $available_plots as $plot ):
    $plot_date_string = substr(basename($plot), 0,8);
    $plot_date = date_create_from_format('Ymd', $plot_date_string);
    $plot_dates[] = date_format($plot_date, 'Y-m-j'); // use this for marking available plots!!

    // with time
    $plot_time_string = substr(basename($plot), 0,13);
    $plot_time = date_create_from_format('Ymd-Hi', $plot_time_string);
    $plot_dates_and_time[] = date_format($plot_time, 'Y-m-j H:i'); // use this for marking available plots!!
endforeach;

// Get prev & next month by checking arguments passed via url link
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // Month of last plot
    $ym = date_format($last_date, 'Y-m');
}

// Check format - otherwise use current date
$timestamp = strtotime($ym . '-01');  // the first day of the month
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}
        
// Today (Format:2018-08-8)
$today = date('Y-m-j');

// Title (Format:August, 2018)
$title = date('F, Y', $timestamp);

// Create prev & next month link
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

// Number of days in the month
$day_count = date('t', $timestamp);

// 1:Mon 2:Tue 3: Wed ... 7:Sun
$str = date('N', $timestamp);

// Array for calendar
$weeks = [];
$week = '';

// Add empty cell(s)
$week .= str_repeat('<td></td>', $str - 1);

for ($day = 1; $day <= $day_count; $day++, $str++) {

    $date = $ym . '-' . $day;

    // Start cell with class for background color
    if ($today == $date):
        $week .= '<td class="today">';
    elseif ($date_of_last == $date):
        $week .= '<td class="date_of_last">';
    else:
        $week .= '<td>';
    endif;

    // Add number of day to cell
    $week .= $day;

    // Add event if plot_date
    if (in_array($date, $plot_dates)) {
        $keys = array_keys($plot_dates, $date);
        foreach( $keys as $i):
            $week .= '<br/> <a href="plot.php?index=' . $i . '" class="btn btn-secondary">' . substr($plot_dates_and_time[$i],-5) . '</a>';
            // $week .= '<br/> <a href="plot.php?plotlist=' . $available_plots . '&index=' . $i . '" class="plot_date">' . $plot_dates_and_time[$i] . '</a>';
        endforeach;
    }

    // Finish cell
    $week .= '</td>';

    // Sunday OR last day of the month
    if ($str % 7 == 0 || $day == $day_count) {

        // last day of the month
        if ($day == $day_count && $str % 7 != 0) {
            // Add empty cell(s)
            $week .= str_repeat('<td></td>', 7 - $str % 7);
        }

        $weeks[] = '<tr>' . $week . '</tr>';

        $week = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHP Calendar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <style>
        .container {
            font-family: 'Montserrat', sans-serif;
            margin: 60px auto;
        }
        .list-inline {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-weight: bold;
            font-size: 26px;
            vertical-align: middle;
        }
        th {
            text-align: center;
        }
        td {
            height: 100px;
        }
        th:nth-of-type(6), td:nth-of-type(6) {
            color: blue;
        }
        th:nth-of-type(7), td:nth-of-type(7) {
            color: red;
        }
        .today {
            background-color: #E8DAEF;
        }

        .date_of_last {
            background-color: red;
        }

        .plot_date {
            background-color: #E8DAEF; 
        }
    </style>
</head>
<body>
    <!--<?php echo $date_of_last; ?>-->
    <div class="container">
        <ul class="list-inline">
            <li class="list-inline-item"><a href="?ym=<?= $prev; ?>" class="btn btn-dark">&lt; prev</a></li>
            <li class="list-inline-item"><span class="title"><?= $title; ?></span></li>
            <li class="list-inline-item"><a href="?ym=<?= $next; ?>" class="btn btn-dark">next &gt;</a></li>
        </ul>
        <p class="text-right"><a href="calendar.php" class="btn btn-dark">Latest Measurement</a></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>M</th>
                    <th>T</th>
                    <th>W</th>
                    <th>T</th>
                    <th>F</th>
                    <th>S</th>
                    <th>S</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($weeks as $week) {
                        echo $week;
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>