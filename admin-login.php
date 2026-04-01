<?php 
    require_once '../config/constants.php';

    $errors = [];

    if (isset($_POST['submit'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        // If no validation errors, check the database
        if (count($errors) == 0) {
            $sql = "SELECT * FROM tbl_admin WHERE username = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $db_password = $row['password'];

                // Verify password
                if (password_verify($password, $db_password)) {
                    // Login successful
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['admin'] = $row['admin'];
                    $_SESSION['logged_in'] = true;

                    // Redirect to dashboard
                    header("Location: " . SITEURL . "admin/dashboard.php");
                    exit();
                } else {
                    $errors[] = "Incorrect password.";
                }
            } else {
                $errors[] = "Account not found.";
            }

            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Bellbrook Open Arms Clinic</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/general.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <style>
        /* Page Specific Styles */
        body {
            /* Ensures the background fills the screen nicely without the footer */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, var(--bg-color, #f8f9fa), #ffffff);
        }

        main { 
            padding-top: 80px; 
            max-width: 1400px; 
            margin: 0 auto; 
            padding-inline: 2rem; 
            flex-grow: 1; /* Pushes content to center if needed */
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
        }

        .login-container {
            background: var(--surface-color, #ffffff);
            padding: 3.5rem;
            border-radius: var(--radius-lg, 24px);
            box-shadow: var(--shadow-soft, 0 10px 30px rgba(0,0,0,0.05));
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(0,0,0,0.03);
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 4rem;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--primary, #0056b3);
        }

        .login-icon {
            font-size: 56px;
            color: var(--primary, #0056b3);
            margin-bottom: 1rem;
            background: var(--primary-light, #e6f0ff);
            width: 90px;
            height: 90px;
            line-height: 90px;
            border-radius: 50%;
            display: inline-block;
        }

        .login-container h1 {
            font-size: 2rem;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
            color: var(--text-main, #333);
        }

        .login-container p {
            color: var(--text-muted, #666);
            margin-bottom: 2.5rem;
            font-size: 1rem;
        }

        /* Error Styling */
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            animation: fadeInUp 0.4s ease-out forwards;
        }

        .alert-error .material-symbols-rounded {
            color: #ef4444;
            font-size: 24px;
        }

        .alert-error-list {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-main, #333);
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.2s ease;
            box-sizing: border-box;
            background: var(--bg-color, #fdfdfd);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary, #0056b3);
            box-shadow: 0 0 0 4px rgba(0, 86, 179, 0.1);
            background: #ffffff;
        }

        .form-group input.input-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .inline-error-msg {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: none;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--primary, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 20px rgba(0, 86, 179, 0.25));
        }
        
        .backbtn {
            width: 100%;
            padding: 1rem;
            background: var(--primary, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .backbtn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 20px rgba(0, 86, 179, 0.25));
        }
        
        /* Animation Classes */
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <main>
        
        <div class="login-container fade-in-up">
            
            <span class="material-symbols-rounded login-icon">admin_panel_settings</span>

            <h1>Admin Access</h1>
            <p>Secure portal for staff and administrators. If you forgot something, call the clinic.</p>
            <img src="assets\images\logo.png" width="250" alt="BOAC Logo" style="margin-bottom: 1.5rem;">

            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <span class="material-symbols-rounded">error</span>
                    <div class="alert-error-list">
                        <?php foreach ($errors as $error): ?>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="adminLoginForm" novalidate>
                <div class="form-group">
                    <label for="admin_username">Username</label>
                    <input type="text" id="admin_username" name="username" autocomplete="username" placeholder="Enter your username">
                    <div class="inline-error-msg" id="username_error">Please enter your username.</div>
                </div>
                
                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" id="admin_password" name="password" autocomplete="current-password" placeholder="Enter your password">
                    <div class="inline-error-msg" id="password_error">Please enter your password.</div>
                </div>
                
                <button type="submit" name="submit" class="btn-submit">
                    Secure Login
                    <span class="material-symbols-rounded">login</span>
                </button>
                <button type="button" class="backbtn" onclick="history.back()">Go back</button>            
            </form>
        </div>
    </main>

    <svg style="width: 0; height: 0; position: absolute;" aria-hidden="true" focusable="false">
        <filter id="liquid-glass-refraction" color-interpolation-filters="sRGB">
            <feTurbulence type="fractalNoise" baseFrequency="0.05" numOctaves="1" result="noise" />
            <feGaussianBlur in="noise" stdDeviation="1" result="blurredNoise" />
            <feDisplacementMap in="SourceGraphic" in2="blurredNoise" scale="8" xChannelSelector="R" yChannelSelector="G" result="displacement" />
        </filter>
    </svg>

    <script src="assets/js/navbar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('adminLoginForm');
            const usernameInput = document.getElementById('admin_username');
            const passwordInput = document.getElementById('admin_password');
            const usernameError = document.getElementById('username_error');
            const passwordError = document.getElementById('password_error');
            
            // Clear errors on input
            const clearError = (input, errorMsg) => {
                input.addEventListener('input', () => {
                    input.classList.remove('input-error');
                    errorMsg.style.display = 'none';
                });
            };
            
            clearError(usernameInput, usernameError);
            clearError(passwordInput, passwordError);

            loginForm.addEventListener('submit', (e) => {
                let isValid = true;

                // Custom Frontend Validation
                if (usernameInput.value.trim() === '') {
                    usernameInput.classList.add('input-error');
                    usernameError.style.display = 'block';
                    isValid = false;
                }
                
                if (passwordInput.value.trim() === '') {
                    passwordInput.classList.add('input-error');
                    passwordError.style.display = 'block';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault(); // Stop submission if fields are empty
                    return;
                }

                // If valid, show loading animation
                const btn = loginForm.querySelector('.btn-submit');
                btn.innerHTML = '<span class="material-symbols-rounded" style="animation: spin 1s linear infinite;">autorenew</span> Authenticating...';
                btn.style.opacity = '0.8';
                btn.style.pointerEvents = 'none';
            });
        });
    </script>
    
    <style>
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>