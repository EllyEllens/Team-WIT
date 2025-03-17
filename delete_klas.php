<?php
// Verbind met de database
$conn = new mysqli("localhost", "root", "", "natin");

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of er een klas-ID is meegegeven
if (isset($_GET['id'])) {
    $klas_id = $_GET['id'];

    // SQL-query om de klas te verwijderen
    $sql = "DELETE FROM klas WHERE id = $klas_id";

    // Voer de query uit en controleer of deze succesvol was
    if ($conn->query($sql) === TRUE) {
        echo "Klas succesvol verwijderd.";
    } else {
        echo "Fout bij het verwijderen van de klas: " . $conn->error;
    }

    // Redirect naar de hoofdpagina na het verwijderen van de klas
    header("Location: klassen.php"); // Verander 'index.php' naar je eigen bestand als dat anders is
    exit();
} else {
    echo "Geen klas-ID opgegeven.";
}

$conn->close();
?>
