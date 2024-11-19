<?php
// Database parameters
$host = 'localhost';     // Database host
$db = 'specs';           // Database name
$user = 'root';          // Database user
$pass = '';              // Password
$port = 4306;            // Custom MySQL port

// Connect to the database with the specified port
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve all data from the system_specs table
$sql = "SELECT os, Version, cpu, Cores, Threads, gpu, Vram, ram FROM system_specs ORDER BY timestamp DESC";

// Execute the query and fetch the results
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Specifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #ffffff;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td img {
            width: 40px;
            height: 40px;
            vertical-align: middle;
            margin-right: 15px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .info {
            display: inline-block;
            vertical-align: middle;
        }
        .info span {
            display: block;
            text-align: left;
            padding-left: 10px;
        }
    </style>
</head>
<body>

<h2>System Specifications Data</h2>

<?php
if ($result->num_rows > 0) {
    // Display results in an HTML table
    echo "<table>
            <tr>
                <th>OS</th>
                <th>Version</th>
                <th>CPU</th>
                <th>CPU Cores</th>
                <th>CPU Threads</th>
                <th>GPU</th>
                <th>VRAM</th>
                <th>RAM</th>
            </tr>";

    // Loop through the results and display each row
    while ($row = $result->fetch_assoc()) {
        // Get the icon paths
        $osIcon = getOsIconPath($row['os'], $row['Version']);
        $cpuIcon = getCpuIcon($row['cpu']);
        $gpuIcon = getGpuIcon($row['gpu']);
        $ramIcon = getRamIcon();

        echo "<tr>
                <td><img src='$osIcon' alt='OS Icon'><div class='info'><span>" . htmlspecialchars($row['os']) . "</span></div></td>
                <td><div class='info'><span>" . htmlspecialchars($row['Version']) . "</span></div></td>
                <td><img src='$cpuIcon' alt='CPU Icon'><div class='info'><span>" . htmlspecialchars($row['cpu']) . "</span></div></td>
                <td><div class='info'><span>" . htmlspecialchars($row['Cores']) . "</span></div></td>
                <td><div class='info'><span>" . htmlspecialchars($row['Threads']) . "</span></div></td>
                <td><img src='$gpuIcon' alt='GPU Icon'><div class='info'><span>" . htmlspecialchars($row['gpu']) . "</span></div></td>
                <td><div class='info'><span>" . htmlspecialchars($row['Vram']) . " MB</span></div></td>
                <td><img src='$ramIcon' alt='RAM Icon'><div class='info'><span>" . htmlspecialchars($row['ram']) . " MB</span></div></td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No data found!";
}

$conn->close();

// Functions to retrieve icon paths

function getOsIconPath($osName, $osVersion) {
    if (strpos(strtolower($osName), 'win') !== false) {
        if (strpos($osName, '10') !== false) return "icon/Windows 10 128x128.png";
        if (strpos($osName, '11') !== false) return "icon/Windows 11 128x128.png";
        return "icon/Microsoft Windows 128x128.png";
    } elseif (strpos(strtolower($osName), 'mac') !== false) {
        return getMacOsIconPath($osVersion);
    } elseif (strpos(strtolower($osName), 'nux') !== false) {
        // Pass the OS version to getLinuxOsIconPath
        return getLinuxOsIconPath($osVersion);
    } else {
        return "icon/Unknown 128x128.png";
    }
}

function getMacOsIconPath($osVersion) {
    $icons = [
        "10.11" => "icon/Mac OS 10.11 128x128.png",
        "10.12" => "icon/Mac OS 10.12 128x128.png",
        "10.13" => "icon/Mac OS 10.13 128x128.png",
        "10.14" => "icon/Mac OS 10.14 128x128.png",
        "10.15" => "icon/Mac OS 10.15 128x128.png",
        "11" => "icon/Mac OS 11 128x128.png",
        "12" => "icon/Mac OS 12 128x128.png",
        "13" => "icon/Mac OS 13 128x128.png",
        "14" => "icon/Mac OS 14 128x128.png",
        "15" => "icon/Mac OS 15 128x128.png"
    ];
    foreach ($icons as $key => $iconPath) {
        if (strpos($osVersion, $key) !== false) {
            return $iconPath;
        }
    }
    return "icon/Apple Mac OS 128x128.png";
}

function getLinuxOsIconPath($osVersion) {
    // Convert the OS version string to lowercase for case-insensitive matching
    $osVersion = strtolower($osVersion);

    // Check for specific Linux distributions
    if (strpos($osVersion, 'ubuntu') !== false) {
        return "icon/Ubuntu Linux 128x128.png";
    } elseif (strpos($osVersion, 'debian') !== false) {
        return "icon/Debian Linux 128x128.png";
    } elseif (strpos($osVersion, 'fedora') !== false) {
        return "icon/Fedora Linux 128x128.png";
    } elseif (strpos($osVersion, 'gentoo') !== false) {
        return "icon/Gentoo Linux 128x128.png";
    } elseif (strpos($osVersion, 'arch') !== false) {
        return "icon/Arch Linux 128x128.png";
    } elseif (strpos($osVersion, 'pop') !== false) {
        return "icon/POP OS Linux 128x128.png";
    } elseif (strpos($osVersion, 'zorin') !== false) {
        return "icon/Zorin OS Linux 128x128.png";
    } elseif (strpos($osVersion, 'manjaro') !== false) {
        return "icon/Manjaro Linux 128x128.png";
    } elseif (strpos($osVersion, 'elementary') !== false) {
        return "icon/Elementary OS Linux 128x128.png";
    } elseif (strpos($osVersion, 'nix') !== false) {
        return "icon/Nix OS Linux 128x128.png";
    } elseif (strpos($osVersion, 'red') !== false || strpos($osVersion, 'hat') !== false) {
        return "icon/Red Hat Linux 128x128.png";
    } else {
        return "icon/GNU Linux 128x128.png"; // Default for generic Linux
    }
}



function getCpuIcon($cpuInfo) {
    $cpuInfo = strtolower($cpuInfo);
    if (strpos($cpuInfo, 'intel') !== false) {
        return "icon/Intel 128x128.png";
    } elseif (strpos($cpuInfo, 'amd') !== false) {
        return "icon/AMD 128x128.png";
    } elseif (strpos($cpuInfo, 'apple') !== false) {
        return "icon/Apple CPU 128x128.png";
    } elseif (strpos($cpuInfo, 'virtual') !== false || strpos($cpuInfo, 'vmware') !== false) {
        return "icon/Virtual Machine CPU 128x128.png";
    } else {
        return "icon/Unknown CPU 128x128.png";
    }
}

function getGpuIcon($gpuInfo) {
    $gpuInfo = strtolower($gpuInfo);
    if (strpos($gpuInfo, 'nvidia') !== false) {
        return "icon/Nvidia 128x128.png";
    } elseif (strpos($gpuInfo, 'amd') !== false || strpos($gpuInfo, 'radeon') !== false) {
        return "icon/AMD Radeon 128x128.png";
    } elseif (strpos($gpuInfo, 'intel') !== false) {
        if (strpos($gpuInfo, 'arc') !== false) {
            return "icon/Intel ARC 128x128.png";
        } else {
            return "icon/Intel HD Graphics 128x128.png";
        }
    } elseif (strpos($gpuInfo, 'apple') !== false) {
        return "icon/Apple GPU 128x128.png";
    } elseif (strpos($gpuInfo, 'vmware') !== false) {
        return "icon/VMware GPU 128x128.png";
    } elseif (strpos($gpuInfo, 'virtual') !== false) {
        return "icon/Virtual Machine 128x128.png";
    } elseif (strpos($gpuInfo, 'vm') !== false) {
        return "icon/Virtual Machine 128x128.png";
    } else {
        return "icon/Unknown GPU 128x128.png";
    }
}
function getRamIcon() {
    return "icon/RAM 128x128.png";
}
?>
