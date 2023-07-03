<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    // Redirection vers la page de connexion
    header("Location: connexion.php");
    exit();
}

// Vérifier si l'id de réservation est spécifié dans l'URL
if (!isset($_GET['id'])) {
    echo "L'id de réservation n'est pas spécifié.";
    exit();
}

// Récupérer l'id de réservation depuis l'URL et s'assurer qu'il est numérique
$idReservation = intval($_GET['id']);

// Récupérer les informations de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservationsalles";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier les erreurs de connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Préparer la requête avec une instruction préparée
$sql = "SELECT r.titre, r.description, r.debut, r.fin, u.login
        FROM reservations r
        INNER JOIN utilisateurs u ON r.id_utilisateur = u.id
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idReservation);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si la réservation existe
if ($result->num_rows != 1) {
    echo "La réservation n'existe pas.";
    exit();
}

// Récupérer les informations de la réservation
$row = $result->fetch_assoc();
$titre = $row['titre'];
$description = $row['description'];
$debut = $row['debut'];
$fin = $row['fin'];
$login = $row['login'];

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
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
        <title>Réservation - <?php echo $titre; ?></title>
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
            <div class="card-img5">
                <!-- image -->
            </div>

            <div class="card-content">
                <h3>Réservation - <?php echo $titre; ?></h3>
        
                <div class="form-row">
                    <p><strong>Créateur:</strong> <?php echo $login; ?></p>
                </div>
                <div class="form-row">
                    <p><strong>Description:</strong> <?php echo $description; ?></p>
                </div>
                <div class="form-row">
                    <p><strong>Heure de début:</strong> <?php echo $debut; ?></p>
                </div>
                <div class="form-row">
                    <p><strong>Heure de fin:</strong> <?php echo $fin; ?></p>
                </div>
            </div>
        </div>
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







