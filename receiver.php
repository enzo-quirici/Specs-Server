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
        // Redirect to display_data.php with an error message
        header("Location: display_data.php?status=error&message=Invalid%20JSON%20data");
        exit();
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
                // Redirect to display_data.php with success message
                header("Location: display_data.php?status=success&message=Data%20saved%20successfully.");
                exit();
            } else {
                echo "Error saving data: " . $conn->error;
                // Redirect to display_data.php with an error message
                header("Location: display_data.php?status=error&message=Error%20saving%20data.");
                exit();
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
            // Redirect to display_data.php with an error message
            header("Location: display_data.php?status=error&message=Database%20query%20failed.");
            exit();
        }
    } else {
        echo "Missing required data!";
        // Redirect to display_data.php with an error message
        header("Location: display_data.php?status=error&message=Missing%20required%20data.");
        exit();
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
        // Redirect to display_data.php with an error message
        header("Location: display_data.php?status=error&message=Uploaded%20file%20is%20not%20a%20valid%20JSON%20file.");
        exit();
    }
} else {
    echo "No JSON data provided!";
    // Redirect to display_data.php with an error message
    header("Location: display_data.php?status=error&message=No%20JSON%20data%20provided.");
    exit();
}

// Close the connection
$conn->close();
?>
