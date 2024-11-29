<?php
// Include your database connection here
include 'database.php';

// OS distribution query
$osDistribution = $conn->query("
    SELECT 
        CASE 
            WHEN OS LIKE 'Windows%' THEN 'Windows' 
            ELSE OS 
        END AS OS, 
        COUNT(*) as count 
    FROM system_specs 
    GROUP BY OS
");
$osLabels = [];
$osData = [];
$totalOs = 0;
$osCounts = [];

while ($row = $osDistribution->fetch_assoc()) {
    $osName = $row['OS'];
    if (stripos($osName, 'Windows') === 0) { // Check if OS starts with "Windows"
        $osName = 'Windows';
    }
    if (!isset($osCounts[$osName])) {
        $osCounts[$osName] = 0;
    }
    $osCounts[$osName] += $row['count']; // Aggregate counts
    $totalOs += $row['count']; // Sum up total for OS percentages
}

// Populate labels and data arrays from aggregated counts
foreach ($osCounts as $os => $count) {
    $osLabels[] = $os;
    $osData[] = $count;
}


// CPU distribution query with added ARM category
$cpuDistribution = $conn->query("SELECT cpu, COUNT(*) as count FROM system_specs GROUP BY cpu");
$cpuCategories = ['AMD' => 0, 'Intel' => 0, 'Apple' => 0, 'ARM' => 0, 'Unknown' => 0];

// Classify the CPU data
while ($row = $cpuDistribution->fetch_assoc()) {
    $cpu = $row['cpu'];
    if (stripos($cpu, 'AMD') !== false) {
        $cpuCategories['AMD'] += $row['count'];
    } elseif (stripos($cpu, 'Intel') !== false) {
        $cpuCategories['Intel'] += $row['count'];
    } elseif (stripos($cpu, 'Apple') !== false) {
        $cpuCategories['Apple'] += $row['count'];
    } elseif (
        stripos($cpu, 'ARM') !== false || 
        stripos($cpu, 'Cortex') !== false ||
        stripos($cpu, 'Nvidia') !== false || 
        stripos($cpu, 'Snapdragon') !== false || 
        stripos($cpu, 'MediaTek') !== false 
    ) {
        $cpuCategories['ARM'] += $row['count'];
    } else {
        $cpuCategories['Unknown'] += $row['count'];
    }
}

$cpuLabels = array_keys($cpuCategories);
$cpuData = array_values($cpuCategories);
$totalCpu = array_sum($cpuData); // Total CPU count


// GPU distribution query with added Apple, VM, and ARM categories
$gpuDistribution = $conn->query("SELECT gpu, COUNT(*) as count FROM system_specs GROUP BY gpu");
$gpuCategories = [
    'NVIDIA' => 0, 
    'AMD' => 0, 
    'Intel' => 0, 
    'Apple' => 0,
    'ARM' => 0, 
    'Virtual GPU' => 0, 
    'Unknown' => 0
];

// Classify the GPU data with additional categories
while ($row = $gpuDistribution->fetch_assoc()) {
    $gpu = $row['gpu'];
    if (stripos($gpu, 'nvidia') !== false) {
        $gpuCategories['NVIDIA'] += $row['count'];
    } elseif (stripos($gpu, 'amd') !== false || stripos($gpu, 'radeon') !== false) {
        $gpuCategories['AMD'] += $row['count'];
    } elseif (stripos($gpu, 'intel') !== false) {
        $gpuCategories['Intel'] += $row['count'];
    } elseif (stripos($gpu, 'apple') !== false) {
        $gpuCategories['Apple'] += $row['count'];
    } elseif (stripos($gpu, 'arm') !== false || stripos($gpu, 'adreno') !== false || stripos($gpu, 'mali') !== false || stripos($gpu, 'helio') !== false) {
        $gpuCategories['ARM'] += $row['count'];
    } elseif (stripos($gpu, 'vm') !== false || stripos($gpu, 'virtual') !== false) {
        $gpuCategories['Virtual GPU'] += $row['count'];
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
    '48GB-64GB' => 0,
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

    <div class="container">

        <div class="image-container">
            <img src="<?= "icon/Icon 128x128.png" ?>" alt="Icon">
        </div>

        <h1>System Statistics</h1>

        <div class="switch-container">
        <label class="switch">
        <input type="checkbox" id="darkModeToggle">
        <span class="slider"></span>
        </label>
        </div>
        
        <a><b>Toogle Dark Mode</b></a>
        
        <div class="button-container">
            <a href="display_data.php" class="button-link">
                <button class="button">Specs</button>
            </a>
        </div>
        
        <!-- OS Distribution Section -->
        <div class="section-container">
            <div class="table-container">
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
            </div>

            <div class="chart-container">
                <h3>OS Pie Chart</h3>
                <?php
                $cx = 150;
                $cy = 150;
                $radius = 100;
                $startAngle = 0;
                $osColors = ['#f5be04', '#9c9995', '#0766f5', '#07a6f5', '#ff0000', '#00d9ff', '#1968c2'];
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
                <div class="legend">
                    <?php foreach ($osLabels as $index => $label): ?>
                        <div style="background-color: <?= $osColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- CPU Distribution Section -->
        <div class="section-container">
            <div class="table-container">
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
            </div>

          <div class="chart-container">
              <h3>CPU Pie Chart</h3>
              <?php
              $startAngle = 0;  // Starting angle for CPU pie chart
              $cpuColors = ['#ff0514', '#0072c9', '#9c9995', '#008fbe', '#767578'];  // Updated colors: Added color for ARM

             // Ensure SVG center and radius are defined
             $cx = 150;  // Center X-coordinate
             $cy = 150;  // Center Y-coordinate
             $radius = 100;  // Circle radius

            echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';

            foreach ($cpuData as $index => $value) {
                $angle = ($value / $totalCpu) * 360;  // Angle for current slice
                $x1 = $cx + $radius * cos(deg2rad($startAngle));
                $y1 = $cy + $radius * sin(deg2rad($startAngle));
                $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
                $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));
                
                // Draw slice
                echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $cpuColors[$index] . '" />';

                // Update starting angle for next slice
                $startAngle += $angle;
            }
        
            echo '</svg>';
            ?>
            <div class="legend">
                <?php foreach ($cpuLabels as $index => $label): ?>
                    <div style="background-color: <?= $cpuColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- GPU Distribution Section -->
    <div class="section-container">
        <div class="table-container">
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
        </div>
    
    <div class="chart-container">
        <h3>GPU Pie Chart</h3>
    <?php
    $cx = 150; // Center X coordinate
    $cy = 150; // Center Y coordinate
    $radius = 100; // Radius of the pie chart
    $startAngle = 0; // Starting angle for the pie chart
    $gpuColors = ['#14a800', '#ff0514', '#0072c9', '#a6a4a2', '#008fbe', '#ff6700', '#767578']; // Added color for ARM category
    
    echo '<svg width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';

    foreach ($gpuData as $index => $value) {
        if ($value == 0) {
            continue; // Skip slices with zero value
        }
        
        $angle = ($value / $totalGpu) * 360;
        $x1 = $cx + $radius * cos(deg2rad($startAngle));
        $y1 = $cy + $radius * sin(deg2rad($startAngle));
        $x2 = $cx + $radius * cos(deg2rad($startAngle + $angle));
        $y2 = $cy + $radius * sin(deg2rad($startAngle + $angle));
        
        // Determine if the arc is greater than 180 degrees
        $largeArcFlag = $angle > 180 ? 1 : 0;

        // Draw the pie slice
        echo '<path d="M ' . $cx . ',' . $cy . 
            ' L ' . $x1 . ',' . $y1 . 
            ' A ' . $radius . ',' . $radius . ' 0 ' . $largeArcFlag . ',1 ' . $x2 . ',' . $y2 . 
            ' Z" fill="' . $gpuColors[$index] . '" />';
        
        // Update the start angle for the next slice
        $startAngle += $angle;
    }
    
    echo '</svg>';
    ?>

            <!-- Legend for GPU -->
            <div class="legend">
                <?php foreach ($gpuLabels as $index => $label): ?>
                    <div style="background-color: <?= $gpuColors[$index] ?>;"><?= htmlspecialchars($label) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- RAM Distribution Section -->
    <div class="section-container">
    <div class="table-container">
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
    </div>

    <!-- RAM Pie Chart -->
    <div class="chart-container">
    <h3>RAM Pie Chart</h3>
    <?php
    $cx = 150;  // Center of the circle
    $cy = 150;  // Center of the circle
    $radius = 100;  // Radius of the circle
    $startAngle = 0;  // Starting angle for RAM pie chart
    $ramColors = [
        '#72a5f7', /* Powder Blue */
        '#2ead0a', /* Green Yellow */
        '#FFD700', /* Gold */
        '#20B2AA', /* Light Sea Green */
        '#32CD32', /* Lime Green */
        '#1E90FF', /* Dodger Blue */
        '#FF8C00', /* Dark Orange */
        '#FF1493', /* Deep Pink */
        '#FF5733', /* Orange Red */
        '#8A2BE2', /* Blue Violet */
        '#767578' /* Gray */
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

    $color = isset($ramColors[$index]) ? $ramColors[$index] : '#000000';
    
    // Create the slice path (using the arc commands)
    echo '<path d="M ' . $cx . ',' . $cy . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ' ' . $radius . ' 0 ' . ($angle > 180 ? 1 : 0) . ' 1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $color . '" />';
    
    // Update the start angle for the next slice
    $startAngle += $angle;
    }
    
    // End SVG output
    echo '</svg>';
    ?>

    <!-- Legend for RAM -->
        <div class="legend">
            <?php foreach ($ramLabels as $index => $label): ?>
                <div style="background-color: <?= $ramColors[$index] ?>;">
                    <?= htmlspecialchars($label) ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
<script src="script.js"></script>
</html>
