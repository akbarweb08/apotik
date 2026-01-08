<?php
session_start();
include 'config.php';

// Cek sudah login belum
if (isset($_SESSION['username'])) {
    $REDIRECT_URL = $_SESSION['role'] == 'kasir' ? 'admin.php' : 'kasir.php';
    header("Location: $REDIRECT_URL ");
    exit();
}

// Ambil pesan error dari URL
$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'field_kosong':
            $error = 'Username dan password harus diisi!';
            break;
        case 'login_gagal':
            $error = 'Username atau password salah!';
            break;
        default:
            $error = 'Terjadi kesalahan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek.id</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #9CAF88 0%, #A8B89C 50%, #B8C4B4 100%);
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px -12px rgba(120, 134, 107, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            border: 1px solid rgba(156, 175, 136, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 35px 70px -15px rgba(120, 134, 107, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.3);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #9CAF88, #78866B);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 15px 35px rgba(120, 134, 107, 0.3);
        }
        
        .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .apotek-title {
            color: #78866B;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        
        .subtitle {
            color: #8A9A7B;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        .form-floating {
            margin-bottom: 1.75rem;
        }
        
        .form-control {
            border: 2px solid #E8ECE4;
            border-radius: 14px;
            padding: 1.2rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            background: #FAFDF7;
            transition: all 0.3s ease;
            height: calc(3.5rem + 2px);
        }
        
        .form-control:focus {
            border-color: #9CAF88;
            box-shadow: 0 0 0 0.25rem rgba(156, 175, 136, 0.15);
            background: white;
            transform: translateY(-1px);
        }
        
        .form-floating > label {
            color: #8A9A7B;
            font-weight: 500;
            padding-left: 1rem;
            font-size: 0.95rem;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #9CAF88 0%, #78866B 100%);
            border: none;
            border-radius: 14px;
            padding: 1.1rem;
            font-weight: 600;
            font-size: 1.05rem;
            color: white;
            width: 100%;
            height: 56px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(120, 134, 107, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(120, 134, 107, 0.4);
            color: white !important;
            background: linear-gradient(135deg, #88A370 0%, #6D7A5E 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }
        
        .demo-info {
            background: linear-gradient(135deg, rgba(156, 175, 136, 0.1), rgba(184, 196, 180, 0.1));
            border: 1px solid rgba(156, 175, 136, 0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .logo-icon {
                width: 70px;
                height: 70px;
            }
            
            .logo-icon i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <h1 class="apotek-title">Apotek.id</h1>
                <p class="subtitle mb-0">Sistem Manajemen Apotek</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="proses_login.php">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <label for="username">
                        <i class="bi bi-person-fill me-1"></i>Username
                    </label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required>
                    <label for="password">
                        <i class="bi bi-lock-fill me-1"></i>Password
                    </label>
                </div>
                
                <button type="submit" class="btn btn-login mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Masuk ke Sistem
                </button>
            </form>
            
            <div class="demo-info text-center">
                <small class="text-muted fw-medium">
                    <i class="bi bi-info-circle me-1"></i>
                    Demo: <strong>admin1</strong> / <strong>admin123</strong> | 
                    <strong>kasir1</strong> / <strong>admin123</strong>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>