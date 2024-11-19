<?php
// Paramètres de la base de données
$host = 'localhost';
$db = 'specs'; // Nom de la base de données
$user = 'root'; // Utilisateur de la base de données
$pass = '';     // Mot de passe
$port = 4306;   // Port personnalisé

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $db, $port);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lire les données JSON envoyées via POST
$inputData = file_get_contents('php://input');

// Afficher les données brutes pour déboguer
echo "Raw data received: " . $inputData . "<br>";

// Décoder les données JSON
$data = json_decode($inputData, true);

// Vérifier si les données sont valides
if ($data === null) {
    echo "Invalid JSON data!<br>";  // Afficher un message d'erreur si le JSON est invalide
} else {
    echo "Data decoded successfully.<br>";

    // Vérifier que les champs requis sont présents
    if (isset($data['os'], $data['cpu'], $data['gpu'], $data['ram'], $data['version'], $data['cores'], $data['threads'], $data['vram'])) {
        $os = $data['os'];
        $version = $data['version'];
        $cpu = $data['cpu'];
        $gpu = $data['gpu'];
        $ram = $data['ram'];
        $cores = $data['cores'];
        $threads = $data['threads'];
        $vram = $data['vram'];

        // Préparer la requête pour insérer les données dans la table system_specs
        $sql = "INSERT INTO system_specs (os, cpu, gpu, ram, version, cores, threads, vram) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Utiliser une requête préparée pour éviter les injections SQL
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sssssiis', $os, $cpu, $gpu, $ram, $version, $cores, $threads, $vram); // Types adaptés aux colonnes
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

// Fermer la connexion
$conn->close();
?>
