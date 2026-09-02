<?php 

$token = $_GET['token'];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM registration WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/reset-pass.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="reset-pass-container">
        <form method="post" action="process-reset-password.php">
        <h3>Reset Password</h3>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New Password</label>
        <input class="box" type="password" id=password name="password">

        <label for="pass_confirm">Repeat Password</label>
        <input class="box" type="password" id="pass_confirm" name="password_confirmation">

        <button class="btn">Send</button>
    </form>
    </div>
    
</body>
</html>