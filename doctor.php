<?php
require_once '../config.php';
require_once '../db.php';

// Check if doctor is logged in
if (!isLoggedIn() || getUserType() !== 'doctor') {
    redirect('../login.php');
}

$pageTitle = 'لوحة Professor';
$userId = getUserId();

// Get professor info
$professor = $db->fetch("SELECT p.*, d.name as department_name FROM professors p 
    JOIN departments d ON p.department_id = d.id 
    WHERE p.user_id = ?", [$userId]);

// Get my courses
$courses = $db->fetchAll("
    SELECT c.*, 
           (SELECT COUNT(*) FROM course_sections cs WHERE cs.course_id = c.id) as sections_count,
           (SELECT COUNT(DISTINCT sc.student_id) FROM student_courses sc 
            JOIN course_sections cs ON sc.section_id = cs.id 
            WHERE cs.course_id = c.id AND sc.status = 'enrolled') as students_count
    FROM courses c
    WHERE c.professor_id = ?
", [$professor['id'] ?? 0]);

// Get pending assignments for grading
$pendingGrades = $db->fetchAll("
    SELECT a.title, a.max_grade, c.name as course_name, s.name as student_name,
           asub.submitted_at, asub.id as submission_id
    FROM assignment_submissions asub
    JOIN assignments a ON asub.assignment_id = a.id
    JOIN courses c ON a.course_id = c.id
    JOIN students s ON asub.student_id = s.id
    WHERE c.professor_id = ? AND asub.grade IS NULL
    ORDER BY asub.submitted_at ASC
    LIMIT 10
", [$professor['id'] ?? 0]);

// Get upcoming exams
$exams = $db->fetchAll("
    SELECT e.*, c.name as course_name
    FROM exams e
    JOIN courses c ON e.course_id = c.id
    WHERE c.professor_id = ?
    ORDER BY e.exam_date ASC
    LIMIT 5
", [$professor['id'] ?? 0]);

// Get announcements
$announcements = $db->fetchAll("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

// Menu items
$menuItems = '
    <li class="nav-item">
        <a class="nav-link active" href="doctor.php">
            <i class="fas fa-home"></i> الرئيسية
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_courses.php">
            <i class="fas fa-book"></i> المواد
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_assignments.php">
            <i class="fas fa-tasks"></i> الواجبات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_exams.php">
            <i class="fas fa-file-alt"></i> الامتحانات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_attendance.php">
            <i class="fas fa-user-check"></i> الحضور
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_grades.php">
            <i class="fas fa-chart-line"></i> الدرجات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_announcements.php">
            <i class="fas fa-bullhorn"></i> الإعلانات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="doctor_messages.php">
            <i class="fas fa-envelope"></i> الرسائل
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
                        <h6 class="mb-0">المواد</h6>
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
                        <h6 class="mb-0">إجمالي الطلاب</h6>
                        <h3 class="mb-0">
                            <?php
                            $totalStudents = 0;
                            foreach ($courses as $c) {
                                $totalStudents += $c['students_count'];
                            }
                            echo $totalStudents;
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">واجبات للتصحيح</h6>
                        <h3 class="mb-0"><?php echo count($pendingGrades); ?></h3>
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
                        <h6 class="mb-0">الامتحانات</h6>
                        <h3 class="mb-0"><?php echo count($exams); ?></h3>
                    </div>
                    <i class="fas fa-file-alt fa-2x opacity-50"></i>
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
                        <a href="doctor_add_course.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i> إضافة مادة
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="doctor_add_assignment.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-tasks"></i> إضافة واجب
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="doctor_add_exam.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-file-alt"></i> إضافة امتحان
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="doctor_add_announcement.php" class="btn btn-outline-info w-100">
                            <i class="fas fa-bullhorn"></i> إضافة إعلان
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- My Courses -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-book"></i> موادتي</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المادة</th>
                                    <th>الشعب</th>
                                    <th>الطلاب</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td><?php echo $course['sections_count']; ?></td>
                                        <td><?php echo $course['students_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد مواد</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pending Grades -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-tasks"></i> واجبات للتصحيح</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pendingGrades)): ?>
                    <div class="list-group">
                        <?php foreach ($pendingGrades as $grade): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($grade['title']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($grade['course_name']); ?> -
                                        <?php echo htmlspecialchars($grade['student_name']); ?>
                                    </small>
                                </div>
                                <a href="doctor_grade.php?id=<?php echo $grade['submission_id']; ?>"
                                    class="btn btn-sm btn-primary">
                                    تصحيح
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">لا توجد واجبات للتصحيح</p>
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
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> الامتحانات</h5>
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
                    <p class="text-muted text-center">لا توجد امتحانات</p>
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

<!-- Professor Info -->
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-user"></i> معلومات Professor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>رقم Professor:</strong> <?php echo htmlspecialchars($professor['professor_id'] ?? ''); ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>الاسم:</strong> <?php echo htmlspecialchars($professor['name'] ?? ''); ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>القسم:</strong> <?php echo htmlspecialchars($professor['department_name'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>