<?php
require_once '../config.php';
require_once '../db.php';

// Check if student is logged in
if (!isLoggedIn() || getUserType() !== 'student') {
    redirect('../login.php');
}

$pageTitle = 'لوحة الطالب';
$userId = getUserId();

// Get student info
$student = $db->fetch("SELECT s.*, d.name as department_name FROM students s 
    JOIN departments d ON s.department_id = d.id 
    WHERE s.user_id = ?", [$userId]);

// Get enrolled courses
$courses = $db->fetchAll("
    SELECT c.*, p.name as professor_name, cs.section_number, cs.semester
    FROM student_courses sc
    JOIN course_sections cs ON sc.section_id = cs.id
    JOIN courses c ON cs.course_id = c.id
    LEFT JOIN professors p ON c.professor_id = p.id
    WHERE sc.student_id = ? AND sc.status = 'enrolled'
", [$student['id'] ?? 0]);

// Get upcoming assignments
$assignments = $db->fetchAll("
    SELECT a.*, c.name as course_name
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    WHERE a.due_date > NOW()
    ORDER BY a.due_date ASC
    LIMIT 5
");

// Get upcoming exams
$exams = $db->fetchAll("
    SELECT e.*, c.name as course_name
    FROM exams e
    JOIN courses c ON e.course_id = c.id
    WHERE e.exam_date > NOW()
    ORDER BY e.exam_date ASC
    LIMIT 5
");

// Get announcements
$announcements = $db->fetchAll("
    SELECT * FROM announcements 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Get grades
$grades = $db->fetchAll("
    SELECT c.name as course_name, 
           COALESCE((
               SELECT SUM(eg.grade) / COUNT(*) 
               FROM exam_grades eg
               JOIN exams ex ON eg.exam_id = ex.id
               WHERE ex.course_id = c.id AND eg.student_id = ?
           ), 0) as grade
    FROM courses c
", [$student['id'] ?? 0]);

// Menu items
$menuItems = '
    <li class="nav-item">
        <a class="nav-link active" href="student.php">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_schedule.php">
            <i class="fas fa-calendar-alt"></i> الجدول الدراسي
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_courses.php">
            <i class="fas fa-book"></i> المواد المسجلة
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_grades.php">
            <i class="fas fa-chart-line"></i> الدرجات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_assignments.php">
            <i class="fas fa-tasks"></i> الواجبات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_exams.php">
            <i class="fas fa-file-alt"></i> الامتحانات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_attendance.php">
            <i class="fas fa-user-check"></i> الحضور والغياب
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_messages.php">
            <i class="fas fa-envelope"></i> الرسائل
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="student_fees.php">
            <i class="fas fa-money-bill"></i> المصروفات
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
                        <h6 class="mb-0">المواد المسجلة</h6>
                        <h3 class="mb-0"><?php echo count($courses); ?></h3>
                    </div>
                    <i class="fas fa-book fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">المعدل التراكمي</h6>
                        <h3 class="mb-0"><?php echo number_format($student['gpa'] ?? 0, 2); ?></h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الواجبات المعلقة</h6>
                        <h3 class="mb-0"><?php echo count($assignments); ?></h3>
                    </div>
                    <i class="fas fa-tasks fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">الامتحانات القادمة</h6>
                        <h3 class="mb-0"><?php echo count($exams); ?></h3>
                    </div>
                    <i class="fas fa-file-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Schedule -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> الجدول الدراسي</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المادة</th>
                                    <th>الاستاذ</th>
                                    <th>الشعبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['professor_name'] ?? 'غير محدد'); ?></td>
                                        <td><?php echo htmlspecialchars($course['section_number']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد مواد مسجلة</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Assignments -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-tasks"></i> الواجبات القادمة</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($assignments)): ?>
                    <div class="list-group">
                        <?php foreach ($assignments as $assignment): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                    <small><?php echo formatDate($assignment['due_date']); ?></small>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($assignment['course_name']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد واجبات قادمة</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Exams -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> الامتحانات القادمة</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($exams)): ?>
                    <div class="list-group">
                        <?php foreach ($exams as $exam): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($exam['title']); ?></h6>
                                    <small><?php echo formatDateTime($exam['exam_date']); ?></small>
                                </div>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($exam['course_name']); ?> |
                                    <?php echo $exam['duration_minutes']; ?> دقيقة
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد امتحانات قادمة</p>
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
                                    <?php echo htmlspecialchars(substr($announcement['content'], 0, 100)); ?>...</p>
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

<!-- Student Info -->
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-user"></i> معلومات الطالب</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>رقم الطالب:</strong> <?php echo htmlspecialchars($student['student_id'] ?? ''); ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>الاسم:</strong>
                    <?php echo htmlspecialchars($student['name'] ?? ''); ?>
                </p>
            </div>
            <div class="col-md-4">
                <p><strong>القسم:</strong>
                    <?php echo htmlspecialchars($student['department_name'] ?? ''); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>