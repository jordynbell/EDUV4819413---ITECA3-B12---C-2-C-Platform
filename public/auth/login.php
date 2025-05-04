<?php

require_once __DIR__ . '/../../lib/db.php';

$loginError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT password FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        if (password_verify($password, $db_password)) {
            session_start();
            $_SESSION['Email'] = $email;
            $_SESSION['User_ID'] = $conn->query("SELECT user_id FROM user WHERE email = '$email'")->fetch_assoc()['user_id'];
            $_SESSION['Role'] = $conn->query("SELECT role FROM user WHERE email = '$email'")->fetch_assoc()['role'];
            header("Location: ../index.php");
        } else {
            $loginError = true;
        }
    } else {
        $loginError = true;
    }
}
$conn->close();
?>

<!DOCTYPE html>

<html>

<head>
    <title>Login - Squito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="/C2CPlatform/public/assets/css/site.css" rel="stylesheet">
</head>

<body>
<div class="container mx-auto mt-5 mb-5" style="max-width: 60rem;">
    <h1 class="text-center mb-4">Squito Login</h1>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border rounded p-4">
                <form action="" method="post">
                    <div class="mb-3 mt-2">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Login" class="btn btn-primary mx-auto d-block">
                    </div>
                    <div class="text-center mt-3">
                        <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
    <?php if($loginError): ?>
    toastr.error('Invalid email or password. Please try again.', 'Login Failed');
    <?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>