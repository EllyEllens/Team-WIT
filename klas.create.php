<?php
// Databaseverbinding instellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Functie om een nieuwe klas toe te voegen
function voegKlasToe($conn, $className) {
    $className = $conn->real_escape_string($className);
    $sql = "INSERT INTO klas (naam) VALUES ('$className')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: klassen.php"); // Redirect na succes
        exit();
    } else {
        echo "Fout bij het toevoegen van klas: " . $conn->error;
    }
}

// Controleer of het formulier is ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['className'])) {
    voegKlasToe($conn, $_POST['className']);
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klas Toevoegen</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Voeg je CSS-bestand toe -->
</head>
<body>

    <h1>Nieuwe Klas Toevoegen</h1>

    <form action="add_klas.php" method="POST">
        <label for="className">Klasnaam:</label>
        <input type="text" id="className" name="className" required>
        <button type="submit">Voeg Klas Toe</button>
    </form>

</body>
</html>
