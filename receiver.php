<?php
// Database parameters
include 'database.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle JSON data and insert into database
function handleJsonData($jsonData, $conn) {

    // Decode JSON
    $data = json_decode($jsonData, true);

    if ($data === null) {
        header("Location: display_data.php?status=error&message=Invalid%20JSON%20data");
        exit();
    }

    // Required fields (minimum viable payload)
    if (
        !isset(
            $data['os'],
            $data['version'],
            $data['cpu'],
            $data['gpu'],
            $data['ram'],
            $data['cores'],
            $data['threads'],
            $data['vram']
        )
    ) {
        header("Location: display_data.php?status=error&message=Missing%20required%20data");
        exit();
    }

    // Optional fields (NULL allowed)
    $device      = $data['device']      ?? null;
    $owner       = $data['owner']       ?? null;
    $denomination = $data['denomination'] ?? null;   // ← AJOUTÉ ICI

    // Required fields
    $os      = $data['os'];
    $version = $data['version'];
    $cpu     = $data['cpu'];
    $gpu     = $data['gpu'];

    // Force numeric values + gestion "N/A" ou chaînes non numériques
    $ram     = (int)$data['ram'];
    $cores   = (int)$data['cores'];
    $threads = (int)$data['threads'];
    $vram    = is_numeric($data['vram']) ? (int)$data['vram'] : null;

    // SQL avec le nouveau champ denomination
    $sql = "
        INSERT INTO system_specs
        (device, owner, denomination, os, version, cpu, cores, threads, gpu, vram, ram)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    if ($stmt = $conn->prepare($sql)) {

        /*
         * Types (11 paramètres maintenant) :
         * s = string
         * i = integer
         */
        $stmt->bind_param(
            "ssssssiissi",           // ← un 's' supplémentaire pour denomination
            $device,
            $owner,
            $denomination,           // ← AJOUTÉ ICI
            $os,
            $version,
            $cpu,
            $cores,
            $threads,
            $gpu,
            $vram,
            $ram
        );

        if ($stmt->execute()) {
            header("Location: index.php?status=success&message=Data%20saved%20successfully");
            exit();
        } else {
            header("Location: index.php?status=error&message=Error%20saving%20data: " . $stmt->error);
            exit();
        }

        $stmt->close();

    } else {
        header("Location: index.php?status=error&message=Database%20query%20failed: " . $conn->error);
        exit();
    }
}

// ===== INPUT HANDLING =====
$inputData = file_get_contents('php://input');

if (!empty($inputData)) {
    handleJsonData($inputData, $conn);

} elseif (isset($_FILES['jsonFile']) && $_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {

    if ($_FILES['jsonFile']['type'] === 'application/json') {
        $fileContent = file_get_contents($_FILES['jsonFile']['tmp_name']);
        handleJsonData($fileContent, $conn);
    } else {
        header("Location: index.php?status=error&message=Uploaded%20file%20is%20not%20a%20valid%20JSON%20file");
        exit();
    }

} else {
    header("Location: index.php?status=error&message=No%20JSON%20data%20provided");
    exit();
}

$conn->close();
?>