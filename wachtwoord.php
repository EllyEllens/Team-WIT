<?php
// Verbinding maken met de database
$servername = "localhost";
$username = "root"; // Als je geen wachtwoord hebt, laat dit leeg
$password = ""; // Voeg hier het wachtwoord toe als je er een hebt
$dbname = "wit"; // Dit is de naam van je database

// Maak verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// De gebruiker vult zijn e-mailadres in
if (isset($_POST['email'])) {
   $email = $_POST['email'];
   
   // Stel de reset link in
   $resetToken = bin2hex(random_bytes(16)); // Genereer een willekeurige token
   $resetLink = "http://jouwdomein.com/reset_password.php?token=" . $resetToken; // Pas de URL aan

   // Sla de token op in de database voor later gebruik
   $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
   $stmt->bind_param("ss", $resetToken, $email);
   $stmt->execute();

   // Stuur de e-mail met de resetlink
   $to = $email;
   $subject = "Wachtwoord Reset Verzoek";
   $message = "Klik op de onderstaande link om je wachtwoord opnieuw in te stellen:\n\n" . $resetLink;
   $headers = "From: no-reply@jouwdomein.com\r\n";

   if (mail($to, $subject, $message, $headers)) {
       echo "Er is een e-mail gestuurd met de reset link.";
   } else {
       echo "Er is een fout opgetreden bij het verzenden van de e-mail.";
   }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    // Verbind met de database
    $mysqli = new mysqli("localhost", "root", "", "educatie");

    if ($mysqli->connect_error) {
        die("Verbinding mislukt: " . $mysqli->connect_error);
    }

    // Controleer of het e-mailadres bestaat in de database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        // Genereer een uniek token voor de reset link
        $token = bin2hex(random_bytes(50)); // Genereer een veilige token

        // Sla het token op in de database (zorg ervoor dat het token gekoppeld is aan de gebruiker)
        $query = "UPDATE users SET reset_token = '$token' WHERE email = '$email'";
        $mysqli->query($query);

        // Verstuur een resetlink naar het opgegeven e-mailadres
        $reset_link = "http://jouwwebsite.com/reset_wachtwoord.php?token=$token";
        $subject = "Wachtwoord reset link";
        $message = "Klik op de volgende link om je wachtwoord te resetten: $reset_link";
        $headers = "From: no-reply@jouwwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "Er is een e-mail gestuurd met de reset link.";
        } else {
            echo "Er is een fout opgetreden bij het versturen van de e-mail.";
        }
    } else {
        echo "Er is geen account gekoppeld aan dit e-mailadres.";
    }

    $mysqli->close();
}
?>

<!DOCTYPE html
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">

   <!--=============== REMIXICONS ===============-->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/js/all.min.js">
   <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

   <!--=============== CSS ===============-->
   <link rel="stylesheet" href="assests/css/style.css">
   
   <title>Wachtwoord Herstellen - NATIN MBO</title>
</head>
<body>
   <!--=============== LOGIN IMAGE ===============-->
   <svg class="login__blob" viewBox="0 0 566 840" xmlns="http://www.w3.org/2000/svg">
      <mask id="mask0" mask-type="alpha">
         <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
      </mask>
   
      <g mask="url(#mask0)">
         <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
   
         <!-- Afbeelding (Aanbevolen grootte: 1000 x 1200) -->
         <image class="login__img" href="assests/img/natin.png"/>
      </g>
   </svg>      

   <!--=============== LOGIN ===============-->
   <div class="login container grid" id="loginAccessRegister">
      <!--===== Inlog Toegang =====-->
      <div class="login__access">
         <h1 class="login__title">
            <img src="assests/img/logo.webp" alt="Logo" class="login__logo">
            Wachtwoord vergeten.
         </h1>
         
         <div class="login__area">
            <form action="wachtwoord.php" method="POST" class="login__form">
               <div class="login__content grid">
                  <div class="login__box">
                     <input type="email" name="email" id="email" required placeholder="" class="login__input">
                     <label for="email" class="login__label">Voer je e-mail adress in</label>
                        <i class="ri-mail-fill login__icon"></i>
                  </div>
               </div>
               <button type="submit" class="login__button">Stuur Reset Link</button>
            </form>
           
         </div>
      </div>
   </div>
   
   <!--=============== MAIN JS ===============-->
   
   <script src="assests/js/main.js"></script>
</body>
</html>
