<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOSTARTER</title>
    <link rel="stylesheet" href="../public/libs/bootstrap.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-light bg-light border-bottom mb-4">
        <div class="container-fluid d-flex align-items-center">

            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="../public/home.php">BOSTARTER</a>

            <!-- Gruppo Sinistro: Progetti, Statistiche, Skill (solo se logged in) -->
            <?php if (isset($_SESSION['email'])): ?>
                <ul class="navbar-nav flex-row align-items-center ms-3">
                    <li class="nav-item px-2">
                        <a class="nav-link" href="../public/progetti.php">Progetti</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link" href="../public/statistiche.php">Statistiche</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link" href="../public/skill.php">Skill</a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Gruppo Destro: Login/Register or Logout, in fondo -->
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <?php if (isset($_SESSION['email'])): ?>
                    <li class="nav-item px-2">
                        <a class="nav-link"
                           href="../actions/logout.php">Logout <?php echo "(" . htmlspecialchars($_SESSION['email']) . ")"; ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item px-2">
                        <a class="nav-link" href="../public/login.php">Login</a>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link" href="../public/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>