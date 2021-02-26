<?php
    require_once "pdo.php";
    session_start();

    if(isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }

    if(isset($_POST['profile_id']) && isset($_POST['delete'])){
        $sql = "DELETE FROM profile WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':profile_id' => $_POST['profile_id']));
        $_SESSION['success'] = "Profile Deleted";
        header("Location: index.php");
        return;
    }

    if(! isset($_GET['profile_id'])){
        $_SESSION['error'] = 'Bad value for id';
        header("Location: index.php");
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for id';
        header( 'Location: index.php' ) ;
        return;
    }

    $fn = $row['first_name'];
    $ln = $row['last_name'];
    $id = $row['profile_id'];
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
        <h1>Deleting Profile</h1>
        <p>First Name: <?= $fn ?></p>
        <p>Last Name: <?= $ln ?></p>
        <form method='post'>
        <input type='hidden' name='profile_id' value="<?= $id ?>">
        <input type='submit' value='Delete' name='delete'>
        <input type="submit" value="Cancel" name="cancel">
        </form>
    </div>
    </body>
</html>