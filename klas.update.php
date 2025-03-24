<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";

// Maak een databaseverbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

$klasId = $_GET['klas_id'] ?? null;
$klas = null;

if ($klasId) {
    // Haal klasgegevens op voor het updateformulier
    $sql = "SELECT * FROM klas WHERE klas_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klasId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $klas = $result->fetch_assoc();
    } else {
        echo "Klas niet gevonden.";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['className'])) {
    // Verwerk het formulier om de klas bij te werken
    $newClassName = $_POST['className'];

    $updateSql = "UPDATE klas SET naam = ? WHERE klas_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newClassName, $klasId);

    if ($updateStmt->execute()) {
        echo "Klas succesvol bijgewerkt!";
    } else {
        echo "Fout bij het bijwerken van de klas: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klas Bijwerken</title>
    <link rel="stylesheet" href="assests/css/klassen.css">
</head>
<body>
    <!-- Header with profile and search bar -->
    <header class="header">
        <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
        <div class="header-right">
            <input type="text" class="search-bar" placeholder="Zoeken...">
            <img src="assests/img/logo.webp" alt="Profiel" class="profile-pic">
        </div>
    </header>

    <h1 style="margin-top: 80px;">Klas Gegevens Bijwerken</h1>

    <!-- Formulier voor het bijwerken van klas -->
    <div class="update-form-container">
        <?php if ($klas): ?>
            <form action="klas.update.php?klas_id=<?= $klas['klas_id'] ?>" method="POST">
                <label for="className">Klasnaam:</label>
                <input type="text" id="className" name="className" value="<?= htmlspecialchars($klas['naam']) ?>" required>
                <button type="submit">Update Klas</button>
            </form>
        <?php else: ?>
            <p>Geen klas geselecteerd voor bewerking.</p>
        <?php endif; ?>
    </div>

    <script>
        // Sidebar toggle functionaliteit
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            document.querySelector('.container').style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
        });
    </script>
</body>
</html>
