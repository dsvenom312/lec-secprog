<?php
    require __DIR__ . "/../config/database.php";

    $conn = new mysqli(
        $config["servername"],
        $config["username"],
        $config["password"],
        $config["dbname"]
    );

