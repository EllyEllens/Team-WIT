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

// Haal alle klassen op uit de database
$sql = "SELECT * FROM klas ORDER BY naam ASC";
$result = $conn->query($sql);

$klassen = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $klassen[] = $row;
    }
}

// Verkrijg studenten per klas
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['class_id'])) {
    $classId = (int) $_GET['class_id']; // Zorg ervoor dat dit een integer is
    
    // Verkrijg studenten die aan deze klas zijn gekoppeld
    $studentsSql = "
        SELECT s.voornaam, s.achternaam, s.status 
        FROM Studenten s
        INNER JOIN student_klas sk ON s.student_id = sk.student_id
        WHERE sk.klas_id = ?";
    
    $stmt = $conn->prepare($studentsSql);
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students); // Stuur de gegevens als JSON terug naar de JavaScript functie
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasinformatie</title>
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <link rel="stylesheet" href="assests/css/klassen.css">
</head>
<body>
    <!-- Header -->
    <div class="container">
    <div class="header">
        <h2>Klassen Overzicht</h2>
    </div>

    <div class="add-user-container">
        <a href="klas.creat.php" class="add-user-btn">Klas Toevoegen</a>
        <a href="admin.php" class="add-user-btn">Terug naar Home</a>
    </div>

    <!-- Modal voor het toevoegen van een klas -->
    <div id="addClassModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nieuwe Klas Toevoegen</h2>
            <form action="klas.create.php" method="POST">
                <label for="className">Klasnaam:</label>
                <input type="text" id="className" name="className" required>
                <button type="submit">Voeg Klas Toe</button>
            </form>
        </div>
    </div>

    <!-- Modal voor het weergeven van klasdetails met studenten -->
    <div id="classDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Klas Details</h2>
            <div id="classDetailsContent"></div>
        </div>
    </div>

    <!-- Overzicht van de klassen in containers -->
    <div class="klassen-container">
        <?php foreach ($klassen as $klas): ?>
            <div class="klas-box" onclick="showClassDetails(<?= $klas['klas_id']; ?>)">
                <h3><?= htmlspecialchars($klas['naam']); ?></h3>
                <a href="klas.update.php?class_id=<?= $klas['klas_id']; ?>" class="update-button">Bewerk Klas</a>
                <a href="klas.delete.php?class_id=<?= $klas['klas_id']; ?>" class="update-button">Verwijder Klas</a>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Modal script voor het tonen van klasdetails met studenten
        var classDetailsModal = document.getElementById("classDetailsModal");
        var classDetailsContent = document.getElementById("classDetailsContent");

        function showClassDetails(classId) {
            console.log("Opgevraagd klas-ID:", classId); // Debugging
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "klassen.php?class_id=" + classId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log("Respons ontvangen:", xhr.responseText); // Debugging
                    var students = JSON.parse(xhr.responseText);
                    
                    // Maak de inhoud voor de pop-up
                    var content = "<h3>Studenten in deze klas:</h3><ul>";
                    students.forEach(function(student) {
                        content += "<li>" + student.voornaam + " " + student.achternaam + " - Status: " + student.status + "</li>";
                    });
                    content += "</ul>";

                    // Zet de inhoud in de modal
                    classDetailsContent.innerHTML = content;
                    
                    // Toon de modal
                    classDetailsModal.style.display = "block";
                }
            };
            xhr.send();
        }

        // Sluit de details modal wanneer de 'X' wordt geklikt
        var closeClassDetails = classDetailsModal.getElementsByClassName("close")[0];
        closeClassDetails.onclick = function() {
            classDetailsModal.style.display = "none";
        }

        // Sluit de details modal wanneer er buiten de modal wordt geklikt
        window.onclick = function(event) {
            if (event.target == classDetailsModal) {
                classDetailsModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
