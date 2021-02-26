<?php 
    require_once "pdo.php";
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Shaurabh Tiwari</title>
        <link href="starter.css" rel="stylesheet">
        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
            integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
            crossorigin="anonymous">

        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
            integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
            crossorigin="anonymous">

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

        <script
            src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>

        <script
            src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
            integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
            crossorigin="anonymous"></script>
        <script
          src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
          integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
          crossorigin="anonymous"></script>
    </head>
    <body>
    <div class="container">
        <h1>Shaurabh Tiwari's Resume Registry</h1>
        <?php
            if(isset($_SESSION['error'])){
                echo ("<p style='color: red;'>".$_SESSION['error']."</p>");
                unset($_SESSION['error']);
            }

            if(isset($_SESSION['success'])){
                echo ("<p style='color: green;'>".$_SESSION['success']."</p>");
                unset($_SESSION['success']);
            }
        ?>
        <?php 
        
            if(isset($_SESSION['name']) && isset($_SESSION['user_id'])){
                echo "<div><a href='logout.php'>Logout</a></div><br>";
                $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id, user_id FROM profile");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(sizeof($rows)>0){
                    echo "<table style='border: 1px solid black;'><thead><tr><th style='border: 1px solid black;'>Name</th><th style='border: 1px solid black;'>Headline</th><th style='border: 1px solid black;'>Actionr</th></tr></thead>";
                    foreach ($rows as $row){
                        echo '<tbody><tr><td style="border: 1px solid black;"><a href="view.php?profile_id='.$row['profile_id'].'">';
                        echo htmlentities($row['first_name']) ." ". htmlentities($row['last_name']) ."</a></td><td style='border: 1px solid black;'>";
                        echo htmlentities($row['headline'])."</td><td style='border: 1px solid black;'>";
                        if($row['user_id']==$_SESSION['user_id']){
                        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
                        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                        }
                        echo("</td></tbody>");
                    }
                    echo "</table>";
                    echo "<br><div><a href='add.php'>Add New Entry</a></div><br>";
                }
                else{
                    echo "<div>No Rows Found</div>";
                    echo "<div><a href='add.php'>Add New Entry</a></div><br>";
                }
            }
            else{
                echo "<div><a href='login.php'>Please log in</a></div>";
                $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id, user_id FROM profile");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(sizeof($rows)>0){
                    echo "<table style='border: 1px solid black;'><thead><tr><th style='border: 1px solid black;'>Name</th><th style='border: 1px solid black;'>Headline</th></tr></thead>";
                    foreach ($rows as $row){
                        echo '<tbody><tr><td style="border: 1px solid black;"><a href="view.php?profile_id='.$row['profile_id'].'">';
                        echo htmlentities($row['first_name']) ." ". htmlentities($row['last_name']) ."</a></td><td style='border: 1px solid black;'>";
                        echo htmlentities($row['headline']);
                        echo("</td></tbody>");
                    }
                    echo "</table>";
                }
            }
        ?>
    </div>
    </body>
</html>