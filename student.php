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
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
    <title>Studenten Overzicht</title>
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>
    <div class="container">
    <div class="header">
        <h2>Studenten Overzicht</h2>
    </div>

    <!-- Add user button outside header -->
    <div class="add-user-container">
        <a href="student.creat.php" class="add-user-btn">Student Toevoegen</a>
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
                                        <a href='student.delete.php?id=" . $row['student_id'] . "' class='delete-btn'>Verwijderen</a>
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

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("hidden");
            document.getElementById("main-content").classList.toggle("expanded");
        }
    </script>
</body>
</html>


<?php
$conn->close();
?>
