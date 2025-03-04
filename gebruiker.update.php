<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen geldig ID opgegeven.");
}

$id = $_GET['id'];

// Haal de gegevens van de gebruiker op (READ)
$sql = "SELECT * FROM personen WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
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
    $rol_id = $_POST['rol_id'];

    $sql = "UPDATE personen SET voornaam = ?, achternaam = ?, geboortedatum = ?, telefoon = ?, email = ?, geslacht = ?, adres = ?, rol_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $rol_id, $id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Gebruiker succesvol bijgewerkt!</p>";
        header("Location: index.php");
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij bijwerken gebruiker: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Bewerken</title>
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
        <input type="number" name="rol_id" value="<?php echo $user['rol_id']; ?>" required><br>
        <button type="submit">Bijwerken</button>
    </form>
    
    <a href="student.php">Terug naar overzicht</a>
</body>
</html>

<?php
$conn->close();
?>
