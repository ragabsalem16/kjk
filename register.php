<?php
require_once 'config.php';
require_once 'db.php';

$error = '';
$success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $studentId = trim($_POST['student_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $department = $_POST['department'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول المطلوبة';
    } elseif ($password !== $confirmPassword) {
        $error = 'كلمات المرور غير متطابقة';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        // Check if email already exists
        $existingUser = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);

        if ($existingUser) {
            $error = 'البريد الإلكتروني مسجل بالفعل';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $userId = $db->insert(
                "INSERT INTO users (user_type, email, password, name) VALUES ('student', ?, ?, ?)",
                [$email, $hashedPassword, $name]
            );

            // Insert student record
            $db->insert(
                "INSERT INTO students (user_id, student_id, name, email, department_id) VALUES (?, ?, ?, ?, ?)",
                [$userId, $studentId, $name, $email, $department]
            );

            $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول';
        }
    }
}

// Get departments for dropdown
$departments = $db->fetchAll("SELECT * FROM departments");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب جديد - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
        }

        .register-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="register-section">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-plus fa-3x mb-3"></i>
                <h3>إنشاء حساب جديد</h3>
                <p class="mb-0">سجل في <?php echo SITE_NAME; ?></p>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <a href="login.php" class="btn btn-sm btn-success mt-2">تسجيل الدخول</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="name" placeholder="أدخل اسمك الكامل" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="example@university.edu"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم الطالب</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" name="student_id" placeholder="أدخل رقم الطالب">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">القسم</label>
                        <select class="form-select" name="department">
                            <option value="">اختر القسم</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="أدخل كلمة المرور"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="أعد إدخال كلمة المرور" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-user-plus"></i> إنشاء حساب
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">لديك حساب بالفعل؟
                        <a href="login.php" class="text-decoration-none">تسجيل الدخول</a>
                    </p>
                    <a href="index.php" class="text-decoration-none mt-2 d-block">
                        <i class="fas fa-arrow-right"></i> العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>