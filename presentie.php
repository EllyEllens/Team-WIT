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

// Filters verwerken
$filter_presentie = isset($_GET['filter_presentie']) ? $_GET['filter_presentie'] : '';
$filter_klas = isset($_GET['filter_klas']) ? $_GET['filter_klas'] : '';

// Dynamische WHERE-clause opbouwen
$whereClauses = [];
if (!empty($filter_presentie)) {
    $whereClauses[] = "presentie_code = '$filter_presentie'";
}
if (!empty($filter_klas)) {
    $whereClauses[] = "klas_id = '$filter_klas'";
}
$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Data voor grafieken ophalen
$presentie_aantal_query = "SELECT presentie_code, COUNT(*) AS aantal FROM Aanwezigheid $whereClause GROUP BY presentie_code";
$presentie_result = $conn->query($presentie_aantal_query);

$aanwezig = 0;
$afwezig = 0;
$te_laat = 0;

while ($row = $presentie_result->fetch_assoc()) {
    if ($row['presentie_code'] == 'Aanwezig') {
        $aanwezig = $row['aantal'];
    } elseif ($row['presentie_code'] == 'Afwezig') {
        $afwezig = $row['aantal'];
    } elseif ($row['presentie_code'] == 'Te laat') {
        $te_laat = $row['aantal'];
    }
}

// Extra query voor lesblokken
$lesblok_aantal_query = "SELECT lesblok_id, COUNT(*) AS aantal FROM Aanwezigheid $whereClause GROUP BY lesblok_id";
$lesblok_result = $conn->query($lesblok_aantal_query);

$lesblokken = [];
while ($row = $lesblok_result->fetch_assoc()) {
    $lesblokken[] = $row['aantal'];
}

// Klassen ophalen uit de `klas`-tabel
$klassen_query = "SELECT klas_id, naam FROM klas ORDER BY naam";
$klassen_result = $conn->query($klassen_query);

$klassen = [];
while ($row = $klassen_result->fetch_assoc()) {
    $klassen[$row['klas_id']] = $row['naam'];
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <title>Presentie Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .chart-container {
            width: 48%;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Knopstijl aangepast naar binnen de container */
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

        .filter-container form {
            display: inline-block;
            text-align: left;
        }

        .filter-container select {
            margin: 10px;
        }

        .filter-container button {
            background-color: #0e6402;
            color: white;
            font-weight: bold;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-container button:hover {
            background-color: hsl(113, 82%, 35%);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Presentie Dashboard</h2>

    <!-- Verplaats de knop hier naar binnen de container -->
    <div class="text-center mb-4">
        <a href="admin.php" class="home-btn">Terug naar Home</a>
    </div>

    <!-- Filteropties -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="filter_presentie">Filter op presentie:</label>
            <select name="filter_presentie" id="filter_presentie" class="form-control d-inline-block w-auto">
                <option value="">Alles</option>
                <option value="Aanwezig" <?= $filter_presentie == "Aanwezig" ? "selected" : "" ?>>Aanwezig</option>
                <option value="Afwezig" <?= $filter_presentie == "Afwezig" ? "selected" : "" ?>>Afwezig</option>
                <option value="Te laat" <?= $filter_presentie == "Te laat" ? "selected" : "" ?>>Te laat</option>
                <option value="Laatbrief" <?= $filter_presentie == "Laatbrief" ? "selected" : "" ?>>Laatbrief</option>
                <option value="Vrijstelling" <?= $filter_presentie == "Vrijstelling" ? "selected" : "" ?>>Vrijstelling</option>
            </select>

            <label for="filter_klas">Filter op klas:</label>
            <select name="filter_klas" id="filter_klas" class="form-control d-inline-block w-auto">
                <option value="">Alle klassen</option>
                <?php foreach ($klassen as $id => $naam) { ?>
                    <option value="<?= $id ?>" <?= $filter_klas == $id ? "selected" : "" ?>><?= $naam ?></option>
                <?php } ?>
            </select>

            <button type="submit">Toepassen</button>
        </form>
    </div>

    <!-- Grafieken -->
    <div class="d-flex justify-content-between flex-wrap">
        <div class="card chart-container">
            <h4 class="text-center">Presentie Statistieken</h4>
            <canvas id="presentieChart"></canvas>
        </div>

        <div class="card chart-container">
            <h4 class="text-center">Lesblok Statistieken</h4>
            <canvas id="lesblokChart"></canvas>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <h5>Lesblok Aanwezigheden</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Lesblok</th>
                    <th>Aantal Aanwezigheden</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lesblokken as $index => $aantal) { ?>
                    <tr>
                        <td>Lesblok <?= $index + 1 ?></td>
                        <td><?= $aantal ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Presentie Statistieken Grafiek
    var ctx1 = document.getElementById('presentieChart').getContext('2d');
    var presentieChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Aanwezig', 'Afwezig', 'Laat', 'Laatbrief', 'Vrijstelling'],
            datasets: [{
                label: 'Aantal Presenties',
                data: [<?= $aanwezig; ?>, <?= $afwezig; ?>, <?= $te_laat; ?>],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                borderColor: ['#218838', '#c82333', '#e0a800'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Lesblok Statistieken Grafiek
    var ctx2 = document.getElementById('lesblokChart').getContext('2d');
    var lesblokChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Lesblok 1', 'Lesblok 2', 'Lesblok 3', 'Lesblok 4'], // Pas aan op basis van je lesblokken
            datasets: [{
                label: 'Aantal Aanwezigheden per Lesblok',
                data: [<?= implode(',', $lesblokken); ?>],
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>

</body>
</html>
