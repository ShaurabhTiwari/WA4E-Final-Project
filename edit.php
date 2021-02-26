<?php
    require_once "pdo.php";
    session_start();

    if (!isset($_SESSION['name'])) {
        die('Not logged in');
    }

    if (isset($_POST['cancel'])){
        header('Location: index.php');
        return;
    }

    require_once "util.php";

    if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['summary']) && isset($_POST['profile_id']) && isset($_POST['headline']) ) {

        $msg = validateProfile();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        }

        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        }

        $msg = validateEdu();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        }

        $sql = "UPDATE profile SET first_name = :first_name,
                last_name = :last_name, email= :email, headline = :headline, summary = :summary
                WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':summary' => $_POST['summary'],
            ':headline' => $_POST['headline'],
            ':profile_id' => $_POST['profile_id']
        ));
                
        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

        insertPosition($pdo, $_REQUEST['profile_id']);

        $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

        insertEducation($pdo, $_REQUEST['profile_id']);
            
        $_SESSION['success'] = 'Profile edited';
        header( 'Location: index.php' ) ;
        return;
    }

    if ( ! isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Bad value for id";
        header('Location: index.php');
        return;
    }

    $profile = loadPro($pdo, $_REQUEST['profile_id']);
    $positions = loadPos($pdo, $_REQUEST['profile_id']);
    $schools = loadEdu($pdo, $_REQUEST['profile_id']);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Shaurabh Tiwari's Profile Edit</title>
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
    </head>
    <body>
    <div class="container">
        <h1>Editing Profile For 
        <?php if(isset($_SESSION['name']))
                echo htmlentities($_SESSION['name']);
        ?>
        </h1>
        <?php
            if(isset($_SESSION['error'])){
                echo ("<p style='color: red;'>".$_SESSION['error']."</p>");
                unset($_SESSION['error']);
            }
        ?>

        <form method='post'>
        <label for='f_n'>First Name:</label>
        <input id='f_n' name='first_name' type='text' value="<?= $profile['first_name'] ?>"><br>
        <label for='l_n'>Last Name:</label>
        <input id='l_n' type='text' name='last_name' value="<?= $profile['last_name'] ?>"><br>
        <label for='em_'>Email:</label>
        <input id='em_' name='email' type='text' value="<?= $profile['email'] ?>"><br>
        <label for='h_l'>Headline</label><br>
        <input id='h_l' name='headline' type='text' value="<?= $profile['headline'] ?>"><br>
        <label for='sm_'>Summary:</label><br>
        <textarea name="summary" id="sm_" rows="8" cols="80"><?= $profile['summary'] ?></textarea><br>

        <?php

            $countEdu = 0;

            echo('<p>Education: <input type="submit" id="addEdu" value="+">' . "\n");
            echo('<div id="edu_fields">');
            if (count($schools) > 0) {
                foreach ($schools as $school) {
                    $countEdu++;
                    echo('<div id="edu' . $countEdu . '">');
                    echo
                        '<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . $school['year'] . '">
                        <input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;\"></p>
                        <p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" 
                        value="' . htmlentities($school['name']) . '" />';
                    echo "\n</div>\n";
                }
            }
            echo "</div></p>\n";

            $countPos = 0;

            echo('<p>Position: <input type="submit" id="addPos" value="+">' . "\n");
            echo('<div id="position_fields">');
            if (count($positions) > 0) {
                foreach ($positions as $position) {
                    $countEdu++;
                    echo('<div id="position id="position' . $countPos . '">');
                    echo
                        '<br>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '">
                        <input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';
                    echo '<textarea name="desc' . $countPos . '"rows="8" cols="80">' . "\n";
                    echo htmlentities($position['description']) . "\n";
                    echo "\n</textarea>\n</div>\n";
                }
            }
        ?>
        </div>

        <input type="hidden" name="profile_id" value="<?= $profile['profile_id'] ?>">
        <input type='submit' value='Save'>
        <input type='submit' name='cancel' value='Cancel'>
        </form>
    </div>
    </body>
    <script>
            countPos = <?= $countPos ?>;
            countEdu = <?= $countEdu ?>;
            $(document).ready(function () {
                window.console && console.log('Document ready called');
                $('#addPos').click(function (event) {
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
                        <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
                        <input type="button" value="-" \
                        onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                        <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
                        </div>'
                    );
                });
                $('#addEdu').click(function (event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding education " + countEdu);

                    $('#edu_fields').append(
                        '<div id="edu' + countEdu + '"> \
                        <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
                        <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
                        <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
                        </p></div>'
                    );

                    $('.school').autocomplete({
                    source: "school.php"
                    });
                });
            });
        </script>
</html>