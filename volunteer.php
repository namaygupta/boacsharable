<?php require_once 'config/constants.php';
    if (isset($_POST['submit'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $motive = trim($_POST['motive'] ?? '');

        $sql = "INSERT INTO tbl_volunteer (first_name, last_name, email, phone_number, experience, motive) VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$experience', '$motive')";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            $_SESSION['success'] = true;
        } else {
            $_SESSION['error'] = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer | Bellbrook Open Arms Clinic</title>
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet">
   
    <link rel="stylesheet" href="assets/css/general.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/footer.css">

    <style>
        main { padding-top: 140px; max-width: 1400px; margin: 0 auto; padding-inline: 2rem; padding-bottom: 4rem; }
        
        .page-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem auto;
        }
        .page-header h1 {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        .page-header p {
            font-size: 1.125rem;
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Island Form Container */
        .volunteer-container {
            background: var(--surface-color, #ffffff);
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: var(--radius-lg, 24px);
            box-shadow: var(--shadow-soft, 0 10px 40px rgba(0,0,0,0.05));
            border: 1px solid rgba(0,0,0,0.02);
        }

        .volunteer-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: var(--radius-md, 12px);
            font-family: inherit;
            font-size: 1rem;
            background: var(--bg-color, #f8f9fa);
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px var(--primary-light, rgba(0, 123, 255, 0.1));
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Radio Buttons Styling */
        .radio-group-container {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-weight: 400 !important;
        }

        .radio-label input[type="radio"] {
            width: 1.2rem;
            height: 1.2rem;
            accent-color: var(--primary);
        }

        .submit-btn {
            margin-top: 1rem;
            padding: 1rem 2rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 25px rgba(0,0,0,0.1));
        }

        /* --- NEW: Validation Styles --- */
        .error-message {
            color: #d32f2f;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .form-group.has-error input, 
        .form-group.has-error textarea {
            border-color: #d32f2f;
            background: #fffafa;
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1);
        }
        .form-group.has-error .error-message {
            display: block;
        }

        /* --- NEW: Modal Styles --- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            background: var(--surface-color, #ffffff);
            padding: 2.5rem;
            border-radius: var(--radius-lg, 24px);
            text-align: center;
            max-width: 420px;
            width: 90%;
            transform: translateY(30px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .modal-icon {
            width: 70px;
            height: 70px;
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
        }
        .modal-icon span {
            font-size: 36px;
        }
        .modal-content h3 {
            margin-bottom: 0.75rem;
            color: var(--primary);
            font-size: 1.5rem;
        }
        .modal-content p {
            color: var(--text-main);
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .modal-close-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            width: 100%;
            font-size: 1rem;
        }
        .modal-close-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 25px rgba(0,0,0,0.1));
        }

        /* Simple animation utility classes */
        .slide-up { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .slide-up.visible { opacity: 1; transform: translateY(0); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; gap: 1.5rem; }
            .volunteer-container { padding: 2rem 1.5rem; }
        }

        /* Opportunities Section Styles */
        .opportunities-section {
            margin-bottom: 4rem;
        }
        .opportunities-grid {   
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }
        .opp-card {
            background: var(--surface-color);
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            transition: var(--transition-snappy);
        }
        .opp-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .opp-icon {
            width: 45px;
            height: 45px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .opp-icon span {
            font-size: 24px;
        }
        .opp-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--primary);
            line-height: 1.3;
        }
        .opp-card p {
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--text-main);
            margin: 0;
        }

        @media (max-width: 768px) {
            .opportunities-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="page-header slide-up">
            <h1>Volunteer</h1>
            <p>Our clinic relies on the dedication of community members and medical professionals alike. By volunteering your time, you are directly helping provide essential healthcare to those in need. Fill out the application below to get started.</p>
        </div>
        
        <section class="opportunities-section slide-up">
            <div class="opportunities-grid">
                <div class="opp-card">
                    <div class="opp-icon"><span class="material-symbols-rounded">medical_services</span></div>
                    <h3>Tuesday/Women's Health</h3>
                    <p>Physicians, nurses, students (pre-med/medical), and community members.</p>
                </div>

                <div class="opp-card">
                    <div class="opp-icon"><span class="material-symbols-rounded">self_improvement</span></div>
                    <h3>Paths to Wellness</h3>
                    <p>Specifically seeking undergraduate and medical students for long-term care support.</p>
                </div>

                <div class="opp-card">
                    <div class="opp-icon"><span class="material-symbols-rounded">groups</span></div>
                    <h3>Student-Run Free Clinic</h3>
                    <p>Licensed physicians (for supervision) and medical students.</p>
                </div>

                <div class="opp-card">
                    <div class="opp-icon"><span class="material-symbols-rounded">translate</span></div>
                    <h3>Interpretation</h3>
                    <p>Individuals fluent in both English and another language to bridge communication gaps.</p>
                </div>
            </div>
        </section>
        
        <section class="volunteer-container slide-up">
            <form action="" method="POST" class="volunteer-form" id="volunteerForm" novalidate>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" placeholder="Jane">
                        <div class="error-message">Please enter your first name</div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" placeholder="Doe">
                        <div class="error-message">Please enter your last name</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="jane.doe@example.com">
                        <div class="error-message">Please enter a valid email address</div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_number" placeholder="(123) 456-7890">
                        <div class="error-message">Please enter your phone number</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="experience">Relevant Experience (Optional)</label>
                    <textarea id="experience" name="experience" placeholder="Tell us about any medical, administrative, or community service experience you have..."></textarea>
                </div>

                <div class="form-group">
                    <label for="motive">Why do you want to volunteer with BOAC?</label>
                    <textarea id="motive" name="motive" placeholder="Share your motivation for joining our mission..."></textarea>
                    <div class="error-message">Please let us know why you'd like to join us</div>
                </div>

                <button type="submit" name="submit" class="submit-btn" id="submit-btn">Submit Application</button>
            </form>
        </section>
    </main>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="modal-overlay active" id="successModal">
            <div class="modal-content">
                <div class="modal-icon">
                    <span class="material-symbols-rounded">check_circle</span>
                </div>
                <h3>Thank You!</h3>
                <p>Your volunteer application has been submitted successfully. Our team will review your information and get back to you soon.</p>
                <button class="modal-close-btn" onclick="closeModal()">Close</button>
            </div>
        </div>
    <?php 
        // Clear the session variable so it doesn't show again on refresh
        unset($_SESSION['success']);
    endif; 
    ?>

    <?php include 'footer.php'; ?> 

    <script src="assets/js/navbar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll Animation Observer
            const scrollObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.slide-up').forEach(el => scrollObserver.observe(el));

            // Custom Form Validation
            const form = document.getElementById('volunteerForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate First Name
                const firstName = document.getElementById('firstName');
                if (!firstName.value.trim()) {
                    firstName.closest('.form-group').classList.add('has-error');
                    isValid = false;
                }

                // Validate Last Name
                const lastName = document.getElementById('lastName');
                if (!lastName.value.trim()) {
                    lastName.closest('.form-group').classList.add('has-error');
                    isValid = false;
                }

                // Validate Email
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim() || !emailRegex.test(email.value)) {
                    email.closest('.form-group').classList.add('has-error');
                    isValid = false;
                }

                // Validate Phone
                const phone = document.getElementById('phone');
                if (!phone.value.trim()) {
                    phone.closest('.form-group').classList.add('has-error');
                    isValid = false;
                }

                // Validate Motive
                const motive = document.getElementById('motive');
                if (!motive.value.trim()) {
                    motive.closest('.form-group').classList.add('has-error');
                    isValid = false;
                }

                // Prevent submission if validation fails
                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Remove error state upon user input
            document.querySelectorAll('.form-group input, .form-group textarea').forEach(element => {
                element.addEventListener('input', function() {
                    this.closest('.form-group').classList.remove('has-error');
                });
            });
        });

        // Close Modal Function
        function closeModal() {
            const modal = document.getElementById('successModal');
            if(modal) {
                modal.classList.remove('active');
                // Optional: remove modal from DOM entirely after transition
                setTimeout(() => modal.remove(), 400);
            }
        }
    </script>
</body>
</html>