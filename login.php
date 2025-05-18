<?php
session_start();
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST["Hasło"];

    $sql = "SELECT * FROM users WHERE nazwa_uzytkownika = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["haslo"])) {
            $_SESSION["login"] = $user["nazwa_uzytkownika"];
            header("Location: home.php");
            exit();
        } else {
            echo "Nieprawidłowe hasło.";
        }
    } else {
        echo "Użytkownik nie istnieje.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rejstracja2.css">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<form method="post" action="">
    <label>Nazwa użytkownika:</label><br>
    <input type="text" name="login"><br>
    <label>Hasło:</label><br>
    <input type="password" name="Hasło"><br>
    <input type="submit" value="Zaloguj się">
</form>
