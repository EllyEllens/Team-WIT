<?php
// Verbinding maken met de database
$conn = new mysqli("localhost", "root", "", "natin");

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Haal de geselecteerde dag op uit de URL parameter
$dag = '';
if (isset($_GET['dag']) && !empty($_GET['dag'])) {
    $dag = $_GET['dag']; // Dit zal een dagnaam zijn zoals "maandag", "dinsdag", etc.
}

// Query om de studenten met hun klas_id op te halen
$sql_studenten = "
    SELECT personen.id AS student_id, voornaam, achternaam, geslacht, studenten_per_klas.klas_id
    FROM personen
    JOIN studenten_per_klas ON personen.id = studenten_per_klas.student_id";
$result_studenten = $conn->query($sql_studenten);

if (!$result_studenten) {
    die("Fout in studenten-query: " . $conn->error);
}

// Query om het rooster voor de geselecteerde dag van de week op te halen voor klas_id = 7, inclusief vakken per lesblok
$rooster_per_klas = [];
if (!empty($dag)) {
    $sql_rooster = "
        SELECT rooster.begintijd, rooster.eindtijd, vak.vak AS vak_naam, rooster.dag, rooster.klas_id, rooster.lesblok
        FROM rooster 
        JOIN vak ON rooster.vak_id = vak.vak_id
        WHERE rooster.dag = '$dag' AND rooster.klas_id = 7
        ORDER BY rooster.begintijd";  // Sorteren op begintijd om lesblokken correct weer te geven
    $result_rooster = $conn->query($sql_rooster);

    if (!$result_rooster) {
        die("Fout in rooster-query: " . $conn->error);
    }

    // Roosterdata in een array opslaan per klas en lesblok
    while ($row_rooster = $result_rooster->fetch_assoc()) {
        $klas_id = $row_rooster['klas_id'];
        $lesblok = $row_rooster['lesblok'];
        $rooster_per_klas[$klas_id][$lesblok][] = [
            'begintijd' => date("H:i", strtotime($row_rooster['begintijd'])),
            'eindtijd' => date("H:i", strtotime($row_rooster['eindtijd'])),
            'vak_naam' => $row_rooster['vak_naam']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentielijst Rooster</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        textarea {
            width: 100%;
            height: 40px;
        }
    </style>
</head>
<body>
    <h2>Presentielijst Rooster</h2>
    
    <form method="GET" action="">
        <label for="dag">Kies een dag:</label>
        <select id="dag" name="dag" required>
            <option value="maandag" <?= $dag == 'maandag' ? 'selected' : '' ?>>Maandag</option>
            <option value="dinsdag" <?= $dag == 'dinsdag' ? 'selected' : '' ?>>Dinsdag</option>
            <option value="woensdag" <?= $dag == 'woensdag' ? 'selected' : '' ?>>Woensdag</option>
            <option value="donderdag" <?= $dag == 'donderdag' ? 'selected' : '' ?>>Donderdag</option>
            <option value="vrijdag" <?= $dag == 'vrijdag' ? 'selected' : '' ?>>Vrijdag</option>
        </select>
        <button type="submit">Toon Rooster</button>
    </form>

    <?php if (!empty($dag)): ?>
        <p style="text-align: center;">Geselecteerde dag: <strong><?= htmlspecialchars($dag) ?></strong></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>Geslacht</th>
                <th>Lesblok 1</th>
                <th>Lesblok 2</th>
                <th>Lesblok 3</th>
                <th>Lesblok 4</th>
                <th>Opmerkingen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_studenten->num_rows > 0) {
                while ($row_studenten = $result_studenten->fetch_assoc()) {
                    $klas_id = $row_studenten["klas_id"];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row_studenten["voornaam"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row_studenten["achternaam"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row_studenten["geslacht"]) . "</td>";
                    
                    // Lesblokken invullen met vakken en tijden
                    for ($i = 1; $i <= 4; $i++) {
                        if (isset($rooster_per_klas[$klas_id][$i])) {
                            $rooster_data = $rooster_per_klas[$klas_id][$i];
                            echo "<td>";
                            foreach ($rooster_data as $les) {
                                echo "<div>" . $les['begintijd'] . " - " . $les['eindtijd'] . "</div>";
                                echo "<div>" . htmlspecialchars($les['vak_naam']) . "</div>";
                            }
                            echo "</td>";
                        } else {
                            echo "<td></td>";
                        }
                    }

                    echo "<td><textarea placeholder='Opmerkingen'></textarea></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Geen studenten gevonden</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php if (!empty($dag) && empty($rooster_per_klas)): ?>
        <p style="text-align: center; color: red;">Geen rooster gevonden voor deze dag.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
