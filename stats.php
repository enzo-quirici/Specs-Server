<?php
// Include your database connection here
include 'database.php';

// OS distribution query
$osDistribution = $conn->query("SELECT OS, COUNT(*) as count FROM system_specs GROUP BY OS");
$osLabels = [];
$osData = [];
$totalOs = 0;

while ($row = $osDistribution->fetch_assoc()) {
    $osLabels[] = $row['OS'];
    $osData[] = $row['count'];
    $totalOs += $row['count']; // Sum up total for OS percentages
}

// CPU distribution query (AMD, Intel, Apple, Unknown)
$cpuDistribution = $conn->query("SELECT cpu, COUNT(*) as count FROM system_specs GROUP BY cpu");
$cpuCategories = ['AMD' => 0, 'Intel' => 0, 'Apple' => 0, 'Unknown' => 0];

// Classify the CPU data
while ($row = $cpuDistribution->fetch_assoc()) {
    $cpu = $row['cpu'];
    if (stripos($cpu, 'AMD') !== false) {
        $cpuCategories['AMD'] += $row['count'];
    } elseif (stripos($cpu, 'Intel') !== false) {
        $cpuCategories['Intel'] += $row['count'];
    } elseif (stripos($cpu, 'Apple') !== false) {
        $cpuCategories['Apple'] += $row['count'];
    } else {
        $cpuCategories['Unknown'] += $row['count'];
    }
}

$cpuLabels = array_keys($cpuCategories);
$cpuData = array_values($cpuCategories);
$totalCpu = array_sum($cpuData); // Total CPU count
$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_stats.css">
    <title>Specs Statistics</title>
</head>
<body>
    <h1>System Statistics</h1>

    <div class="container">
        <!-- OS Distribution Table -->
        <h2>Operating System Distribution</h2>
        <table>
            <thead>
                <tr>
                    <th>OS</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($osLabels as $index => $label): ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td><?= htmlspecialchars($osData[$index]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- OS Pie Chart -->
        <div id="chart-container">
            <h3>OS Pie Chart</h3>
            <?php
            $cx = 150;  // Center of the circle
            $cy = 150;  // Center of the circle
            $radius = 100;  // Radius of the circle
            $startAngle = 0;  // Starting angle
            $osColors = ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99', '#ff6666'];  // Colors for the slices

            echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';
            
            foreach ($osData as $index => $value) {
                $angle = ($value / $totalOs) * 360;
                $x1 = $cx + $radius * cos(deg2rad($startAngle));
                $y1 = $cy + $radius * sin(deg2rad($startAngle));
                $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
                $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));

                echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $osColors[$index] . '" />';
                $startAngle += $angle;
            }

            echo '</svg>';
            ?>
        </div>

        <!-- Legend for OS -->
        <div class="legend">
            <?php foreach ($osLabels as $index => $label): ?>
                <div style="background-color: <?= $osColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
            <?php endforeach; ?>
        </div>

        <!-- CPU Distribution Table -->
        <h2>CPU Distribution</h2>
        <table>
            <thead>
                <tr>
                    <th>CPU</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cpuLabels as $index => $label): ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td><?= htmlspecialchars($cpuData[$index]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- CPU Pie Chart -->
        <div id="chart-container">
            <h3>CPU Pie Chart</h3>
            <?php
            $startAngle = 0;  // Starting angle for CPU pie chart
            $cpuColors = ['#f50505', '#056df5', '#f5d505', '#189c09'];  // Colors for the CPU slices
            
            echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';
            
            foreach ($cpuData as $index => $value) {
                $angle = ($value / $totalCpu) * 360;
                $x1 = $cx + $radius * cos(deg2rad($startAngle));
                $y1 = $cy + $radius * sin(deg2rad($startAngle));
                $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
                $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));

                echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $cpuColors[$index] . '" />';
                $startAngle += $angle;
            }

            echo '</svg>';
            ?>
        </div>

        <!-- Legend for CPU -->
        <div class="legend">
            <?php foreach ($cpuLabels as $index => $label): ?>
                <div style="background-color: <?= $cpuColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>
