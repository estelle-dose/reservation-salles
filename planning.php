<?php
session_start();

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

// Fonction pour formater les dates et heures
function formatDateTime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

// Récupérer les réservations de la semaine en cours (du lundi au vendredi)
$aujourdHui = date("Y-m-d");
$jourSemaine = date("N", strtotime($aujourdHui));
$debutSemaine = date("Y-m-d", strtotime("-" . ($jourSemaine - 1) . " days", strtotime($aujourdHui)));
$finSemaine = date("Y-m-d", strtotime("+" . (5 - $jourSemaine) . " days", strtotime($aujourdHui)));

$sql = "SELECT r.id, r.titre, r.debut, r.fin, u.login
        FROM reservations r
        INNER JOIN utilisateurs u ON r.id_utilisateur = u.id
        WHERE r.debut >= '$debutSemaine 08:00:00' AND r.fin <= '$finSemaine 19:00:00'
        ORDER BY r.debut";

$result = $conn->query($sql);

// Créer un tableau vide pour le planning
$planning = [];

// Initialiser le tableau avec les jours de la semaine
$joursSemaine = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"];

foreach ($joursSemaine as $jour) {
    $planning[$jour] = [];
}

// Remplir le tableau avec les réservations
while ($row = $result->fetch_assoc()) {
    $idReservation = $row['id'];
    $titre = $row['titre'];
    $debut = $row['debut'];
    $fin = $row['fin'];
    $login = $row['login'];

    // Récupérer le jour de la réservation
    $jourReservation = date("N", strtotime($debut));

    // Formater les dates et heures
    $debutFormatted = formatDateTime($debut);
    $finFormatted = formatDateTime($fin);

    // Ajouter la réservation au planning
    $planning[$joursSemaine[$jourReservation - 1]][] = [
        'id' => $idReservation,
        'titre' => $titre,
        'debut' => $debutFormatted,
        'fin' => $finFormatted,
        'login' => $login
    ];
}

// Fermer la connexion à la base de données
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
        <title>Planning</title>
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

    <main class="table">
        <section class="table__header">
            <h1>Planning de la semaine</h1>
        </section>
        
        <section class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>8h - 9h</th>
                        <th>9h - 10h</th>
                        <th>10h - 11h</th>
                        <th>11h - 12h</th>
                        <th>12h - 13h</th>
                        <th>13h - 14h</th>
                        <th>14h - 15h</th>
                        <th>15h - 16h</th>
                        <th>16h - 17h</th>
                        <th>17h - 18h</th>
                        <th>18h - 19h</th>
                    </tr>
                </thead>
        
                <tbody>
                <?php foreach ($joursSemaine as $jour): ?>
                    <tr>
                        <td><strong><?php echo $jour; ?></strong></td>
                        <?php for ($heure = 8; $heure <= 18; $heure++): ?>
                            <td>
                                <?php if (isset($planning[$jour])): ?>
                                    <?php foreach ($planning[$jour] as $reservation): ?>
                                        <?php
                                        $debutHeure = (int) substr($reservation['debut'], 11, 2);
                                        $finHeure = (int) substr($reservation['fin'], 11, 2);

                                        if ($debutHeure <= $heure && $finHeure > $heure):
                                            ?>
                                            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                                                <a href="reservation.php?id=<?php echo $reservation['id']; ?>">
                                            <?php endif; ?>
                                            <?php echo $reservation['login']; ?><br>
                                            <?php echo $reservation['titre']; ?>
                                            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

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
