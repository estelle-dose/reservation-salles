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

// Récupérer l'ID de l'utilisateur connecté
$idUtilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur depuis la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservationsalles";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier les erreurs de connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Récupérer le login actuel de l'utilisateur
$sql = "SELECT login FROM utilisateurs WHERE id=$idUtilisateur";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $loginActuel = $row['login'];
} else {
    $error_message = "Erreur lors de la récupération des informations de l'utilisateur.";
    exit();
}

// Assigner le login actuel à la variable de session
$_SESSION['login'] = $loginActuel;

// Vérifier si le formulaire de modification est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les nouvelles données du formulaire
    $nouveauLogin = $_POST['nouveau_login'];
    $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'];
    $confirmNouveauMotDePasse = $_POST['confirm_nouveau_mot_de_passe'];

    // Vérifier si les mots de passe correspondent
    if ($nouveauMotDePasse !== $confirmNouveauMotDePasse) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } else {
        // Préparer et exécuter la requête de mise à jour du profil
        $sql = "UPDATE utilisateurs SET login='$nouveauLogin', password='$nouveauMotDePasse' WHERE id=$idUtilisateur";

        if ($conn->query($sql) === true) {
            $sucess_message = "Profil mis à jour avec succès.";

            // Mettre à jour le login actuel de l'utilisateur dans la variable de session
            $_SESSION['login'] = $nouveauLogin;

            // Redirection vers la page de connexion
            header("Location: connexion.php");
            exit();
        } else {
            $error_message = "Erreur lors de la mise à jour du profil : " . $conn->error;
        }
    }
}

// Fermer la connexion à la base de données
$conn->close();

// Déconnexion de la session
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: connexion.php");
    exit();
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
        <title>Profil</title>
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
            <div class="card-img4">
                <!-- image -->
            </div>

            <div class="card-content">
                <h3>Profil</h3>
                
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                
                <div class="form-row">
                    <p>Connecté en tant que: <?php echo $loginActuel; ?></p>
                </div>

                <div class="form-row">
                    <label for="login">Nouveau login:</label>
                    <input type="text" name="nouveau_login" required value="<?php echo $loginActuel; ?>">
                </div>
                
                <div class="form-row">
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe:</label>
                    <input type="password" name="nouveau_mot_de_passe" required>
                </div>

                <div class="form-row">
                    <label for="confirm_nouveau_mot_de_passe">Confirmez le nouveau mot de passe:</label>
                    <input type="password" name="confirm_nouveau_mot_de_passe" required>
                </div>

                <div class="form-row">
                    <input type="submit" value="Modifier le profil">
                </div>
                </form>

                <!-- Bouton de déconnexion -->
                <form method="GET" action="profil.php">
                    <div class="form-row">
                        <input type="hidden" name="logout" value="true">
                        <input type="submit" value="Déconnexion">
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

