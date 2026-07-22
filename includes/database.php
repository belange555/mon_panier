<?php

$serveur = "mysql-jnbelange.alwaysdata.net";
$utilisateur = "jnbelange";
$motDePasse = "Gtmbobine@2026";
$baseDeDonnees = "jnbelange_mon_panier";

$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}

$connexion->set_charset("utf8mb4");