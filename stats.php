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

// GPU distribution query with added Apple and VM categories
$gpuDistribution = $conn->query("SELECT gpu, COUNT(*) as count FROM system_specs GROUP BY gpu");
$gpuCategories = ['NVIDIA' => 0, 'AMD' => 0, 'Intel' => 0, 'Apple' => 0, 'GPU VM' => 0, 'Unknown' => 0];

// Classify the GPU data with additional categories
while ($row = $gpuDistribution->fetch_assoc()) {
    $gpu = $row['gpu'];
    if (stripos($gpu, 'NVIDIA') !== false) {
        $gpuCategories['NVIDIA'] += $row['count'];
    } elseif (stripos($gpu, 'AMD') !== false) {
        $gpuCategories['AMD'] += $row['count'];
    } elseif (stripos($gpu, 'Intel') !== false) {
        $gpuCategories['Intel'] += $row['count'];
    } elseif (stripos($gpu, 'Apple') !== false) {
        $gpuCategories['Apple'] += $row['count'];
    } elseif (stripos($gpu, 'vm') !== false || stripos($gpu, 'virtual') !== false) {
        $gpuCategories['GPU VM'] += $row['count'];
    } else {
        $gpuCategories['Unknown'] += $row['count'];
    }
}

$gpuLabels = array_keys($gpuCategories);
$gpuData = array_values($gpuCategories);
$totalGpu = array_sum($gpuData); // Total GPU count

// RAM distribution query (in MB and with ranges)
$ramDistribution = $conn->query("SELECT ram, COUNT(*) as count FROM system_specs GROUP BY ram");

// Define the categories with proper ranges
$ramCategories = [
    '< 4GB' => 0,
    '4GB' => 0,
    '6GB' => 0,
    '8GB' => 0,
    '10GB-12GB' => 0,
    '16GB' => 0,
    '18GB-24GB' => 0,
    '32GB' => 0,
    '48-64GB' => 0,
    '> 64GB' => 0,
    'Unknown' => 0
];

// Classify the RAM data in MB ranges
while ($row = $ramDistribution->fetch_assoc()) {
    $ramMB = (int)$row['ram'];

    // Classify based on the RAM size in MB
    if ($ramMB < 3470) {
        $ramCategories['< 4GB'] += $row['count'];
    } elseif ($ramMB >= 3470 && $ramMB < 4750) {
        $ramCategories['4GB'] += $row['count'];
    } elseif ($ramMB >= 4750 && $ramMB < 7350) {
        $ramCategories['6GB'] += $row['count'];
    } elseif ($ramMB >= 7350 && $ramMB < 9750) {
        $ramCategories['8GB'] += $row['count'];
    } elseif ($ramMB >= 9750 && $ramMB < 13450) {
        $ramCategories['10GB-12GB'] += $row['count'];
    } elseif ($ramMB >= 13450 && $ramMB < 17850) {
        $ramCategories['16GB'] += $row['count'];
    } elseif ($ramMB >= 17850 && $ramMB < 26750) {
        $ramCategories['18GB-24GB'] += $row['count'];
    } elseif ($ramMB >= 26750 && $ramMB < 34750) {
        $ramCategories['32GB'] += $row['count'];
    } elseif ($ramMB >= 34750 && $ramMB < 67950) {
        $ramCategories['48GB-64GB'] += $row['count'];
    } elseif ($ramMB >= 67950) {
        $ramCategories['> 64GB'] += $row['count'];
    } else {
        $ramCategories['Unknown'] += $row['count'];
    }
}

// Prepare the labels and data for the RAM distribution chart
$ramLabels = array_keys($ramCategories);
$ramData = array_values($ramCategories);
$totalRam = array_sum($ramData); // Total RAM count

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
            $osColors = ['#f5c507', '#9c9995', '#07a6f5', '#0766f5', '#0766f5'];  // Colors for the slices

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
            $cpuColors = ['#f50505', '#056df5', '#9c9995', '#189c09'];  // Colors for the CPU slices
            
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

        <!-- GPU Distribution Table -->
        <h2>GPU Distribution</h2>
        <table>
            <thead>
                <tr>
                    <th>GPU</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gpuLabels as $index => $label): ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td><?= htmlspecialchars($gpuData[$index]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- GPU Pie Chart -->
        <div id="chart-container">
            <h3>GPU Pie Chart</h3>
            <?php
            $startAngle = 0;  // Starting angle for GPU pie chart
            $gpuColors = ['#14a800', '#ff0000', '#006eff', '#a6a4a2', '#ff9d00', '#5e5e5c'];  // Colors for the GPU slices
            
            echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';
            
            foreach ($gpuData as $index => $value) {
                $angle = ($value / $totalGpu) * 360;
                $x1 = $cx + $radius * cos(deg2rad($startAngle));
                $y1 = $cy + $radius * sin(deg2rad($startAngle));
                $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
                $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));

                echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $gpuColors[$index] . '" />';
                $startAngle += $angle;
            }

            echo '</svg>';
            ?>
        </div>

        <!-- Legend for GPU -->
        <div class="legend">
            <?php foreach ($gpuLabels as $index => $label): ?>
                <div style="background-color: <?= $gpuColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
            <?php endforeach; ?>
        </div>

<!-- RAM Distribution Table -->
<h2>RAM Distribution</h2>
<table>
    <thead>
        <tr>
            <th>RAM</th>
            <th>Count</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ramLabels as $index => $label): ?>
            <tr>
                <td><?= htmlspecialchars($label) ?></td>
                <td><?= htmlspecialchars($ramData[$index]) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- RAM Pie Chart -->
<div id="chart-container">
    <h3>RAM Pie Chart</h3>
    <?php
    $cx = 150;  // Center of the circle
    $cy = 150;  // Center of the circle
    $radius = 100;  // Radius of the circle
    $startAngle = 0;  // Starting angle for RAM pie chart
    $ramColors = [
        '#ff0011',
        '#FFD700',
        '#ff8000',
        '#1E90FF',
        '#ff00b3',
        '#2bff00',
        '#20B2AA',
        '#FF4500',
        '#2E8B57',
        '#7443f0'  
    ];    
    
    $ramColors = array_slice($ramColors, 0, count($ramData));
    

    $Colors = array_slice($ramColors, 0, count($ramData));
    
    // Start SVG output for RAM Pie Chart
    echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';
    
// Draw each slice based on the data
foreach ($ramData as $index => $value) {
    $angle = ($value / $totalRam) * 360; // Calculate angle for each slice
    
    // Calculate the x and y points for the arc
    $x1 = $cx + $radius * cos(deg2rad($startAngle));
    $y1 = $cy + $radius * sin(deg2rad($startAngle));
    $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
    $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));
    
    // Check calculated values for debugging
    // echo "x1: $x1, y1: $y1, x2: $x2, y2: $y2, angle: $angle <br>";

    // Si l'index n'existe pas dans le tableau des couleurs, attribuer une couleur par défaut
    $color = isset($ramColors[$index]) ? $ramColors[$index] : '#000000';  // Couleur par défaut (noir)
    
    // Create the slice path (using the arc commands)
    echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $color . '" />';
    
    // Update the start angle for the next slice
    $startAngle += $angle;
}
    
    // End SVG output
    echo '</svg>';
    ?>
</div>

    <!-- Legend for RAM -->
        <div class="legend">
            <?php foreach ($ramLabels as $index => $label): ?>
                <div style="background-color: <?= $ramColors[$index] ?>;">
                    <?= htmlspecialchars($label) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
