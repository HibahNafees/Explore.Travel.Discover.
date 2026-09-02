<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (strlen($_POST["password"]) < 8) {
        die("Password must be at least 8 characters");
    }
    
    if (!preg_match("/[a-z]/i", $_POST["password"])) {
        die("Password must contain at least one letter");
    }
    
    if (!preg_match("/[0-9]/", $_POST["password"])) {
        die("Password must contain at least one number");
    }
    
    if ($_POST["password"] !== $_POST["password_confirmation"]) {
        die("Passwords must match");
    }
    
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $number = $_POST['number'];

    //Database Connection
    $conn = new mysqli('localhost', 'root', '', 'register');

    //REGISTRATION
    if ($conn->connect_error) {
        die('Connection Failed : ' . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("insert into registration(firstName, lastName, gender, email, password, number)
     values(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $password, $number);
        $stmt->execute();
        echo "registration successful...";
        $stmt->close();
        $conn->close();
    }

    ?>
</body>

</html>