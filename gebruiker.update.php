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

$person_id = $_GET['id'];

// Haal de gegevens van de gebruiker op (READ)
$sql = "SELECT p.person_id, p.voornaam, p.achternaam, p.geboortedatum, p.telefoon, p.email, p.geslacht, p.adres, p.status, p.rol_id, r.rol 
        FROM Personen p
        LEFT JOIN Rollen r ON p.rol_id = r.rol_id
        WHERE p.person_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $person_id);
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
    $rol_id = $_POST['rol_id'];

    // Wachtwoord niet gewijzigd? Gebruik de oude wachtwoord waarde
    if (isset($_POST['wachtwoord']) && !empty($_POST['wachtwoord'])) {
        $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);
        $sql = "UPDATE Personen SET voornaam = ?, achternaam = ?, geboortedatum = ?, telefoon = ?, email = ?, geslacht = ?, adres = ?, status = ?, rol_id = ?, wachtwoord = ? WHERE person_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $status, $rol_id, $wachtwoord, $person_id);
    } else {
        $sql = "UPDATE Personen SET voornaam = ?, achternaam = ?, geboortedatum = ?, telefoon = ?, email = ?, geslacht = ?, adres = ?, status = ?, rol_id = ? WHERE person_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssii", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $status, $rol_id, $person_id);
    }

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Gebruiker succesvol bijgewerkt!</p>";
        header("Location: gebruiker.php"); 
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij bijwerken gebruiker: " . $stmt->error . "</p>";
    }
}

// Haal alle rollen op voor de dropdown
$roles_sql = "SELECT rol_id, rol FROM Rollen";
$roles_result = $conn->query($roles_sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Bewerken</title>
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>
    <h2>Gebruiker Bewerken</h2>
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
            <option value="Deaactief" <?php if ($user['status'] == "Deactief") echo "selected"; ?>>Deactief</option>
        </select><br>
        <select name="rol_id" required>
            <?php
            if ($roles_result->num_rows > 0) {
                while ($role = $roles_result->fetch_assoc()) {
                    echo "<option value='" . $role['rol_id'] . "' " . ($user['rol_id'] == $role['rol_id'] ? "selected" : "") . ">" . $role['rol'] . "</option>";
                }
            }
            ?>
        </select><br>

        <!-- Wachtwoord veld voor optie om wachtwoord te veranderen -->
        <input type="password" name="wachtwoord" placeholder="Wachtwoord wijzigen"><br>

        <button type="submit">Bijwerken</button>
    </form>
    
    <a href="gebruiker.php" class="back-btn">Terug naar overzicht</a>

</body>
</html>

<?php
$conn->close();
?>
