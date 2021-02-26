<?php // Do not put any HTML above this line

require_once "pdo.php";
session_start();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    } elseif (strpos($_POST['email'],'@')===false) {
        $_SESSION['error'] = "Email must have an at-sign sign (@)";
        header("Location: login.php");
        return;
    }
}

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( $row !== false ) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        // Redirect the browser to index.php
        header("Location: index.php");
        return;
    }else {
        $_SESSION['error'] = "Incorrect Password or Email";
        header("Location: login.php");
        return;
    }
}

// Fall through into the View
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
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the three character name of the 
programming language used in this class (all lower case) 
followed by 123. -->
</p>
</div>
</body>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('nam').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+"  pw="+pw);
        if (pw == null || pw == ""|| addr==null || addr=="") {
            alert("Both fields must be filled out");
            return false;
        }else if(! addr.includes("@")){
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</html>