<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Haal alle klassen op uit de database
$sql = "SELECT * FROM klas ORDER BY naam ASC";
$result = $conn->query($sql);

$klassen = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $klassen[] = $row;
    }
}

// Controleer of er een klas_id is meegegeven via GET
if (isset($_GET['klas_id'])) {
    $klasId = $_GET['klas_id'];

    // Verwijder de klas uit de database
    $deleteSql = "DELETE FROM klas WHERE klas_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $klasId); // 'i' staat voor integer type voor klas_id
    $stmt->execute();

    // Controleer of de verwijdering succesvol was
    if ($stmt->affected_rows > 0) {
        // Redirect naar klassen.php na succesvolle verwijdering
        header("Location: klassen.php");
        exit(); // Zorg ervoor dat de script stopt na de redirect
    } else {
        echo "Er is een fout opgetreden bij het verwijderen van de klas.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht van Klasgegevens</title>
    <link rel="stylesheet" href="assests/css/klassen.css">
</head>
<body>
    <h1 style="margin-top: 80px;">Overzicht van Klasgegevens</h1>

    <!-- Overzicht van de klassen in containers -->
    <div class="klassen-container">
        <?php foreach ($klassen as $klas): ?>
            <div class="klas-box">
                <h3><?= htmlspecialchars($klas['naam']); ?></h3>
                <a href="klas.delete.php?klas_id=<?= $klas['klas_id']; ?>" onclick="return confirm('Weet je zeker dat je deze klas wilt verwijderen?');">Verwijder deze klas</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
