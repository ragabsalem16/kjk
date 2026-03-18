<?php
require_once '../config.php';
require_once '../db.php';

// Check if student affairs is logged in
if (!isLoggedIn() || getUserType() !== 'affairs') {
    redirect('../login.php');
}

$pageTitle = 'لوحة شؤون الطلاب';

// Get statistics
$totalStudents = $db->fetch("SELECT COUNT(*) as count FROM students")['count'] ?? 0;
$activeStudents = $db->fetch("SELECT COUNT(*) as count FROM students WHERE status = 'active'")['count'] ?? 0;
$graduatedStudents = $db->fetch("SELECT COUNT(*) as count FROM students WHERE status = 'graduated'")['count'] ?? 0;
$pendingFees = $db->fetch("SELECT COUNT(*) as count FROM tuition_fees WHERE status = 'pending'")['count'] ?? 0;

// Get recent registrations
$recentStudents = $db->fetchAll("SELECT * FROM students ORDER BY created_at DESC LIMIT 10");

// Get pending tuition fees
$fees = $db->fetchAll("
    SELECT tf.*, s.name, s.student_id
    FROM tuition_fees tf
    JOIN students s ON tf.student_id = s.id
    WHERE tf.status = 'pending'
    ORDER BY tf.due_date ASC
    LIMIT 10
");

// Menu items
$menuItems = '
    <li class="nav-item">
        <a class="nav-link active" href="affairs.php">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="affairs_students.php">
            <i class="fas fa-user-graduate"></i> الطلاب
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="affairs_register.php">
            <i class="fas fa-user-plus"></i> تسجيل طالب
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="affairs_certificates.php">
            <i class="fas fa-file-certificate"></i> الشهادات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="affairs_fees.php">
            <i class="fas fa-money-bill"></i> المصروفات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="affairs_ids.php">
            <i class="fas fa-id-card"></i> البطاقات الجامعية
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
                        <h6 class="mb-0">إجمالي الطلاب</h6>
                        <h3 class="mb-0"><?php echo $totalStudents; ?></h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الطلاب النشطون</h6>
                        <h3 class="mb-0"><?php echo $activeStudents; ?></h3>
                    </div>
                    <i class="fas fa-user-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الخريجون</h6>
                        <h3 class="mb-0"><?php echo $graduatedStudents; ?></h3>
                    </div>
                    <i class="fas fa-graduation-cap fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">مصروفات معلقة</h6>
                        <h3 class="mb-0"><?php echo $pendingFees; ?></h3>
                    </div>
                    <i class="fas fa-money-bill fa-2x opacity-50"></i>
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
                    <div class="col-md-3 mb-2">
                        <a href="affairs_register.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus"></i> تسجيل طالب
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="affairs_certificates.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-file-certificate"></i> إصدار شهادة
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="affairs_ids.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-id-card"></i> طباعة بطاقة
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="affairs_fees.php" class="btn btn-outline-info w-100">
                            <i class="fas fa-money-bill"></i> المصروفات
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
                <h5 class="mb-0"><i class="fas fa-user-graduate"></i> آخر التسجيلات</h5>
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
                                            <span class="badge bg-<?php
                                            echo $student['status'] == 'active' ? 'success' :
                                                ($student['status'] == 'graduated' ? 'info' : 'secondary');
                                            ?>">
                                                <?php
                                                echo $student['status'] == 'active' ? 'نشط' :
                                                    ($student['status'] == 'graduated' ? 'خريج' : 'موقف');
                                                ?>
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

    <!-- Pending Fees -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill"></i> مصروفات معلقة</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($fees)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>المبلغ</th>
                                    <th>الأولوية</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fees as $fee): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fee['name']); ?></td>
                                        <td><?php echo number_format($fee['amount'], 2); ?> $</td>
                                        <td>
                                            <?php if (strtotime($fee['due_date']) < time()): ?>
                                                <span class="badge bg-danger">متأخر</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">معلق</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد مصروفات معلقة</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>