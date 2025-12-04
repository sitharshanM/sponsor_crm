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

$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
  <div class="card" style="max-width: 450px; width: 100%;">
    <div class="card-header">
      <h2 class="card-title">
        <i class="fas fa-sign-in-alt"></i> Login
      </h2>
    </div>

    <?php if($errors): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
          <?php foreach($errors as $e): ?>
            <div><?=htmlspecialchars($e)?></div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <form method="post" data-validate>
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-user"></i> Username
        </label>
        <input type="text" name="username" class="form-control" 
               value="<?=htmlspecialchars($username)?>" 
               required autofocus 
               placeholder="Enter your username">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-lock"></i> Password
        </label>
        <input type="password" name="password" class="form-control" 
               required 
               placeholder="Enter your password">
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </form>

    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-200); text-align: center; color: var(--gray-600); font-size: 0.875rem;">
      <i class="fas fa-info-circle"></i> Default: admin / admin123
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
