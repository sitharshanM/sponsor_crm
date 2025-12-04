<?php
// public/login.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/User.php';

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

$userModel = new User($pdo);
$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $user = $userModel->findByUsername($username);
        if ($user && $userModel->verifyPassword($user, $password)) {
            $_SESSION['user_id'] = $user['id'];
            $userModel->updateLastLogin($user['id']);
            header('Location: /index.php');
            exit;
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Login</h2>

<?php if($errors): ?>
  <div style="color:red; margin-bottom:15px;">
    <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
  </div>
<?php endif; ?>

<form method="post" style="max-width:400px;">
  <label>Username<br>
    <input type="text" name="username" value="<?=htmlspecialchars($username)?>" required autofocus>
  </label><br><br>
  <label>Password<br>
    <input type="password" name="password" required>
  </label><br><br>
  <button type="submit">Login</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>

