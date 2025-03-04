<?php
// Verbind met de database
$conn = new mysqli("localhost", "root", "", "natin"); // Pas de gegevens aan

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of het formulier is ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verkrijg de klasnaam uit het formulier
    $className = mysqli_real_escape_string($conn, $_POST['className']);

    // SQL-query om de klas in de database in te voegen
    $sql = "INSERT INTO klas (naam) VALUES ('$className')";

    // Voer de query uit en controleer of deze succesvol was
    if ($conn->query($sql) === TRUE) {
        // Als de klas succesvol is toegevoegd, geef een bericht weer
        echo "Nieuwe klas toegevoegd: " . $className;
    } else {
        // Als er een fout optreedt bij het invoegen
        echo "Fout bij het toevoegen van klas: " . $conn->error;
    }

    // Sluit de verbinding
    $conn->close();

    // Redirect naar de hoofdpagina om het formulier opnieuw te laden
    header("Location: klassen.php"); // Verander 'index.php' naar je eigen bestand als dat anders is
    exit();
}
?>

<form action="add_klas.php" method="POST">
    <label for="className">Klasnaam:</label>
    <input type="text" id="className" name="className" required>
    <button type="submit">Voeg Klas Toe</button>
</form>
