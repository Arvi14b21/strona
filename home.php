<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Witaj na stronie głównej
    <form actionn="home.php" method="post">
        <input type="submit" name="logout" value="Wyloguj się">
    </form>
</body>
</html>


<?php
    session_start();
    if (!isset($_SESSION["login"])) {
        header("Location: index.php");
        exit();
    }

    if(isset($_POST["logout"])){
        session_destroy();
        header("Location: index.php");
        exit();
    }

    // Dodawanie zadania
    if(isset($_POST["add_task"]) && !empty($_POST["task_name"]) && !empty($_POST["task_date"])) {
        if (!isset($_SESSION["tasks"])) $_SESSION["tasks"] = [];
        $_SESSION["tasks"][] = [
            "name" => htmlspecialchars($_POST["task_name"]),
            "date" => htmlspecialchars($_POST["task_date"]),
            "done" => false,
            "subtasks" => []
        ];
    }

    // Usuwanie zadania
    if(isset($_POST["delete_task"])) {
        $id = intval($_POST["delete_task"]);
        if (isset($_SESSION["tasks"][$id])) {
            array_splice($_SESSION["tasks"], $id, 1);
        }
    }

    // Oznaczanie jako wykonane
    if(isset($_POST["done_task"])) {
        $id = intval($_POST["done_task"]);
        if (isset($_SESSION["tasks"][$id])) {
            $_SESSION["tasks"][$id]["done"] = true;
        }
    }

    // Dodawanie podzadania
    if(isset($_POST["add_subtask"]) && isset($_POST["parent_id"]) && !empty($_POST["subtask_name"])) {
        $parent_id = intval($_POST["parent_id"]);
        if (isset($_SESSION["tasks"][$parent_id])) {
            if (!isset($_SESSION["tasks"][$parent_id]["subtasks"])) {
                $_SESSION["tasks"][$parent_id]["subtasks"] = [];
            }
            $_SESSION["tasks"][$parent_id]["subtasks"][] = [
                "name" => htmlspecialchars($_POST["subtask_name"]),
                "done" => false
            ];
        }
    }

    // Oznaczanie podzadania jako wykonane
    if(isset($_POST["done_subtask"]) && isset($_POST["parent_id"])) {
        $parent_id = intval($_POST["parent_id"]);
        $sub_id = intval($_POST["done_subtask"]);
        if (isset($_SESSION["tasks"][$parent_id]["subtasks"][$sub_id])) {
            $_SESSION["tasks"][$parent_id]["subtasks"][$sub_id]["done"] = true;
        }
    }

    // Usuwanie podzadania
    if(isset($_POST["delete_subtask"]) && isset($_POST["parent_id"])) {
        $parent_id = intval($_POST["parent_id"]);
        $sub_id = intval($_POST["delete_subtask"]);
        if (isset($_SESSION["tasks"][$parent_id]["subtasks"][$sub_id])) {
            array_splice($_SESSION["tasks"][$parent_id]["subtasks"], $sub_id, 1);
        }
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <div class="top-bar">
        Zalogowano jako: <b style="margin: 0 8px;"><?php echo htmlspecialchars($_SESSION["login"]); ?></b>
        <form class="logout-form" action="home.php" method="post" style="display:inline;">
            <input type="submit" name="logout" value="Wyloguj się">
        </form>
    </div>
    <div class="container">
        <h2>Twoje zadania</h2>
        <form action="home.php" method="post">
            <input type="text" name="task_name" placeholder="Nazwa zadania" required>
            <input type="date" name="task_date" required>
            <input type="submit" name="add_task" value="Dodaj zadanie">
        </form>
        <ul class="task-list">
            <?php
            if (isset($_SESSION["tasks"]) && count($_SESSION["tasks"]) > 0) {
                foreach ($_SESSION["tasks"] as $id => $task) {
                    echo '<li class="task-item">';
                    echo '<div>';
                    echo '<span class="'.($task["done"] ? 'task-done' : '').'">'.htmlspecialchars($task["name"]).'</span>';
                    echo '<span class="task-date">na: '.htmlspecialchars($task["date"]).'</span>';
                    echo '</div>';
                    echo '<div class="task-actions">';
                    if (!$task["done"]) {
                        echo '<form action="home.php" method="post" style="display:inline;">';
                        echo '<button type="submit" name="done_task" value="'.$id.'">Oznacz jako wykonane</button>';
                        echo '</form>';
                    } else {
                        echo '<span style="color:#43b581;margin-left:10px;">Wykonane</span>';
                    }
                    echo '<form action="home.php" method="post" style="display:inline;">';
                    echo '<button type="submit" name="delete_task" value="'.$id.'" class="delete-btn">Usuń</button>';
                    echo '</form>';
                    echo '</div>';

                    // Wyświetlanie podzadań
                    if (isset($task["subtasks"]) && count($task["subtasks"]) > 0) {
                        echo '<ul class="subtasks-list">';
                        foreach ($task["subtasks"] as $sub_id => $subtask) {
                            echo '<li>';
                            echo '<span class="'.($subtask["done"] ? 'subtask-done' : '').'">'.htmlspecialchars($subtask["name"]).'</span>';
                            if (!$subtask["done"]) {
                                echo '<form action="home.php" method="post" style="display:inline;">';
                                echo '<input type="hidden" name="parent_id" value="'.$id.'">';
                                echo '<button type="submit" name="done_subtask" value="'.$sub_id.'" class="subtask-btn">Oznacz jako wykonane</button>';
                                echo '</form>';
                            } else {
                                echo '<span style="color:#43b581;margin-left:10px;">Wykonane</span>';
                            }
                            echo '<form action="home.php" method="post" style="display:inline;">';
                            echo '<input type="hidden" name="parent_id" value="'.$id.'">';
                            echo '<button type="submit" name="delete_subtask" value="'.$sub_id.'" class="delete-btn">Usuń</button>';
                            echo '</form>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                    // Formularz dodawania podzadania
                    echo '<form class="subtask-form" action="home.php" method="post">';
                    echo '<input type="hidden" name="parent_id" value="'.$id.'">';
                    echo '<input type="text" name="subtask_name" placeholder="Dodaj podzadanie" required>';
                    echo '<button type="submit" name="add_subtask" class="subtask-btn">Dodaj</button>';
                    echo '</form>';

                    echo '</li>';
                }
            } else {
                echo '<li>Brak zadań.</li>';
            }
            ?>
        </ul>
    </div>
</body>
</html>