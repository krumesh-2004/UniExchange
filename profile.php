<?php
require_once __DIR__ . '/config/config.php';
$error = null;
Auth::requireLogin();

$userModel = new User();
$user = $userModel->findById(Auth::id());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_profile'])) {
            $userModel->updateProfile(Auth::id(), [
                'name' => trim($_POST['name']),
                'phone' => trim($_POST['phone']),
                'year_of_study' => $_POST['year_of_study'],
                'faculty' => trim($_POST['faculty']),
            ]);
            $_SESSION['name'] = trim($_POST['name']);
            Auth::flash('success', 'Profile updated successfully.');
        } elseif (isset($_POST['change_password'])) {
            $newPass = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            if (strlen($newPass) < 6) throw new Exception('Password must be at least 6 characters.');
            if ($newPass !== $confirm) throw new Exception('Passwords do not match.');
            $userModel->changePassword(Auth::id(), $newPass);
            Auth::flash('success', 'Password changed successfully.');
        }
        header('Location: profile.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Edit Profile';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <?php require __DIR__ . '/includes/alerts.php'; ?>
  <?php if (!empty($error)): ?><div class="alert alert-danger rounded-ux"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <h2 class="section-title">Edit Profile</h2>
  <div class="row g-4 mt-2">
    <div class="col-lg-7">
      <div class="ux-card p-4">
        <h5 class="fw-bold mb-3">Personal Information</h5>
        <form method="POST" class="ux-form">
          <div class="row g-3">
            <div class="col-md-6">
              <label>Full Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label>Email (cannot be changed)</label>
              <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="col-md-6">
              <label>Year of Study</label>
              <select name="year_of_study" class="form-select">
                <?php foreach (['1st Year','2nd Year','3rd Year','4th Year'] as $y): ?>
                  <option <?php echo $user['year_of_study']==$y?'selected':''; ?>><?php echo $y; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label>Faculty / Course</label>
              <input type="text" name="faculty" class="form-control" value="<?php echo htmlspecialchars($user['faculty']); ?>">
            </div>
          </div>
          <button type="submit" name="update_profile" class="btn btn-ux-primary mt-4">Save Changes</button>
        </form>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="ux-card p-4">
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-lock text-purple me-2"></i>Change Password</h5>
        <form method="POST" class="ux-form">
          <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" minlength="6" required>
          </div>
          <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" minlength="6" required>
          </div>
          <button type="submit" name="change_password" class="btn btn-ux-outline w-100">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
