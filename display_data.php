<?php
// Database parameters
$host = 'localhost';
$db = 'specs';
$user = 'root';
$pass = '';
$port = 4306;

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve data from the system_specs table
$sql = "SELECT os, Version, cpu, Cores, Threads, gpu, Vram, ram FROM system_specs ORDER BY timestamp DESC";
$result = $conn->query($sql);

// Functions to retrieve icon paths (same as before)
function getOsIconPath($osName, $osVersion) {
    if (strpos(strtolower($osName), 'win') !== false) {
        if (strpos($osName, '10') !== false) return "icon/Windows 10 128x128.png";
        if (strpos($osName, '11') !== false) return "icon/Windows 11 128x128.png";
        return "icon/Microsoft Windows 128x128.png";
    } elseif (strpos(strtolower($osName), 'mac') !== false) {
        return getMacOsIconPath($osVersion);
    } elseif (strpos(strtolower($osName), 'nux') !== false) {
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
    $osVersion = strtolower($osVersion);
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
        return "icon/GNU Linux 128x128.png";
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
    } elseif (strpos($gpuInfo, 'amd') !== false) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Specifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <div class="image-container">
        <img src="<?= "icon/Icon 128x128.png" ?>" alt="Icon">
        </div>

        <h1>Specs</h1>

        <!-- Search Bar -->
        <div class="search-bar">
        <input type="text" placeholder="Search for specifications..." id="searchInput">
        <select id="categorySelect">
            <option value="all">All</option>
            <option value="os">OS</option>
            <option value="version">Version</option>
            <option value="cpu">CPU</option>
            <option value="cores">Cores</option>
            <option value="threads">Threads</option>
            <option value="gpu">GPU</option>
            <option value="vram">VRAM</option>
            <option value="ram">RAM</option>
        </select>

        <!-- Table -->
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Version</th>
                        <th>CPU</th>
                        <th>Cores</th>
                        <th>Threads</th>
                        <th>GPU</th>
                        <th>VRAM</th>
                        <th>RAM</th>
                    </tr>
                </thead>
                <tbody id="specsTable">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="icon-cell">
                                <img src="<?= getOsIconPath($row['os'], $row['Version']) ?>" alt="OS Icon">
                                <span><?= htmlspecialchars($row['os']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['Version']) ?></td>
                            <td class="icon-cell">
                                <img src="<?= getCpuIcon($row['cpu']) ?>" alt="CPU Icon">
                                <span><?= htmlspecialchars($row['cpu']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['Cores']) ?></td>
                            <td><?= htmlspecialchars($row['Threads']) ?></td>
                            <td class="icon-cell">
                                <img src="<?= getGpuIcon($row['gpu']) ?>" alt="GPU Icon">
                                <span><?= htmlspecialchars($row['gpu']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['Vram']) ?> MB</td>
                            <td class="icon-cell">
                                <img src="<?= getRamIcon() ?>" alt="RAM Icon">
                                <span><?= htmlspecialchars($row['ram']) ?> MB</span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #999;">No data available.</p>
        <?php endif; ?>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const specsTable = document.getElementById('specsTable');
    const rows = specsTable.getElementsByTagName('tr');
            
    // Correspondance des catégories avec les colonnes
    const columnMap = {
        os: 0,
        version: 1,
        cpu: 2,
        cores: 3,
        threads: 4,
        gpu: 5,
        vram: 6,
        ram: 7
    };
    
    // Recherche en fonction de l'entrée et de la catégorie
    searchInput.addEventListener('keyup', () => filterTable());
    categorySelect.addEventListener('change', () => filterTable());
    
    function filterTable() {
        const filter = searchInput.value.toLowerCase();
        const category = categorySelect.value;
    
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;
        
            if (category === "all") {
                // Rechercher dans toutes les colonnes
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
            } else {
                // Rechercher uniquement dans une colonne spécifique
                const columnIndex = columnMap[category];
                if (cells[columnIndex] && cells[columnIndex].textContent.toLowerCase().includes(filter)) {
                    match = true;
                }
            }
        
            rows[i].style.display = match ? '' : 'none';
        }
    }
    </script>
</body>
</html>