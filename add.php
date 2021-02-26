<?php 
    require_once "pdo.php";
    session_start();
    if ( ! (isset($_SESSION['name']) && isset($_SESSION['user_id']) ) ) {
        die('ACCESS DENIED');
    }

    require_once "util.php";

    if ( isset($_POST['cancel']) ) {
        header('Location: index.php');
        return;
    }
    
    if( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
        if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['summary'])<1 || strlen($_POST['headline'])<1){
            $_SESSION['failure'] = "<div style='color: red;'>All fields are required</div>";
            header("Location: add.php");
            return;
        }
        elseif (strpos($_POST['email'],'@')===false) {
            $_SESSION['failure'] = "<div style='color: red;'>Email must contain @</div>";
            header("Location: add.php");
            return;
        }
        elseif(validatePos() != true){
            $_SESSION['failure'] = validatePos();
            header("Location: add.php");
            return;
        }
        else {
            $stmt = $pdo->prepare('INSERT INTO Profile
                (user_id, first_name, last_name, email, headline, summary)
                VALUES ( :uid, :fn, :ln, :em, :he, :su)');
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            $profile_id = $pdo->lastInsertId();
            $rank = 1;
            for($i=1; $i<=9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;

                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];
                $stmt = $pdo->prepare('INSERT INTO position
                        (profile_id, rank, year, description)
                        VALUES ( :pid, :rank, :year, :desc)');

                $stmt->execute(array(
                        ':pid' => $profile_id,
                        ':rank' => $rank,
                        ':year' => $year,
                        ':desc' => $desc)
                );
                $rank++;
            }
            $rank = 1;
            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['edu_year' . $i])) continue;
                if (!isset($_POST['edu_school' . $i])) continue;

                $edu_year = $_POST['edu_year' . $i];
                $edu_school = $_POST['edu_school' . $i];

                $stmt = $pdo->prepare("SELECT * FROM Institution where name = :xyz");
                $stmt->execute(array(":xyz" => $edu_school));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $institution_id = $row['institution_id'];
                } else {
                    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');

                    $stmt->execute(array(
                        ':name' => $edu_school,
                    ));
                    $institution_id = $pdo->lastInsertId();
                }

                $stmt = $pdo->prepare('INSERT INTO Education
                    (profile_id, institution_id, year, rank)
                    VALUES ( :pid, :institution, :edu_year, :rank)');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':institution' => $institution_id,
                    ':edu_year' => $edu_year,
                    ':rank' => $rank)
                );
                $rank++;
            }
            $_SESSION['success'] = "Record Added";
            header("Location: index.php");
            return;
        }
    }
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
        <h1> Adding Profile For
        <?php if(isset($_SESSION['name']))
                echo htmlentities($_SESSION['name']);
        ?>
        </h1>
        <?php 
            if(isset($_SESSION['failure'])){
                echo $_SESSION['failure'];
                unset($_SESSION['failure']);
            }
        ?>
        <form method='post'>
        <label for='fn'>First Name:</label>
        <input id='fn' name='first_name' type='text'><br>
        <label for='ln'>Last Name:</label>
        <input id='ln' type='text' name='last_name'><br>
        <label for='em'>Email:</label>
        <input id='em' name='email' type='text'><br>
        <label for='hl'>Headline</label><br>
        <input id='hl' name='headline' type='text'><br>
        <label for='sm'>Summary:</label><br>
        <textarea name="summary" id="sm" rows="8" cols="80"></textarea><br>
        <label for="ed">Education: </label>
        <input type="submit" id="addEdu" value="+">
        <div id="edu_fields"></div>
        <label for="ps">Position:  </label>
        <input type="submit" id="addPos" value="+">
        <div id="position_fields"></div>
        <input type='submit' value='Add'>
        <input type='submit' name='cancel' value='Cancel'>
        </form>
    </div>
    </body>
    <script>
        countPos = 0;
        countEdu = 0;
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