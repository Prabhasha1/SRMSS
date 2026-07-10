<?php
// Include the updated premium header navigation layout component
require_once __DIR__ . '/header.php';

// Fetch absolute updated details for the logged-in profile dashboard user from db_connect.php
$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// Handle Update Profile post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';

    if (empty($new_username) || empty($new_email)) {
        $error_msg = "Username and Email fields are required.";
    } else {
        // Validate if username already taken by another account
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_stmt->bind_param("si", $new_username, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error_msg = "The chosen username is already assigned to another staff account.";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            
            if (!empty($new_password)) {
                // If modifying password, hash it securely using standard professional algorithms
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $update_stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
            } else {
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $new_username, $new_email, $user_id);
            }

            if ($update_stmt->execute()) {
                $success_msg = "Account details successfully saved and updated.";
                $_SESSION['username'] = $new_username; // Sync immediate session variables
            } else {
                $error_msg = "System error: Unable to complete data saving process.";
            }
            $update_stmt->close();
        }
    }
}

// Fetch user data for presentation mapping fields
$fetch_stmt = $conn->prepare("SELECT username, email, role, created_at FROM users WHERE id = ?");
$fetch_stmt->bind_param("i", $user_id);
$fetch_stmt->execute();
$user_data = $fetch_stmt->get_result()->fetch_assoc();
$fetch_stmt->close();
?>

<style>
    .glass-profile-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }
    body.dark-mode .glass-profile-card {
        background: rgba(30, 41, 59, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .profile-icon-avatar {
        background: linear-gradient(135deg, #4f46e5, #38bdf8);
        color: #fff;
        width: 80px;
        height: 80px;
        font-size: 2.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 15px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
    }
    .form-control-premium {
        background: rgba(255, 255, 255, 0.6) !important;
        border: 1px solid rgba(15, 23, 42, 0.1) !important;
        color: #0f172a !important;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }
    body.dark-mode .form-control-premium {
        background: rgba(15, 23, 42, 0.4) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #f8fafc !important;
    }
    .form-control-premium:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
    }
</style>

<div class="row justify-content-center my-4 py-2">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card glass-profile-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="profile-icon-avatar">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h3 class="fw-bold m-0 text-gradient">Account Profile</h3>
                <p class="text-muted small mt-1">Manage and update your active system credentials</p>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success d-flex align-items-center border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?php echo htmlspecialchars($success_msg); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger d-flex align-items-center border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?php echo htmlspecialchars($error_msg); ?></div>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">System Rank / Role</label>
                    <input type="text" class="form-control form-control-premium fw-semibold opacity-75" value="<?php echo htmlspecialchars($user_data['role'] ?? 'Staff'); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary-subtle px-3"><i class="bi bi-person text-primary"></i></span>
                        <input type="text" name="username" id="username" class="form-control form-control-premium ps-2" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold text-secondary">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary-subtle px-3"><i class="bi bi-envelope text-primary"></i></span>
                        <input type="email" name="email" id="email" class="form-control form-control-premium ps-2" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold text-secondary">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary-subtle px-3"><i class="bi bi-shield-lock text-primary"></i></span>
                        <input type="password" name="password" id="password" class="form-control form-control-premium ps-2" placeholder="Leave blank to keep current password">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary-subtle mt-4">
                    <span class="text-muted small">Registered: <?php echo htmlspecialchars(date("Y-m-d", strtotime($user_data['created_at'] ?? 'now'))); ?></span>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold py-2 shadow-sm">
                        <i class="bi bi-save2 me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>