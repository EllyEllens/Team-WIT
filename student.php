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

// Studenten ophalen uit de database (READ)
$sql = "SELECT student_id, voornaam, achternaam, geboortedatum, email, adres, geslacht, status 
        FROM Studenten";

$result = $conn->query($sql);

if ($result === false) {
    die("Fout bij het ophalen van studenten: " . $conn->error);
}

// Verwijder de student via een AJAX-aanroep
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Eerst de gerelateerde records verwijderen uit de 'aanwezigheid' tabel
    $sql_delete_aanwezigheid = "DELETE FROM aanwezigheid WHERE student_id = ?";
    $stmt_aanwezigheid = $conn->prepare($sql_delete_aanwezigheid);
    $stmt_aanwezigheid->bind_param("i", $student_id);
    $stmt_aanwezigheid->execute();
    $stmt_aanwezigheid->close();

    // Nu de student zelf verwijderen
    $sql_delete_student = "DELETE FROM Studenten WHERE student_id = ?";
    $stmt_student = $conn->prepare($sql_delete_student);
    $stmt_student->bind_param("i", $student_id);

    if ($stmt_student->execute()) {
        echo "Student succesvol verwijderd";
    } else {
        echo "Fout bij het verwijderen van de student: " . $conn->error;
    }

    $stmt_student->close();
    exit();
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <title>Studenten Overzicht</title>
    <link rel="stylesheet" href="assests/css/student.css">
    <script>
        // Functie om pop-up bevestiging te tonen en de student te verwijderen via AJAX
        function confirmDelete(studentId) {
            var confirmation = confirm("Weet je zeker dat je deze student wilt verwijderen?");
            if (confirmation) {
                // Verzend de POST-aanroep via AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "", true); // We verzenden naar de huidige pagina
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText); // Toon bericht bij succes
                        location.reload(); // Vernieuw de pagina om de veranderingen weer te geven
                    }
                };
                xhr.send("student_id=" + studentId);
            }
        }
    </script>
</head>
<body>
    <div class="container">
    <div class="header">
        <h2>Studenten Overzicht</h2>
    </div>

    <div class="add-user-container">
        <a href="student.creat.php" class="add-user-btn">Student Toevoegen</a>
        <a href="admin.php" class="add-user-btn">Terug naar Home</a>
    </div>
    
    <div class="main-content">
        <div class="container-wrapper">
            <div class="table-container">
                <table id="personenTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Voornaam</th>
                            <th>Achternaam</th>
                            <th>Geboortedatum</th>
                            <th>Email</th>
                            <th>Geslacht</th>
                            <th>Adres</th>
                            <th>Status</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['student_id'] . "</td>";
                                echo "<td>" . $row['voornaam'] . "</td>";
                                echo "<td>" . $row['achternaam'] . "</td>";
                                echo "<td>" . $row['geboortedatum'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['geslacht'] . "</td>";
                                echo "<td>" . $row['adres'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>
                                        <a href='student.update.php?id=" . $row['student_id'] . "' class='edit-btn'>Bewerken</a>
                                        <a href='#' onclick='confirmDelete(" . $row['student_id'] . ")' class='delete-btn'>Verwijderen</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>Geen studenten gevonden</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>