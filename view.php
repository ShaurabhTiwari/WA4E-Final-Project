<?php 

require_once "pdo.php";
session_start();

if(! isset($_GET['profile_id'])){
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
}
$sql = "SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :profile_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for id';
    header( 'Location: index.php' ) ;
    return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);

$stmt1 = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
$stmt1->execute(array(":xyz" => $_GET['profile_id']));
$rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM Education join Institution on Education.institution_id = Institution.institution_id where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$rowsofedu = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <h1>Profile Information</h1>
        <p>First Name: <?= $fn ?></p>
        <p>Last Name: <?= $ln ?></p>
        <p>Email: <?= $em ?></p>
        <p>Headline:<br><?= $hl ?></p>
        <p>Summary:<br><?= $sm ?></p>
        <p>Education: <br/><ul>
            <?php
                foreach ($rowsofedu as $row) {
                    echo('<li>'.$row['year'].':'.$row['name'].'</li>');
                } 
            ?>
        </ul></p>
        <p>Position: <br/><ul>
            <?php
                foreach ($rows as $row) {
                echo('<li>'.$row['year'].':'.$row['description'].'</li>');
                } 
            ?>
        </ul></p>
        <p><a href="index.php">Done</a></p>
    </div>
    </body>
</html>