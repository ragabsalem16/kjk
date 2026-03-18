<?php
require_once '../config.php';
require_once '../db.php';

// Check if TA is logged in
if (!isLoggedIn() || getUserType() !== 'ta') {
    redirect('../login.php');
}

$pageTitle = 'لوحة مساعد التدريس';
$userId = getUserId();

// Get TA info
$ta = $db->fetch("SELECT ta.*, d.name as department_name FROM teaching_assistants ta 
    JOIN departments d ON ta.department_id = d.id 
    WHERE ta.user_id = ?", [$userId]);

// Get assigned sections
$sections = $db->fetchAll("
    SELECT cs.*, c.name as course_name, c.code
    FROM course_sections cs
    JOIN courses c ON cs.course_id = c.id
    WHERE cs.ta_id = ?
", [$ta['id'] ?? 0]);

// Get attendance records
$attendance = $db->fetchAll("
    SELECT a.*, c.name as course_name, s.name as student_name
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    JOIN students s ON a.student_id = s.id
    WHERE a.date = CURDATE()
    ORDER BY a.date DESC
    LIMIT 10
");

// Menu items
$menuItems = '
    <li class="nav-item">
        <a class="nav-link active" href="ta.php">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="ta_sections.php">
            <i class="fas fa-users"></i> الشعب المسؤولة
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="ta_attendance.php">
            <i class="fas fa-user-check"></i> الحضور
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="ta_materials.php">
            <i class="fas fa-file-upload"></i> المواد
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="ta_grading.php">
            <i class="fas fa-check"></i> التصحيح
        </a>
    </li>
';
?>

<?php include 'header.php'; ?>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الشعب المسؤولة</h6>
                        <h3 class="mb-0"><?php echo count($sections); ?></h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">سجلات الحضور</h6>
                        <h3 class="mb-0"><?php echo count($attendance); ?></h3>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">القسم</h6>
                        <h5 class="mb-0"><?php echo htmlspecialchars($ta['department_name'] ?? ''); ?></h5>
                    </div>
                    <i class="fas fa-building fa-2x opacity-50"></i>
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
                    <div class="col-md-4 mb-2">
                        <a href="ta_attendance.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-check"></i> تسجييل حضور
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="ta_materials.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-file-upload"></i> رفع مواد
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="ta_grading.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-check"></i> تصحيح
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Sections -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> الشعب المسؤولة</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($sections)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>المادة</th>
                                    <th>الشعبة</th>
                                    <th>ال semester</th>
                                    <th>السعة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sections as $section): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($section['code']); ?></td>
                                        <td><?php echo htmlspecialchars($section['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($section['section_number']); ?></td>
                                        <td><?php echo htmlspecialchars($section['semester']); ?></td>
                                        <td><?php echo $section['capacity']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد شعب مسؤولة</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Today's Attendance -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> سجلات الحضور اليوم</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($attendance)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>المادة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $record): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                            echo $record['status'] == 'present' ? 'success' :
                                                ($record['status'] == 'absent' ? 'danger' : 'warning');
                                            ?>">
                                                <?php
                                                echo $record['status'] == 'present' ? 'حاضر' :
                                                    ($record['status'] == 'absent' ? 'غائب' : 'متأخر');
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد سجلات حضور اليوم</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- TA Info -->
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-user"></i> معلومات مساعد التدريس</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>رقم مساعد التدريس:</strong> <?php echo htmlspecialchars($ta['ta_id'] ?? ''); ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>الاسم:</strong>
                    <?php echo htmlspecialchars($ta['name'] ?? ''); ?>
                </p>
            </div>
            <div class="col-md-4">
                <p><strong>القسم:</strong>
                    <?php echo htmlspecialchars($ta['department_name'] ?? ''); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>