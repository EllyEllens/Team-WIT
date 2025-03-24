<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen geldig ID opgegeven.");
}

$student_id = $_GET['id'];

// Haal de gegevens van de gebruiker op (READ) inclusief klas
$sql = "SELECT s.*, k.naam AS klas FROM studenten s
        LEFT JOIN student_klas sk ON s.student_id = sk.student_id
        LEFT JOIN klas k ON sk.klas_id = k.klas_id
        WHERE s.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Gebruiker niet gevonden.");
}

$user = $result->fetch_assoc();

// Update gebruiker (UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $geboortedatum = $_POST['geboortedatum'];
    $telefoon = $_POST['telefoon'];
    $email = $_POST['email'];
    $geslacht = $_POST['geslacht'];
    $adres = $_POST['adres'];
    $status = $_POST['status'];
    $klas = $_POST['klas']; // Klas toevoegen
    $wachtwoord = $_POST['wachtwoord']; // Wachtwoord toevoegen

    // Wachtwoord moet gehashed worden voor veiligheid
    if (!empty($wachtwoord)) {
        $hashed_wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);
    } else {
        $hashed_wachtwoord = $user['wachtwoord']; // Als het wachtwoord niet gewijzigd is, behoud de oude waarde
    }

    // Update query voor de studenten
    $sql = "UPDATE studenten SET voornaam = ?, achternaam = ?, geboortedatum = ?, telefoon = ?, email = ?, geslacht = ?, adres = ?, status = ?, wachtwoord = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $status, $hashed_wachtwoord, $student_id);

    // Voer de query uit
    if ($stmt->execute()) {
        // Update de student_klas tabel voor de klaswijziging
        $sql_klas = "UPDATE student_klas SET klas_id = ? WHERE student_id = ?";
        $stmt_klas = $conn->prepare($sql_klas);
        $stmt_klas->bind_param("ii", $klas, $student_id);
        $stmt_klas->execute();

        echo "<p style='color: green;'>Student succesvol bijgewerkt!</p>";
        header("Location: student.php");
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij bijwerken student: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Bewerken</title>
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>
    <h2>Student Bewerken</h2>
    <form method="post" action="">
        <input type="text" name="voornaam" value="<?php echo $user['voornaam']; ?>" required><br>
        <input type="text" name="achternaam" value="<?php echo $user['achternaam']; ?>" required><br>
        <input type="date" name="geboortedatum" value="<?php echo $user['geboortedatum']; ?>" required><br>
        <input type="text" name="telefoon" value="<?php echo $user['telefoon']; ?>" required><br>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
        <select name="geslacht" required>
            <option value="Man" <?php if ($user['geslacht'] == "Man") echo "selected"; ?>>Man</option>
            <option value="Vrouw" <?php if ($user['geslacht'] == "Vrouw") echo "selected"; ?>>Vrouw</option>
            <option value="Anders" <?php if ($user['geslacht'] == "Anders") echo "selected"; ?>>Anders</option>
        </select><br>
        <input type="text" name="adres" value="<?php echo $user['adres']; ?>" required><br>
        <select name="status" required>
            <option value="Actief" <?php if ($user['status'] == "Actief") echo "selected"; ?>>Actief</option>
            <option value="Deactief" <?php if ($user['status'] == "Deactief") echo "selected"; ?>>Deactief</option>
        </select><br>
        <select name="klas" required>
            <option value="" disabled selected>Selecteer een klas</option>
            <?php
            // Haal alle klassen op om ze als opties in te vullen
            $sql_klassen = "SELECT * FROM klas";
            $result_klassen = $conn->query($sql_klassen);
            while ($klas = $result_klassen->fetch_assoc()) {
                echo "<option value='" . $klas['klas_id'] . "' " . ($klas['klas_id'] == $user['klas_id'] ? 'selected' : '') . ">" . $klas['naam'] . "</option>";
            }
            ?>
        </select><br>
        <input type="password" name="wachtwoord" placeholder="Wachtwoord (leeg laten om niet te wijzigen)"><br> <!-- Wachtwoord veld toegevoegd -->
        <button type="submit">Bijwerken</button>
    </form>
    
    <a href="student.php" class="back-btn">Terug naar overzicht</a>

</body>
</html>

<?php
$conn->close();
?>
