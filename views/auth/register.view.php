<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account – CampusXP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box auth-box-wide">
        <div class="auth-brand">
            <div class="logo-badge">C</div>
            <div class="brand-text">CampusXP</div>
        </div>

        <h1 class="auth-title">Create your account</h1>
        <p class="auth-sub">For AIUB students, club executives and recruiters.</p>

        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="alert">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 1.5rem;">
            <form action="index.php?controller=auth&action=register" method="POST" id="reg-form" onsubmit="return validateRegistrationForm()">

                <div class="form-group">
                    <label class="form-label" for="reg-role">I am a</label>
                    <select name="role" id="reg-role" class="form-control" onchange="toggleRoleFields()" required>
                        <option value="student" selected>AIUB student</option>
                        <option value="club_exec">Club executive</option>
                        <option value="recruiter">Recruiter</option>
                    </select>
                </div>

                <div class="grid-2" style="gap: 0 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="reg-fullname">Full name</label>
                        <input type="text" name="full_name" id="reg-fullname" class="form-control" autocomplete="name" placeholder="Samiul Chowdhury" minlength="3" maxlength="60" pattern="^[a-zA-Z\s\.\-']+$" title="Only letters and spaces are allowed (no numbers)" required oninput="validateFullNameInput(this)">
                        <div class="field-error" id="fullname-error" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg-email">Email</label>
                        <input type="email" name="email" id="reg-email" class="form-control" autocomplete="email" placeholder="name@student.aiub.edu" required>
                        <div class="field-error" id="email-error" style="display: none;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-password">Password</label>
                    <input type="password" name="password" id="reg-password" class="form-control" autocomplete="new-password" minlength="6" required>
                    <span class="form-hint">At least 6 characters.</span>
                    <div class="field-error" id="password-error" style="display: none;"></div>
                </div>

                <div id="student-fields" class="form-section" style="margin-top: 0.5rem;">
                    <div class="grid-2" style="gap: 0 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="reg-aiub-id">AIUB student ID</label>
                            <input type="text" name="aiub_id" id="reg-aiub-id" class="form-control" placeholder="22-22222-2" inputmode="numeric" maxlength="10" pattern="^\d{2}-\d{5}-\d{1}$" title="Must be strictly 2 digits, hyphen, 5 digits, hyphen, 1 digit (e.g. 22-22222-2, numbers only)" required oninput="formatAiubIdInput(this)">
                            <div class="field-error" id="id-error" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reg-dept">Department</label>
                            <select name="department" id="reg-dept" class="form-control">
                                <option value="CSE">Computer Science (CSE)</option>
                                <option value="EEE">Electrical Engineering (EEE)</option>
                                <option value="BBA">Business Administration (BBA)</option>
                                <option value="Arch">Architecture</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="exec-fields" class="form-section" style="display: none; margin-top: 0.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="reg-club">Club</label>
                        <select name="club_name" id="reg-club" class="form-control">
                            <option value="AIUB Computer Club (ACC)">AIUB Computer Club (ACC)</option>
                            <option value="IEEE AIUB Student Branch">IEEE AIUB Student Branch</option>
                            <option value="AIUB Drama Club">AIUB Drama Club</option>
                            <option value="OSCAD">OSCAD</option>
                            <option value="AIUB Sports Club">AIUB Sports Club</option>
                        </select>
                    </div>
                </div>

                <div id="recruiter-fields" class="form-section" style="display: none; margin-top: 0.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="reg-company">Company</label>
                        <input type="text" name="company_name" id="reg-company" class="form-control" autocomplete="organization" placeholder="Brain Station 23">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>
        </div>

        <div class="auth-footer">
            Already have an account? <a class="fw-600" href="index.php?controller=auth&action=login">Sign in</a>
        </div>
    </div>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('reg-role').value;
            const studentFields = document.getElementById('student-fields');
            const studentIdInput = document.getElementById('reg-aiub-id');
            studentFields.style.display = role === 'student' ? 'block' : 'none';
            if (studentIdInput) {
                studentIdInput.required = (role === 'student');
            }
            document.getElementById('exec-fields').style.display = role === 'club_exec' ? 'block' : 'none';
            document.getElementById('recruiter-fields').style.display = role === 'recruiter' ? 'block' : 'none';
        }

        function validateFullNameInput(input) {
            const nameErr = document.getElementById('fullname-error');
            const val = input.value;
            if (/\d/.test(val)) {
                nameErr.innerText = "Full Name cannot contain numbers. Only letters and spaces allowed.";
                nameErr.style.display = "block";
            } else if (val.trim().length > 0 && !/^[a-zA-Z\s\.\-']+$/.test(val)) {
                nameErr.innerText = "Full Name contains invalid characters (letters and spaces only).";
                nameErr.style.display = "block";
            } else {
                nameErr.style.display = "none";
            }
        }

        function formatAiubIdInput(input) {
            const idErr = document.getElementById('id-error');
            // Remove any non-digit character (strictly numbers only)
            let raw = input.value.replace(/\D/g, '');
            
            // Limit to max 8 digits (2 + 5 + 1)
            if (raw.length > 8) {
                raw = raw.substring(0, 8);
            }

            // Construct 22-22222-2 format
            let formatted = '';
            if (raw.length > 0) {
                formatted = raw.substring(0, 2);
            }
            if (raw.length > 2) {
                formatted += '-' + raw.substring(2, 7);
            }
            if (raw.length > 7) {
                formatted += '-' + raw.substring(7, 8);
            }

            input.value = formatted;

            // Real-time validation message
            if (raw.length > 0 && raw.length < 8) {
                idErr.innerText = "Must complete format: 2 digits, hyphen, 5 digits, hyphen, 1 digit (e.g. 22-22222-2).";
                idErr.style.display = "block";
            } else {
                idErr.style.display = "none";
            }
        }

        function validateRegistrationForm() {
            let valid = true;
            const nameInput = document.getElementById('reg-fullname');
            const name = nameInput.value.trim();
            const nameErr = document.getElementById('fullname-error');
            const nameRegex = /^[a-zA-Z\s\.\-']{3,60}$/;
            
            if (/\d/.test(name)) {
                nameErr.innerText = "Full Name cannot contain numbers. Only letters and spaces allowed.";
                nameErr.style.display = "block";
                valid = false;
            } else if (!nameRegex.test(name)) {
                nameErr.innerText = "Full Name must be 3-60 characters (letters and spaces only, no numbers).";
                nameErr.style.display = "block";
                valid = false;
            } else {
                nameErr.style.display = "none";
            }

            const emailInput = document.getElementById('reg-email');
            const email = emailInput.value.trim();
            const emailErr = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailErr.innerText = "Please enter a valid email address format.";
                emailErr.style.display = "block";
                valid = false;
            } else {
                emailErr.style.display = "none";
            }

            const role = document.getElementById('reg-role').value;
            if (role === 'student') {
                const idVal = document.getElementById('reg-aiub-id').value.trim();
                const idErr = document.getElementById('id-error');
                if (idVal.length === 0) {
                    idErr.innerText = "AIUB Student ID is required.";
                    idErr.style.display = "block";
                    valid = false;
                } else {
                    const idRegex = /^\d{2}-\d{5}-\d{1}$/;
                    if (!idRegex.test(idVal)) {
                        idErr.innerText = "AIUB ID must strictly follow 22-22222-2 format (2 digits - 5 digits - 1 digit, no strings/letters).";
                        idErr.style.display = "block";
                        valid = false;
                    } else {
                        idErr.style.display = "none";
                    }
                }
            }
            return valid;
        }
    </script>
</body>
</html>
