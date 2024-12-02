<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Laadt PHPMailer via Composer

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natin";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Als het wachtwoord vergeten formulier wordt ingediend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo "<script>alert('Ongeldig e-mailadres.');</script>";
    } else {
        // Controleer of de e-mail bestaat
        $sql = "SELECT * FROM student WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Fout bij voorbereiden van query: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Maak een willekeurige token
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Voeg token toe aan de database
            $sql = "INSERT INTO wachtwoord (email, token, expires) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Fout bij voorbereiden van query: " . $conn->error);
            }

            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();

            // Verstuur e-mail met PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Mail server instellingen
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'jouw-email@gmail.com'; // Je Gmail-adres
                $mail->Password = 'je-app-wachtwoord'; // Je Gmail App-wachtwoord
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Mail details
                $mail->setFrom('noreply@yourdomain.com', 'Wachtwoord Reset');
                $mail->addAddress($email);
                $mail->Subject = 'Wachtwoord resetten';
                $resetLink = "https://jouwdomein.com/reset_password.php?token=" . urlencode($token);
                $mail->Body = "Hallo,\n\nKlik op de volgende link om je wachtwoord te resetten:\n\n" . $resetLink . "\n\nDe link is 1 uur geldig.\n\nMet vriendelijke groet,\nHet Team";

                // Verstuur de e-mail
                $mail->send();
                echo "<script>alert('Een e-mail met een reset link is verzonden naar " . $email . "');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Er is iets misgegaan met het verzenden van de e-mail: " . $mail->ErrorInfo . "');</script>";
            }
        } else {
            echo "<script>alert('Geen account gevonden met dit e-mailadres.');</script>";
        }

        $stmt->close();
    }
}

// Sluit de verbinding
$conn->close();
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
