<?php
// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #1e40af 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar .nav-link i {
            width: 25px;
        }

        .top-bar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3 text-center border-bottom border-secondary">
                    <i class="fas fa-university fa-2x mb-2"></i>
                    <h6 class="mb-0"><?php echo SITE_NAME; ?></h6>
                </div>
                <div class="p-3">
                    <div class="user-info mb-4">
                        <div class="user-avatar">
                            <?php echo substr(getUserName(), 0, 1); ?>
                        </div>
                        <div>
                            <small class="d-block text-white-50">مرحباً</small>
                            <strong><?php echo getUserName(); ?></strong>
                        </div>
                    </div>
                    <ul class="nav flex-column">
                        <?php echo $menuItems ?? ''; ?>
                        <li class="nav-item mt-3">
                            <a href="../logout.php" class="nav-link text-danger">
                                <i class="fas fa-sign-out-alt"></i> خروج
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <!-- Top Bar -->
                <div class="top-bar p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h5>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="position-relative">
                            <i class="fas fa-bell fa-lg text-muted"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </div>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo substr(getUserName(), 0, 1); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <div class="p-4">