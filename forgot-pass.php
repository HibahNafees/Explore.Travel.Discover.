<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/forgot-pass.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="forgot-pass-container">
        <form method="post" action="send-password-reset.php">
            <h3>Forgot Password</h3>
            <input class="box" type="email" name="email" id="email" placeholder="Enter your email">
            <button class="btn">Send</button>
        </form>
    </div>
</body>
</html>