<?php
// Verbinding maken met de database
$servername = "localhost";
$username = "root"; // Gebruikersnaam van de database
$password = ""; // Wachtwoord van de database (leeg als je geen wachtwoord hebt)
$dbname = "wit"; // Naam van de database

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Als de token niet is ingesteld in de URL, geef dan een foutmelding
if (!isset($_GET['token'])) {
    die('Geen token gevonden. Probeer het opnieuw.');
}

// Verkrijg de token uit de URL
$resetToken = $_GET['token'];

// Zoek de gebruiker die deze token heeft in de Personen en Studenten tabellen
$sqlPersonen = "SELECT * FROM Personen WHERE reset_token = ?";
$stmtPersonen = $conn->prepare($sqlPersonen);
$stmtPersonen->bind_param("s", $resetToken);
$stmtPersonen->execute();
$resultPersonen = $stmtPersonen->get_result();

$sqlStudenten = "SELECT * FROM Studenten WHERE reset_token = ?";
$stmtStudenten = $conn->prepare($sqlStudenten);
$stmtStudenten->bind_param("s", $resetToken);
$stmtStudenten->execute();
$resultStudenten = $stmtStudenten->get_result();

// Controleer of er een gebruiker is gevonden in een van de tabellen
if ($resultPersonen->num_rows == 0 && $resultStudenten->num_rows == 0) {
    die('Ongeldige of verlopen token.');
}

// Verkrijg de gebruiker (persoon of student)
$user = null;
if ($resultPersonen->num_rows > 0) {
    $user = $resultPersonen->fetch_assoc();
} else {
    $user = $resultStudenten->fetch_assoc();
}

// Als het formulier is ingediend om het wachtwoord te resetten
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verkrijg de nieuwe wachtwoord en bevestiging
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Controleer of de wachtwoorden overeenkomen
    if ($newPassword != $confirmPassword) {
        echo "De wachtwoorden komen niet overeen.";
    } else {
        // Versleutel het nieuwe wachtwoord
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Werk het wachtwoord bij in de juiste tabel (Personen of Studenten)
        if ($user['rol_id']) { // Als het een persoon is (rol_id aanwezig in Personen)
            $updateSql = "UPDATE Personen SET wachtwoord = ?, reset_token = NULL WHERE person_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $hashedPassword, $user['person_id']);
        } else { // Als het een student is (student_id in Studenten)
            $updateSql = "UPDATE Studenten SET wachtwoord = ?, reset_token = NULL WHERE student_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $hashedPassword, $user['student_id']);
        }

        // Voer de update uit en controleer of het is gelukt
        if ($updateStmt->execute()) {
            echo "Je wachtwoord is succesvol bijgewerkt!";
        } else {
            echo "Er is een fout opgetreden bij het bijwerken van je wachtwoord.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord Resetten</title>
    <link rel="stylesheet" href="assests/css/style.css">
</head>
<body>
    <div class="reset-container">
        <h2>Stel je wachtwoord opnieuw in</h2>
        <form action="reset_password.php?token=<?php echo $resetToken; ?>" method="POST">
            <div class="form-group">
                <label for="password">Nieuw wachtwoord:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Bevestig nieuw wachtwoord:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit">Wachtwoord instellen</button>
        </form>
    </div>
</body>
</html>
