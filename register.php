<?php
require_once __DIR__ . '/config/config.php';
$error = null;

if (Auth::isLoggedIn()) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name        = trim($_POST['name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $studentId   = trim($_POST['student_id'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        $phone       = trim($_POST['phone'] ?? '');
        $year        = trim($_POST['year_of_study'] ?? '');
        $faculty     = trim($_POST['faculty'] ?? '');

        if ($name === '' || $email === '' || $studentId === '' || $password === '') {
            throw new Exception('Please fill in all required fields.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters.');
        }
        if ($password !== $confirm) {
            throw new Exception('Passwords do not match.');
        }

        $userModel = new User();
        $newId = $userModel->register([
            'name' => $name, 'email' => $email, 'student_id' => $studentId,
            'password' => $password, 'phone' => $phone,
            'year_of_study' => $year, 'faculty' => $faculty,
        ]);

        (new ActivityLog())->log($newId, 'Registered a new account');

        Auth::flash('success', 'Registration successful! Please sign in to continue.');
        header('Location: login.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Sign Up';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="ux-card overflow-hidden">
        <div class="row g-0">
          <div class="col-md-5 auth-side d-none d-md-flex flex-column justify-content-center">
            <i class="fa-solid fa-user-graduate fa-3x mb-3"></i>
            <h3 class="fw-bold">Join UniExchange</h3>
            <p>Create your free student account and start saving on books &amp; equipment today.</p>
            <ul class="list-unstyled small mt-3">
              <li class="mb-2"><i class="fa-solid fa-circle-check me-2"></i>Post unlimited listings</li>
              <li class="mb-2"><i class="fa-solid fa-circle-check me-2"></i>Message sellers directly</li>
              <li class="mb-2"><i class="fa-solid fa-circle-check me-2"></i>Save items to wishlist</li>
            </ul>
          </div>
          <div class="col-md-7">
            <div class="p-4 p-md-5">
              <h3 class="fw-bold mb-4">Student Registration</h3>
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-ux"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <form method="POST" class="ux-form">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6">
                    <label>Student ID *</label>
                    <input type="text" name="student_id" class="form-control" required placeholder="e.g. ANU/IT/2324/F/215" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6">
                    <label>University Email *</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6">
                    <label>Year of Study</label>
                    <select name="year_of_study" class="form-select">
                      <option value="">Select</option>
                      <option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label>Faculty / Course</label>
                    <input type="text" name="faculty" class="form-control" placeholder="e.g. HNDIT">
                  </div>
                  <div class="col-md-6">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                  </div>
                  <div class="col-md-6">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                  </div>
                </div>
                <button type="submit" class="btn btn-ux-primary w-100 mt-4">Create Account</button>
                <p class="text-center mt-3 small">Already have an account? <a href="login.php" class="text-purple fw-bold">Sign In</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
