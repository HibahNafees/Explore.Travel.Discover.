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

    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);

    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    $mysqli = require __DIR__ . "/database.php";

    $sql = "UPDATE registration 
    SET reset_token_hash = ?,
    reset_token_expires_at = ?
    WHERE email = ?";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("sss", $token_hash, $expiry, $email);

    $stmt->execute();

    if ($mysqli->affected_rows) {
        require __DIR__ . "/mailer.php";

        $mail->setFrom("cse.160320733002@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END

        Click <a href="http://localhost:3000/reset-password.php?token=$token">here</a>
        to reset your password.
        END;

        $mail->send();

        try {

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }

    }

    echo "Message sent, please check your inbox.";

    ?>
</body>

</html>