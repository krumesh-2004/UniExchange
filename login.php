<?php
require_once __DIR__ . '/config/config.php';
$error = null;

if (Auth::isLoggedIn()) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') {
            throw new Exception('Please enter both email and password.');
        }
        $userModel = new User();
        $user = $userModel->login($email, $password);
        Auth::login($user);
        (new ActivityLog())->log($user['user_id'], 'Logged in');

        if ($user['user_type'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="ux-card overflow-hidden">
        <div class="row g-0">
          <div class="col-md-5 auth-side d-none d-md-flex flex-column justify-content-center">
            <i class="fa-solid fa-book-open fa-3x mb-3"></i>
            
            <h3 class="fw-bold">Welcome Back!</h3>
            <p>Sign in to manage your listings, check messages, and find great deals from fellow students.</p>
          </div>
          <div class="col-md-7">
            <div class="p-4 p-md-5">
              <h3 class="fw-bold mb-2">Sign In</h3>
             
              <?php $flashOk = Auth::flash('success'); if ($flashOk): ?>
                <div class="alert alert-success rounded-ux"><?php echo htmlspecialchars($flashOk); ?></div>
              <?php endif; ?>
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-ux"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <form method="POST" class="ux-form">
                <div class="mb-3">
                  <label>Email Address</label>
                  <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-ux-primary w-100 mt-2">Sign In</button>
                <p class="text-center mt-3 small">Don't have an account? <a href="register.php" class="text-purple fw-bold">Sign Up</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
