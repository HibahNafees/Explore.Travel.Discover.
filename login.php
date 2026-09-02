<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $email = $_POST['email'];
    $password = $_POST['password'];
    $errors = array();

    //Database Connection
    $con = new mysqli('localhost', 'root', '', 'register');
    if ($con->connect_error) {
        die("Failed to connect : " . $con->connect_error);
    } else {
        $stmt = $con->prepare("select * from registration where email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt_result = $stmt->get_result();
        if ($stmt_result->num_rows > 0) {
            $data = $stmt_result->fetch_assoc();
            if ($data['password'] === $password) {
                header("Location: login-success.php");
                // echo "<h2>Login Successful</h2>";
            } else if ($data['password'] != $password) {
                echo "<h2>Invalid Email or password</h2>";
            }
        } else {
            echo "<h2>Invalid Email or password</h2>";
        }
    }
    //login

    ?>
</body>

</html>