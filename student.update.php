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

// Haal de gegevens van de gebruiker op (READ)
$sql = "SELECT * FROM studenten WHERE student_id = ?";
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

    $sql = "UPDATE studenten SET voornaam = ?, achternaam = ?, geboortedatum = ?, telefoon = ?, email = ?, geslacht = ?, adres = ?, status = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $status, $student_id);

    if ($stmt->execute()) {
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
            <option value="M" <?php if ($user['geslacht'] == "M") echo "selected"; ?>>Man</option>
            <option value="V" <?php if ($user['geslacht'] == "V") echo "selected"; ?>>Vrouw</option>
            <option value="X" <?php if ($user['geslacht'] == "X") echo "selected"; ?>>Anders</option>
        </select><br>
        <input type="text" name="adres" value="<?php echo $user['adres']; ?>" required><br>
        <select name="status" required>
            <option value="actief" <?php if ($user['status'] == "actief") echo "selected"; ?>>Actief</option>
            <option value="deactief" <?php if ($user['status'] == "deactief") echo "selected"; ?>>Deactief</option>
        </select><br>
        <button type="submit">Bijwerken</button>
    </form>
    
    <a href="student.php" class="back-btn">Terug naar overzicht</a>

</body>
</html>

<?php
$conn->close();
?>
