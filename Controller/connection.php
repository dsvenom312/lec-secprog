<?php
    require "../config/database.php";

    $conn = new mysqli(
        $config["servername"],
        $config["username"],
        $config["password"],
        $config["dbname"]
    );

