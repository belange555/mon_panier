<?php

require_once "includes/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (empty($email) || empty($mot_de_passe)) {

        $message = "Tous les champs sont obligatoires.";

    } else {
//ici nous vérifierons si l'utilisateur existe dans la base de données
        $stmt = $connexion->prepare("
        SELECT Utilisateur_id, prenom
        FROM utilisateur 
        WHERE email = ? AND mot_de_passe = ?");

        $stmt->bind_param("ss", $email, $mot_de_passe);

        $stmt->execute();

        $resultat = $stmt->get_result();

        if ($resultat->num_rows > 0) {

        session_start();

        $utilisateur = $resultat->fetch_assoc();

        $_SESSION["Utilisateur_id"] = $utilisateur["Utilisateur_id"];
        $_SESSION["prenom"] = $utilisateur["prenom"];

        header("Location: dashboard.php");

        exit();

        } else {

            $message = "Email ou mot de passe incorrect.";

        } //ferme le if ($resultat->num_rows > 0)

    } //ferme le else de la vérification des champs vides
}
?>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Mon Panier</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>


<body class="bg-light">

<div class="container">

<div class="row justify-content-center align-items-center vh-100">

<div class="col-md-5">

<div class="card shadow">

<div class="card-body p-4">


<?php
if ($message != "") {
?>
<div class="alert alert-danger">
    <?php echo $message; ?>
</div>
<?php
}
?>

<h2 class="text-center mb-4">

🛒 Mon Panier

</h2>

<p class="text-center text-muted">

Connectez-vous à votre compte

</p>

<form action="connexion.php" method="POST">

    <div class="mb-3">

<label class="form-label">

Adresse e-mail

</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

    <div class="mb-3">

<label class="form-label">

Mot de passe

</label>

<input
type="password"
name="mot_de_passe"
class="form-control"
required>

</div>

   <button
type="submit"
class="btn btn-primary w-100">

Se connecter

</button>

<div class="text-center mt-3">

Vous n'avez pas de compte ?

<br>

<a href="inscription.php">

Créer un compte

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
