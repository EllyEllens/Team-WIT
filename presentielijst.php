<?php
// DATABASE VERBINDING
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "wit";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// FUNCTION TO SANITIZE INPUTS
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// STUDENTEN OPHALEN VOOR GESELECTEERDE KLAS
$klas_id = isset($_POST['klas_id']) ? sanitizeInput($_POST['klas_id']) : '';

if ($klas_id != '') {
    $studenten_query = "SELECT s.student_id, s.voornaam, s.achternaam 
                        FROM Studenten s
                        JOIN student_klas sk ON s.student_id = sk.student_id
                        WHERE sk.klas_id = ?";

    $stmt = $conn->prepare($studenten_query);
    $stmt->bind_param("i", $klas_id);
    $stmt->execute();
    $studenten_result = $stmt->get_result();
    $stmt->close();
} else {
    $studenten_result = null;
}

// FUNCTIONALITEIT VOOR PRESENTIE OPSLAAN EN UPDATEN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_presentie'])) {
        $student_id = sanitizeInput($_POST['student_id']);
        $lesblok_id = sanitizeInput($_POST['lesblok_id']);
        $klas_id = sanitizeInput($_POST['klas_id']);
        $presentie_code = sanitizeInput($_POST['presentie_code']);
        $opmerking = sanitizeInput($_POST['opmerking']);

        if (!empty($student_id) && !empty($lesblok_id) && !empty($klas_id) && !empty($presentie_code)) {
            $stmt = $conn->prepare("INSERT INTO Aanwezigheid (student_id, lesblok_id, klas_id, presentie_code, opmerking) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiss", $student_id, $lesblok_id, $klas_id, $presentie_code, $opmerking);

            if ($stmt->execute()) {
                echo "Presentie succesvol opgeslagen.";
            } else {
                echo "Fout bij het opslaan van presentie: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Alle velden moeten worden ingevuld!";
        }
    } elseif (isset($_POST['submit_update'])) {
        $aanwezigheid_id = sanitizeInput($_POST['aanwezigheid_id']);
        $presentie_code = sanitizeInput($_POST['presentie_code']);
        $opmerking = sanitizeInput($_POST['opmerking']);

        $stmt = $conn->prepare("UPDATE Aanwezigheid SET presentie_code = ?, opmerking = ? WHERE aanwezigheid_id = ?");
        $stmt->bind_param("ssi", $presentie_code, $opmerking, $aanwezigheid_id);

        if ($stmt->execute()) {
            echo "Presentie succesvol bijgewerkt.";
        } else {
            echo "Fout bij het bijwerken van presentie: " . $stmt->error;
        }
        $stmt->close();
    }
}

// STUDENTEN, LESSEN & KLASSEN OPHALEN
$lesblokken = $conn->query("SELECT * FROM Lesblok");
$klassen = $conn->query("SELECT * FROM klas");

$aanwezigheden_query = "SELECT a.*, s.voornaam, s.achternaam, l.dag, l.start, l.eind, k.naam AS klas_naam 
                        FROM Aanwezigheid a 
                        JOIN Studenten s ON a.student_id = s.student_id
                        JOIN Lesblok l ON a.lesblok_id = l.lesblok_id
                        JOIN klas k ON a.klas_id = k.klas_id
                        ORDER BY l.dag, l.start";

