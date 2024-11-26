<?php
// Database parameters
include 'database.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle JSON data and insert into database
function handleJsonData($jsonData, $conn) {
    // Decode the JSON data
    $data = json_decode($jsonData, true);

    // Check if the JSON is valid
    if ($data === null) {
        echo "Invalid JSON data!";
        return;
    }

    // Check if the required fields are present
    if (isset($data['os'], $data['cpu'], $data['gpu'], $data['ram'], $data['version'], $data['cores'], $data['threads'], $data['vram'])) {
        $os = $data['os'];
        $version = $data['version'];
        $cpu = $data['cpu'];
        $gpu = $data['gpu'];
        $ram = $data['ram'];
        $cores = $data['cores'];
        $threads = $data['threads'];
        $vram = $data['vram'];

        // Prepare the query to insert the data into the system_specs table
        $sql = "INSERT INTO system_specs (os, cpu, gpu, ram, version, cores, threads, vram) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Use a prepared statement to prevent SQL injection
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sssssiis', $os, $cpu, $gpu, $ram, $version, $cores, $threads, $vram);
            if ($stmt->execute()) {
                echo "Data saved successfully in the database.";
            } else {
                echo "Error saving data: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Missing required data!";
    }
}

// Check if there is raw JSON data in php://input
$inputData = file_get_contents('php://input');
if (!empty($inputData)) {
    echo "Processing JSON data from raw POST request...<br>";
    handleJsonData($inputData, $conn);
} elseif (isset($_FILES['jsonFile']) && $_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {
    // If no raw JSON, check for uploaded file
    $fileTmpPath = $_FILES['jsonFile']['tmp_name'];
    $fileName = $_FILES['jsonFile']['name'];
    $fileType = $_FILES['jsonFile']['type'];

    // Ensure the uploaded file is a JSON file
    if ($fileType === 'application/json') {
        echo "Processing JSON data from uploaded file...<br>";
        $fileContent = file_get_contents($fileTmpPath);
        handleJsonData($fileContent, $conn);
    } else {
        echo "Uploaded file is not a valid JSON file!";
    }
} else {
    echo "No JSON data provided!";
}

// Close the connection
$conn->close();
?>
