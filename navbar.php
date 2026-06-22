<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<header class="main-header">
    <div class="container">
        <nav class="navbar navbar-expand-md p-0">

            <a class="navbar-brand me-3" href="index.php">
                <img src="images/logo.png" alt="DogWalk Logo" class="img-fluid logo-img">
            </a>

            <button class="navbar-toggler border-0 text-white ms-auto"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navMenu">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse mt-3 mt-md-0" id="navMenu">

                <ul class="navbar-nav me-auto text-center text-md-start">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Početna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#walker-section">Šetači pasa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about-section">O nama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact-section">Kontakt</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-md-row gap-2 mt-3 mt-md-0 align-items-center">

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <span class="text-white small text-center">
                            Zdravo, <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </span>

                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin.php" class="btn btn-warning btn-sm">
                                Admin panel
                            </a>

                        <?php elseif ($_SESSION['user_role'] === 'walker'): ?>
                            <a href="walker_request.php" class="btn btn-outline-light btn-sm">
                                Moj profil
                            </a>
                            <a href="messages.php" class="btn btn-outline-light btn-sm position-relative">
                                <i class="bi bi-chat-dots"></i> Poruke
                                <?php
                                require_once __DIR__ . '/db.php';
                                $unreadStmt = getDB()->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
                                $unreadStmt->execute([$_SESSION['user_id']]);
                                $unreadCount = $unreadStmt->fetchColumn();
                                if ($unreadCount > 0):
                                    ?>
                                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                                        <?= $unreadCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>

                        <?php else: ?>
                            <a href="profile.php" class="btn btn-outline-light btn-sm">
                                Moj profil
                            </a>
                            <a href="messages.php" class="btn btn-outline-light btn-sm position-relative">
                                <i class="bi bi-chat-dots"></i> Poruke
                                <?php
                                require_once __DIR__ . '/db.php';
                                $unreadStmt = getDB()->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
                                $unreadStmt->execute([$_SESSION['user_id']]);
                                $unreadCount = $unreadStmt->fetchColumn();
                                if ($unreadCount > 0):
                                    ?>
                                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                                        <?= $unreadCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <a href="logout.php" class="btn btn-danger btn-sm">
                            Odjava
                        </a>

                    <?php else: ?>

                        <a href="login.php" class="btn btn-outline-light">
                            Login
                        </a>

                        <a href="register.php" class="btn btn-success">
                            Postani šetač
                        </a>

                    <?php endif; ?>

                </div>

            </div>
        </nav>
    </div>
</header>