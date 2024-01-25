<?php
require_once 'config.php';

$playername = $_POST['playername'];
$score = $_POST['score'];
$duration = $_POST['duration'];

$conn->query("INSERT INTO players( playername, score, duration, date) VALUES ('$playername','$score','$duration',NOW())")or die($conn->error);