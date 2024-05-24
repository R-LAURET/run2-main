<?php

require_once('models/Database.php');
require_once('controlleur/Controlleur.php');


$database = new Database();

$controller = new Controller($database);

$controller->handleRequest();

?>
