<?php require_once 'config/constants.php';

$form_status = null; // Variable to trigger the popup

if (isset($_POST['submit'])) {

    $first_name = htmlspecialchars($_POST['firstName']);
    $last_name = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $query = htmlspecialchars($_POST['query']);

    $sql = "INSERT INTO tbl_contact (first_name, last_name, email, subject, query) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $subject, $query);

    if (mysqli_stmt_execute($stmt)) {
        $form_status = 'success';
    } else {
        $form_status = 'error';
    }

    mysqli_stmt_close($stmt);
    unset($_POST); // Clear form data to prevent resubmission on refresh
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Bellbrook Open Arms Clinic</title>
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet">
   
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/general.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/footer.css">

    <style>
        main { padding-top: 140px; max-width: 1400px; margin: 0 auto; padding-inline: 2rem; padding-bottom: 4rem; }
        
        /* Page Header */
        .page-header { text-align: center; margin-bottom: 4rem; }
        .page-header h1 { font-size: clamp(2.5rem, 5vw, 4rem); letter-spacing: -0.02em; margin-bottom: 1rem; color: var(--primary); }
        .page-header p { font-size: 1.25rem; color: var(--text-main); max-width: 600px; margin: 0 auto; }

        /* Contact Layout */
        .contact-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 4rem; align-items: start; }

        /* Contact Info Side */
        .contact-info-wrapper { display: flex; flex-direction: column; gap: 2rem; }
        .info-card { background: var(--surface-color, #ffffff); padding: 2rem; border-radius: var(--radius-lg, 24px); box-shadow: var(--shadow-soft); display: flex; align-items: flex-start; gap: 1rem; border: 1px solid rgba(0,0,0,0.02); }
        .info-icon { width: 48px; height: 48px; background: var(--primary-light); color: var(--primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .info-content h3 { font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--primary); }
        .info-content p { color: var(--text-main); line-height: 1.6; margin: 0; }
        .info-content a { color: var(--primary); text-decoration: none; font-weight: 500; transition: opacity 0.3s; }
        .info-content a:hover { opacity: 0.8; }

        /* Contact Form Card */
        .form-card { background: var(--surface-color, #ffffff); padding: 3rem; border-radius: var(--radius-lg, 24px); box-shadow: var(--shadow-soft); border: 1px solid rgba(0,0,0,0.02); }
        .form-card h2 { font-size: 2rem; margin-bottom: 2rem; color: var(--primary); }
        
        .contact-form { display: flex; flex-direction: column; gap: 1.5rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-group label { font-weight: 500; font-size: 0.95rem; color: var(--text-main); }

        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 25px rgba(0,0,0,0.1));
        }

        /* Input Styling */
        .form-control { 
            font-family: inherit; 
            font-size: 1rem; 
            padding: 1rem 1.25rem; 
            border: 1px solid rgba(0,0,0,0.1); 
            border-radius: 12px; 
            background: var(--bg-color, #f8f9fa); 
            transition: all 0.3s ease; 
            outline: none; 
            color: var(--text-main);
        }
        
        .form-control:focus { 
            border-color: var(--primary); 
            background: #ffffff; 
            box-shadow: 0 0 0 4px var(--primary-light); 
        }
        textarea.form-control { resize: vertical; min-height: 150px; }

        /* Custom Validation Styles */
        .error-text {
            color: #e63946;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: -0.25rem;
            display: none;
        }
        .input-error {
            border-color: #e63946 !important;
            background: #fffafa !important;
        }
        .input-error:focus {
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.15) !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 968px) { 
            .contact-grid { grid-template-columns: 1fr; gap: 3rem; } 
            .form-row { grid-template-columns: 1fr; gap: 1.5rem; }
            .form-card { padding: 2rem; }
        }

        /* Simple animation utility classes */
        .slide-up { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .slide-up.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="page-header slide-up">
            <h1>Get in Touch</h1>
            <p>We're here to answer any questions you may have about our clinic services, volunteering opportunities, or donations.</p>
        </section>

        <section class="contact-grid slide-up">
            <div class="contact-info-wrapper">
                <div class="info-card">
                    <div class="info-icon"><span class="material-symbols-rounded">location_on</span></div>
                    <div class="info-content">
                        <h3>Location</h3>
                        <p>4403 OH-725 E<br>Bellbrook, OH 45305</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-icon"><span class="material-symbols-rounded">phone</span></div>
                    <div class="info-content">
                        <h3>Phone</h3>
                        <p><a href="tel:9378482939">(937) 848-2939</a></p>
                        <p style="font-size: 0.85rem; margin-top: 0.25rem;">For medical emergencies, please dial 911.</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon"><span class="material-symbols-rounded">mail</span></div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><a href="mailto:info@bellbrookopenarmsclinic.org">info@bellbrookopenarmsclinic.org</a></p>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h2>Send a Message</h2>
                <form class="contact-form" action="contact.php" method="POST" id="customContactForm" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" class="form-control" placeholder="John">
                            <span class="error-text" id="firstNameError"></span>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Doe">
                            <span class="error-text" id="lastNameError"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com">
                        <span class="error-text" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="How can we help?">
                        <span class="error-text" id="subjectError"></span>
                    </div>

                    <div class="form-group">
                        <label for="query">Your Query</label>
                        <textarea id="query" name="query" class="form-control" placeholder="Please write your message here..."></textarea>
                        <span class="error-text" id="queryError"></span>
                    </div>

                    <button type="submit" name="submit" class="btn-submit">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <svg style="width: 0; height: 0; position: absolute;" aria-hidden="true" focusable="false">
        <filter id="liquid-glass-refraction" color-interpolation-filters="sRGB">
            <feTurbulence type="fractalNoise" baseFrequency="0.05" numOctaves="1" result="noise" />
            <feGaussianBlur in="noise" stdDeviation="1" result="blurredNoise" />
            <feDisplacementMap in="SourceGraphic" in2="blurredNoise" scale="8" xChannelSelector="R" yChannelSelector="G" result="displacement" />
        </filter>
    </svg>

    <script src="assets/js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            const form = document.getElementById('customContactForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;

                const showError = (id, message) => {
                    const input = document.getElementById(id);
                    const errorSpan = document.getElementById(id + 'Error');
                    input.classList.add('input-error');
                    errorSpan.textContent = message;
                    errorSpan.style.display = 'block';
                    isValid = false;
                };

                const clearError = (id) => {
                    const input = document.getElementById(id);
                    const errorSpan = document.getElementById(id + 'Error');
                    input.classList.remove('input-error');
                    errorSpan.textContent = '';
                    errorSpan.style.display = 'none';
                };

                // Clear previous errors
                ['firstName', 'lastName', 'email', 'subject', 'query'].forEach(clearError);

                // Validation Rules
                if (!document.getElementById('firstName').value.trim()) {
                    showError('firstName', 'Please enter your first name.');
                }

                if (!document.getElementById('lastName').value.trim()) {
                    showError('lastName', 'Please enter your last name.');
                }

                const emailVal = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailVal) {
                    showError('email', 'Please enter your email address.');
                } else if (!emailRegex.test(emailVal)) {
                    showError('email', 'Please enter a valid email address.');
                }

                if (!document.getElementById('subject').value.trim()) {
                    showError('subject', 'Please enter a subject.');
                }

                if (!document.getElementById('query').value.trim()) {
                    showError('query', 'Please write a message.');
                }

                // Prevent submission if invalid
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <?php if ($form_status === 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Message Sent!',
                    text: 'Your message has been received. We\'ll get back to you shortly.',
                    icon: 'success',
                    confirmButtonColor: 'var(--primary)',
                    confirmButtonText: 'Okay',
                    customClass: {
                        popup: 'swal2-custom-popup'
                    }
                });
            });
        </script>
    <?php elseif ($form_status === 'error'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Sorry, there was an error sending your message. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#e63946',
                    confirmButtonText: 'Try Again'
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>