if (isset($_POST['zoekterm'])) {
    $zoekterm = sanitizeInput($_POST['zoekterm']);
    $aanwezigheden_query = "SELECT a.*, s.voornaam, s.achternaam, l.dag, l.start, l.eind, k.naam AS klas_naam 
                            FROM Aanwezigheid a 
                            JOIN Studenten s ON a.student_id = s.student_id
                            JOIN Lesblok l ON a.lesblok_id = l.lesblok_id
                            JOIN klas k ON a.klas_id = k.klas_id
                            WHERE s.voornaam LIKE ? OR s.achternaam LIKE ? OR k.naam LIKE ?
                            ORDER BY l.dag, l.start";
    $stmt = $conn->prepare($aanwezigheden_query);
    $like_term = "%" . $zoekterm . "%";
    $stmt->bind_param("sss", $like_term, $like_term, $like_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($aanwezigheden_query);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
    <title>Presentie Dashboard</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f9f9f9; 
            margin: 0; 
            padding: 0; 
        }

        h2 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 20px;
        }

        .container { 
            display: flex; 
            flex-direction: column; 
            gap: 20px; 
            padding: 20px; 
            margin: 20px auto;
            max-width: 1000px;
            background-color: #fff;
            border-radius: 10px; 
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); 
            backdrop-filter: blur(8px); 
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0px 6px 16px rgba(0, 128, 0, 0.15); 
        }

        .search-bar { 
            display: flex; 
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 300px;
            margin-right: 10px;
        }

        form, table { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        input, select, button { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-size: 16px; 
            background: #f4f4f4;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 255, 0, 0.7);
        }

        button { 
            background: hsl(113, 82%, 35%); 
            color: white; 
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        button:hover { 
            background: hsl(113, 82%, 35%); 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }

        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }

        th { 
            background-color: hsl(113, 82%, 35%); 
            color: white; 
            font-weight: bold;
        }

        td { 
            background-color: #f4f4f4; 
        }

        td:hover {
            background-color: #e1f1e1;
        }

        .status-A { 
            background: #f8d7da; 
            color: #721c24;
        }

        .status-L { 
            background: #fff3cd; 
            color: #856404;
        }

        .status-Z { 
            background: #fff3e3; 
            color: #7c3f00;
        }

        .status-V { 
            background: #d4edda; 
            color: #0e6402;
        }
        .home-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #0e6402;
            color: white;
            padding: 12px 20px;
            text-align: center;
            border-radius: 8px;
            display: inline-block;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .home-btn:hover {
            background-color: hsl(113, 82%, 35%);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Presentie Dashboard</h2>

    <a href="docent.dashboard.html" class="home-btn">Terug naar Home</a>
    
    <form method="post" class="search-bar">
        <input type="text" name="zoekterm" placeholder="Zoek op naam" value="<?= isset($zoekterm) ? $zoekterm : '' ?>">
        <button type="submit">Zoeken</button>
    </form>

    <form method="post">
        <div class="form-container">
            <label for="klas_id">Klas:</label>
            <select name="klas_id" id="klas_id" required onchange="this.form.submit()">
                <option value="">Selecteer een klas</option>
                <?php while ($row = $klassen->fetch_assoc()) { ?>
                    <option value="<?= $row['klas_id'] ?>" <?= $klas_id == $row['klas_id'] ? 'selected' : '' ?>>
                        <?= $row['naam'] ?>
                    </option>
                <?php } ?>
            </select>

            <?php if ($studenten_result) { ?>
                <label for="student_id">Student:</label>
                <select name="student_id" required>
                    <option value="">Selecteer een student</option>
                    <?php while ($row = $studenten_result->fetch_assoc()) { ?>
                        <option value="<?= $row['student_id'] ?>"><?= $row['voornaam'] . " " . $row['achternaam'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            
            <label for="lesblok_id">Lesblok:</label>
            <select name="lesblok_id" required>
                <option value="">Selecteer een lesblok</option>
                <?php while ($row = $lesblokken->fetch_assoc()) { ?>
                    <option value="<?= $row['lesblok_id'] ?>">
                        <?= $row['dag'] . " " . $row['start'] . " - " . $row['eind'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <label for="presentie_code">Status:</label>
        <select name="presentie_code" required>
            <option value="Aanwezig">Aanwezig</option>
            <option value="Afwezig">Afwezig</option>
            <option value="Te laat">Laat</option>
            <option value="Te laat">Laatbrief</option>
            <option value="Te laat">Vrijstelling</option>
        </select>

        <label for="opmerking">Opmerking:</label>
        <input type="text" name="opmerking" placeholder="Optionele opmerking">

        <button type="submit" name="submit_presentie">Opslaan</button>
    </form>

    <h3>Aanwezigheden</h3>
    <table>
        <tr>
            <th>Student</th>
            <th>Lesblok</th>
            <th>Presentie</th>
            <th>Opmerking</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['voornaam'] . " " . $row['achternaam'] ?></td>
                <td><?= $row['dag'] . " " . $row['start'] . " - " . $row['eind'] ?></td>
                <td class="status-<?= $row['presentie_code'] ?>"><?= $row['presentie_code'] ?></td>
                <td><?= $row['opmerking'] ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="aanwezigheid_id" value="<?= $row['aanwezigheid_id'] ?>">
                        <select name="presentie_code">
                            <option value="Aanwezig" <?= $row['presentie_code'] == 'Aanwezig' ? 'selected' : '' ?>>Aanwezig</option>
                            <option value="Afwezig" <?= $row['presentie_code'] == 'Afwezig' ? 'selected' : '' ?>>Afwezig</option>
                            <option value="Laat" <?= $row['presentie_code'] == 'Laat' ? 'selected' : '' ?>>Laat</option>
                            <option value="Laatbrief" <?= $row['presentie_code'] == 'Laatbrief' ? 'selected' : '' ?>>Laatbrief</option>
                            <option value="Vrijstelling" <?= $row['presentie_code'] == 'Vrijstelling' ? 'selected' : '' ?>>Vrijstelling</option>
                        </select>
                        <input type="text" name="opmerking" value="<?= $row['opmerking'] ?>">
                        <button type="submit" name="submit_update">Bijwerken</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
