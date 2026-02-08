<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload JSON</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <div class="image-container">
        <img src="icon/Icon 128x128.png" alt="Icon">
        </div>
            <h1>Upload JSON File</h1>

            <div class="switch-container">
                <label class="switch">
                <input type="checkbox" id="darkModeToggle">
                <span class="slider"></span>
                </label>
                </div>

                <a><b>Toggle Dark Mode</b></a>
            
            <div class="button-container">
                <a href="index.php" class="button-link">
                    <button class="button">Specs</button>
                    </a>
                </div>
            <div class="container">
                <form action="receiver.php" method="POST" enctype="multipart/form-data">
                    <div class="file-upload-container">
                        <label for="jsonFile" class="file-label">Choose a JSON file:</label>
                        <input type="file" name="jsonFile" id="jsonFile" accept="application/json" class="file-input" required>
                    </div>                
                    <div class="button-container">
                        <button class="button" type="submit">Upload</button>
                        <button class="button" type="reset">Reset</button>
                    </div>
                </form>
            </div>
        </body>
        <script src="script.js"></script>
</html>
