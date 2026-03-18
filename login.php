<?php
require_once 'config.php';
require_once 'db.php';

$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد الإلكتروني وكلمة المرور';
    } else {
        // Check user credentials
        $sql = "SELECT * FROM users WHERE email = ? AND user_type = ? AND status = 'active'";
        $user = $db->fetch($sql, [$email, $userType]);

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            // Update last login
            $db->execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

            // Redirect based on user type
            switch ($user['user_type']) {
                case 'student':
                    redirect('dashboard/student.php');
                    break;
                case 'doctor':
                    redirect('dashboard/doctor.php');
                    break;
                case 'ta':
                    redirect('dashboard/ta.php');
                    break;
                case 'admin':
                    redirect('dashboard/admin.php');
                    break;
                case 'affairs':
                    redirect('dashboard/affairs.php');
                    break;
                default:
                    redirect('index.php');
            }
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
        }

        .login-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .user-type-btn {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .user-type-btn:hover,
        .user-type-btn.active {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }

        .user-type-btn i {
            font-size: 24px;
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="login-section">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-university fa-3x mb-3"></i>
                <h3><?php echo SITE_NAME; ?></h3>
                <p class="mb-0">تسجيل الدخول إلى النظام</p>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- User Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">نوع المستخدم</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="user-type-btn" onclick="selectUserType('student')">
                                    <i class="fas fa-user-graduate d-block mb-2"></i>
                                    <small>طالب</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="user-type-btn" onclick="selectUserType('doctor')">
                                    <i class="fas fa-chalkboard-teacher d-block mb-2"></i>
                                    <small>أستاذ</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="user-type-btn" onclick="selectUserType('ta')">
                                    <i class="fas fa-user-graduate d-block mb-2"></i>
                                    <small>مساعد تدريس</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="user-type-btn" onclick="selectUserType('admin')">
                                    <i class="fas fa-user-cog d-block mb-2"></i>
                                    <small>إدارة</small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user_type" id="user_type" value="student">
                    </div>

                    <!-- Login Fields -->
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني أو رقم الطالب</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="text" class="form-control" name="email" placeholder="أدخل البريد الإلكتروني"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="أدخل كلمة المرور"
                                required>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-right"></i> العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectUserType(type) {
            document.querySelectorAll('.user-type-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('user_type').value = type;
        }
    </script>
</body>

</html>