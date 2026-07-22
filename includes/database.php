<?php

$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "";
$baseDeDonnees = "mon_panier";

$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}

?>