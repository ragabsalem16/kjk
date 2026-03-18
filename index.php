<?php
require_once 'config.php';
require_once 'db.php';

// Get announcements
$announcements = $db->fetchAll("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

// Get news (using announcements as news)
$news = $db->fetchAll("SELECT * FROM announcements WHERE priority = 'high' ORDER BY created_at DESC LIMIT 3");

// Get faculties
$faculties = $db->fetchAll("SELECT * FROM faculties");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
        }
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 100px 0;
        }
        .faculty-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .faculty-card:hover {
            transform: translateY(-10px);
        }
        .news-card {
            border-right: 4px solid var(--accent-color);
        }
        .stat-box {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .nav-bar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light nav-bar sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-university"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="#news">الأخبار</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faculties">الكليات</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">اتصل بنا</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="login.php" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> حساب جديد
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">مرحباً بك في <?php echo SITE_NAME; ?></h1>
            <p class="lead mb-4">نظام متكامل لإدارة العملية التعليمية والطلاب</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-light btn-lg">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </a>
                <a href="#faculties" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-graduation-cap"></i> استكشف الكليات
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3>10,000+</h3>
                        <p>طالب</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                        <h3>500+</h3>
                        <p>أستاذ</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h3>200+</h3>
                        <p>مقرر</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <h3>5</h3>
                        <p>كليات</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-newspaper"></i> أحدث الأخبار والإعلانات
            </h2>
            <div class="row">
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $item): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card news-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($item['content'], 0, 150)); ?>...</p>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i> <?php echo formatDate($item['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">لا توجد أخبار حالياً</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Announcements -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-bullhorn"></i> الإعلانات الرسمية
            </h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="list-group">
                        <?php if (!empty($announcements)): ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                        <small class="text-muted"><?php echo formatDate($announcement['created_at']); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars(substr($announcement['content'], 0, 200)); ?>...</p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                لا توجد إعلانات حالياً
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculties Section -->
    <section id="faculties" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-building-columns"></i> كلياتنا
            </h2>
            <div class="row">
                <?php if (!empty($faculties)): ?>
                    <?php foreach ($faculties as $faculty): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card faculty-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title"><?php echo htmlspecialchars($faculty['name']); ?></h5>
                                    <p class="card-text text-muted">
                                        العميد: <?php echo htmlspecialchars($faculty['dean'] ?? 'غير محدد'); ?>
                                    </p>
                                    <a href="#" class="btn btn-outline-primary">المزيد</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">جاري تحميل الكليات...</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-envelope"></i> اتصل بنا
            </h2>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الرسالة</label>
                            <textarea class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> إرسال
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p class="text-muted">نظام متكامل لإدارة العملية التعليمية</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-muted text-decoration-none">الرئيسية</a></li>
                        <li><a href="#faculties" class="text-muted text-decoration-none">الكليات</a></li>
                        <li><a href="login.php" class="text-muted text-decoration-none">تسجيل الدخول</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>تواصل معنا</h5>
                    <p class="text-muted">
                        <i class="fas fa-envelope"></i> info@university.edu<br>
                        <i class="fas fa-phone"></i> +20 123 456 789
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0 text-muted">© 2024 <?php echo SITE_NAME; ?> - جميع الحقوق محفوظة</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
