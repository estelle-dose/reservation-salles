<?php
session_start();

// Define an empty variable for the error message
$error_message = "";

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['id_utilisateur'])) {
    // Redirection vers la page de profil ou toute autre page protégée
    header("Location: profil.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $login = $_POST['login'];
    $user_password = $_POST['password'];

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

    // Préparer et exécuter la requête de vérification de l'utilisateur en utilisant une requête préparée
    $sql = "SELECT * FROM utilisateurs WHERE login=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login, $user_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // L'utilisateur existe, authentification réussie

        // Récupérer l'ID de l'utilisateur
        $row = $result->fetch_assoc();
        $idUtilisateur = $row['id'];

        // Créer les variables de session
        $_SESSION['id_utilisateur'] = $idUtilisateur;

        // Redirection vers la page de profil ou toute autre page protégée
        header("Location: profil.php");
        exit();
    } else {
        $error_message = "Identifiant ou mot de passe incorrect.";
    }


    

    // Fermer la connexion à la base de données
    $stmt->close();
    $conn->close();
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
        <title>Connexion</title>
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
            <div class="card-img2">
                <!-- image -->
            </div>

            <div class="card-content">
                <h3>Connexion</h3
                >
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                    <div class="form-row">
                        <label for="login">Login:</label>
                        <input type="text" name="login" required>
                    </div>

                    <div class="form-row">
                        <label for="password">Mot de passe:</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-row">
                        <input type="submit" value="Se connecter">
                    </div>

                </form>
            </div>
        </div>

        <!-- Display the error message if it exists -->
        <?php if (!empty($error_message)) : ?>
            <span class='error-message'><?php echo $error_message; ?></span>
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


