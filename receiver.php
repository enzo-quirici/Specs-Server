<?php
// Database parameters
include 'database.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the JSON data sent via POST
$inputData = file_get_contents('php://input');

// Display raw data for debugging
echo "Raw data received: " . $inputData . "<br>";

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the data is valid
if ($data === null) {
    echo "Invalid JSON data!<br>";  // Display an error message if the JSON is invalid
} else {
    echo "Data decoded successfully.<br>";

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
            $stmt->bind_param('sssssiis', $os, $cpu, $gpu, $ram, $version, $cores, $threads, $vram); // Data types matched to the columns
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

// Close the connection
$conn->close();
?>
