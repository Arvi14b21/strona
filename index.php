<?php
    include("database.php");
    session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rejstracja2.css">
    <title>Document</title>
</head>
<body>
   <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label>Nazwa użytkownika:</label><br>
        <input type="text" name="login"><br>

        <label>Hasło:</label><br>
        <input type="password" name="Hasło"><br>
        <div class="info">
            Hasło musi mieć co najmniej 8 znaków, zawierać dużą i małą literę oraz znak specjalny.
        </div>
        <input type="submit" name="submit" value="Zarejestruj się"><br>
        Masz już konto?<br>
        <a href="login.php" class="button-link">Zaloguj się!</a>
    </form>
</body>
</html>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nazwa = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
        $hasło = filter_input(INPUT_POST, "Hasło", FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($nazwa)){
            echo"Brakuje loginu";
        }
        elseif(empty($hasło)){
            echo"Brakuje hasła";
        }
        elseif(strlen($hasło) < 8 ||
            !preg_match('/[A-Z]/', $hasło) ||      
            !preg_match('/[a-z]/', $hasło) ||  
            !preg_match('/[\W_]/', $hasło)     
        ){
            //echo "Hasło musi mieć co najmniej 8 znaków, zawierać dużą i małą literę oraz znak specjalny.";
        }
        
        else{

            $hash = password_hash($hasło, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (nazwa_uzytkownika, haslo)
            VALUES ('$nazwa', '$hash')";

            try{
                mysqli_query($conn, $sql);
                //echo"Jesteś zalogowany";
            }
            catch(mysqli_sql_exception){
                echo"Podana nazwa użytkownika już istnieje";
            }
        }
    mysqli_close($conn);
    }
    

/*
    if(isset($_POST["login"])){

        if(!empty($_POST["login"]) &&
           !empty($_POST["Hasło"])){
            
            $_SESSION["login"] = $_POST["login"];
            $_SESSION["Hasło"] = $_POST["Hasło"];

            header("Location: home.php");
    }
}
*/

?>