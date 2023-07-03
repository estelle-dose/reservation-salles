<?php
session_start();

// Define an empty variable for the error message
$error_message = "";
$sucess_message = "";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    // Redirection vers la page de connexion
    header("Location: connexion.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $dateDebut = $_POST['date_debut'];
    $dateFin = $_POST['date_fin'];

    // Valider les dates et heures
    $datetimeDebut = date("Y-m-d H:i:s", strtotime($dateDebut));
    $datetimeFin = date("Y-m-d H:i:s", strtotime($dateFin));

    // Vérifier si la date de fin est postérieure à la date de début
    if ($datetimeDebut >= $datetimeFin) {
        $error_message = "La date de fin doit être postérieure à la date de début.";
    } else {
        // Vérifier si la réservation est dans la plage horaire autorisée (du lundi au vendredi, de 8h à 19h)
        $jourDebut = date("N", strtotime($datetimeDebut));
        $heureDebut = date("H", strtotime($datetimeDebut));
        $jourFin = date("N", strtotime($datetimeFin));
        $heureFin = date("H", strtotime($datetimeFin));

        if ($jourDebut >= 1 && $jourDebut <= 5 && $jourFin >= 1 && $jourFin <= 5 && $heureDebut >= 8 && $heureDebut <= 18 && $heureFin >= 9 && $heureFin <= 19) {
            // Connexion à la base de données
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "reservationsalles";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Vérifier les erreurs de connexion
            if ($conn->connect_error) {
                die("Erreur de connexion à la base de données : " . $conn->connect_error);
            }

            // Récupérer l'ID de l'utilisateur connecté
            $idUtilisateur = $_SESSION['id_utilisateur'];

            // Préparer et exécuter la requête d'insertion de la réservation
            $sql = "INSERT INTO reservations (titre, description, debut, fin, id_utilisateur)
                    VALUES ('$titre', '$description', '$datetimeDebut', '$datetimeFin', $idUtilisateur)";

            if ($conn->query($sql) === true) {
                $sucess_message = "Réservation créée avec succès.";
            } else {
                $error_message = "Erreur lors de la création de la réservation : " . $conn->error;
            }

            // Fermer la connexion à la base de données
            $conn->close();
        } else {
            $error_message = "La réservation doit être effectuée du lundi au vendredi, de 8h à 19h.";
        }
    }
}
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible"
        content="IE=edge">
        <meta name="viewport" content="width=device-width,
        initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="styles.css" rel="stylesheet"/>
    <title>Réservation de salle</title>
</head>
<body>
    <header>
        <div class='navbar'>
            <div class="logo"><a href="index.php">Pyramides de Gizeh</a></div>
            <ul class="links">
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="planning.php">Planning</a></li>
            </ul>
            <a href="reservation-form.php" class="action_btn">Réserver</a>
            <div class="toggle_btn">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

        <div class="dropdown_menu">
            <li><a href="inscription.php">Inscription</a></li>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="planning.php">Planning</a></li>
            <li><a href="reservation-form.php" class="action_btn">Réserver</a></li>
        </div>
    </header>

    <section class="banner">
        <div class="card-container">
            <div class="card-img1">
                <!-- image -->
            </div>
            
            <div class="card-content">
                <h3>Reservation</h3>

                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-row">
                        <input type="text" placeholder="Titre" name="titre" required>
                    </div>

                    <div class="form-row">
                        <textarea name="description" placeholder="Description" required></textarea>
                    </div>

                    <div class="form-row">
                    <label for="date_debut">Date de début:</label>
                        <input type="datetime-local" name="date_debut" required>
                    </div>

                    <div class="form-row">
                        <label for="date_fin">Date de fin:</label>
                        <input type="datetime-local" name="date_fin" required>
                    </div>

                    <div class="form-row">
                        <input type="submit" value="Réserver">
                    </div>
                    
                </form>
            </div>
        </div>
        
        <!-- Display the error message if it exists -->
        <?php if (!empty($error_message)) : ?>
            <span class='error-message'><?php echo $error_message; ?></span>
        <?php endif; ?>
        <?php if (!empty($sucess_message)) : ?>
            <span class='sucess-message'><?php echo $sucess_message; ?></span>
        <?php endif; ?>
    
    </section>

    

    <script>
        const toggleBtn = document.querySelector('.toggle_btn')
        const toggleBtnIcon = document.querySelector('.toggle_btn i')
        const dropDownMenu = document.querySelector('.dropdown_menu')

        toggleBtn.onclick = function () {
            dropDownMenu.classList.toggle('open')
            const isOpen = dropDownMenu.classList.contains('open')

            toggleBtnIcon.classList = isOpen
            ?'fa-solid fa-xmark'
            :'fa-solid fa-bars'
        }
    </script>
</body>
</html>




