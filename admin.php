<?php
require_once '../config.php';
require_once '../db.php';

// Check if admin is logged in
if (!isLoggedIn() || getUserType() !== 'admin') {
    redirect('../login.php');
}

$pageTitle = 'لوحة الإدارة';

// Get statistics
$totalStudents = $db->fetch("SELECT COUNT(*) as count FROM students")['count'] ?? 0;
$totalProfessors = $db->fetch("SELECT COUNT(*) as count FROM professors")['count'] ?? 0;
$totalCourses = $db->fetch("SELECT COUNT(*) as count FROM courses")['count'] ?? 0;
$totalDepartments = $db->fetch("SELECT COUNT(*) as count FROM departments")['count'] ?? 0;
$totalFaculties = $db->fetch("SELECT COUNT(*) as count FROM faculties")['count'] ?? 0;

// Get recent students
$recentStudents = $db->fetchAll("SELECT * FROM students ORDER BY created_at DESC LIMIT 10");

// Get recent announcements
$announcements = $db->fetchAll("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

// Menu items
$menuItems = '
    <li class="nav-item">
        <a class="nav-link active" href="admin.php">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_faculties.php">
            <i class="fas fa-building"></i> الكليات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_departments.php">
            <i class="fas fa-layer-group"></i> الأقسام
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_professors.php">
            <i class="fas fa-chalkboard-teacher"></i> الأساتذة
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_students.php">
            <i class="fas fa-user-graduate"></i> الطلاب
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_courses.php">
            <i class="fas fa-book"></i> المواد
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_schedules.php">
            <i class="fas fa-calendar-alt"></i> الجداول
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_statistics.php">
            <i class="fas fa-chart-bar"></i> الإحصائيات
        </a>
    </li>
';
?>

<?php include 'header.php'; ?>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الطلاب</h6>
                        <h3 class="mb-0"><?php echo $totalStudents; ?></h3>
                    </div>
                    <i class="fas fa-user-graduate fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الأساتذة</h6>
                        <h3 class="mb-0"><?php echo $totalProfessors; ?></h3>
                    </div>
                    <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">المواد</h6>
                        <h3 class="mb-0"><?php echo $totalCourses; ?></h3>
                    </div>
                    <i class="fas fa-book fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الأقسام</h6>
                        <h3 class="mb-0"><?php echo $totalDepartments; ?></h3>
                    </div>
                    <i class="fas fa-layer-group fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <a href="admin_add_faculty.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i> كلية
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="admin_add_department.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus"></i> قسم
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="admin_add_professor.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-user-plus"></i> Professor
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="admin_add_student.php" class="btn btn-outline-info w-100">
                            <i class="fas fa-user-plus"></i> طالب
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="admin_add_course.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-plus"></i> مادة
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="admin_schedule.php" class="btn btn-outline-dark w-100">
                            <i class="fas fa-calendar"></i> جدول
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Recent Students -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-graduate"></i> آخر الطلاب المسجلين</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentStudents)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطالب</th>
                                    <th>الاسم</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentStudents as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $student['status'] == 'active' ? 'نشط' : 'غير نشط'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا يوجد طلاب</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-bullhorn"></i> الإعلانات</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($announcements)): ?>
                    <div class="list-group">
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                    <small><?php echo formatDate($announcement['created_at']); ?></small>
                                </div>
                                <p class="mb-1 small">
                                    <?php echo htmlspecialchars(substr($announcement['content'], 0, 80)); ?>...</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد إعلانات</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات النظام</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>عدد الكليات:</strong> <?php echo $totalFaculties; ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>عدد الأقسام:</strong> <?php echo $totalDepartments; ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>عدد المواد:</strong>
                    <?php echo $totalCourses; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>