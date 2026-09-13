<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account – CampusXP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.25rem; background: var(--bg-page);">

    <div style="max-width: 520px; width: 100%;">
        <!-- Brand Header -->
        <div style="text-align: center; margin-bottom: 1.25rem;">
            <div class="logo-badge" style="margin: 0 auto 8px; width: 40px; height: 40px; font-size: 1.3rem;">C</div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e3a8a;">Join <span style="color: var(--brand-gold);">CampusXP</span></h1>
            <p style="color: var(--text-muted); font-size: 0.84rem; margin-top: 2px;">Create your authenticated campus account</p>
        </div>

        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" style="margin-bottom: 1rem; padding: 8px 14px;">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <!-- Registration Card -->
        <div class="glass-card" style="padding: 1.25rem 1.5rem;">
            <form action="index.php?controller=auth&action=register" method="POST" id="reg-form" onsubmit="return validateRegistrationForm()">
                
                <!-- Role Selector -->
                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label class="form-label" style="margin-bottom: 4px;">Select Account Role</label>
                    <select name="role" id="reg-role" class="form-control" onchange="toggleRoleFields()" required style="height: 38px;">
                        <option value="student" selected>AIUB Student</option>
                        <option value="club_exec">Club Executive</option>
                        <option value="recruiter">Corporate Recruiter</option>
                    </select>
                </div>

                <div class="grid-2" style="gap: 0.85rem; margin-bottom: 0.85rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 4px;">Full Name</label>
                        <input type="text" name="full_name" id="reg-fullname" class="form-control" placeholder="e.g. Samiul Chowdhury" minlength="3" maxlength="60" pattern="^[a-zA-Z\s\.\-']+$" title="Only letters and spaces are allowed (no numbers)" required style="height: 38px;" oninput="validateFullNameInput(this)">
                        <div id="fullname-error" style="color: #dc2626; font-size: 0.75rem; margin-top: 2px; display: none;"></div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 4px;">Email Address</label>
                        <input type="email" name="email" id="reg-email" class="form-control" placeholder="e.g. name@student.aiub.edu" required style="height: 38px;">
                        <div id="email-error" style="color: #dc2626; font-size: 0.75rem; margin-top: 2px; display: none;"></div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label class="form-label" style="margin-bottom: 4px;">Password (Min 6 Characters)</label>
                    <input type="password" name="password" id="reg-password" class="form-control" placeholder="••••••••" minlength="6" required style="height: 38px;">
                    <div id="password-error" style="color: #dc2626; font-size: 0.75rem; margin-top: 2px; display: none;"></div>
                </div>

                <!-- STUDENT FIELDS -->
                <div id="student-fields" style="background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="font-weight: 700; font-size: 0.82rem; color: var(--brand-blue); margin-bottom: 6px;">Student Academic Details</div>
                    <div class="grid-2" style="gap: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="margin-bottom: 3px; font-size: 0.8rem;">AIUB Student ID</label>
                            <input type="text" name="aiub_id" id="reg-aiub-id" class="form-control" placeholder="e.g. 22-22222-2" maxlength="10" pattern="^\d{2}-\d{5}-\d{1}$" title="Must be strictly 2 digits, hyphen, 5 digits, hyphen, 1 digit (e.g. 22-22222-2, numbers only)" required style="height: 36px;" oninput="formatAiubIdInput(this)">
                            <div id="id-error" style="color: #dc2626; font-size: 0.75rem; margin-top: 2px; display: none;"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="margin-bottom: 3px; font-size: 0.8rem;">Department / Major</label>
                            <select name="department" class="form-control" style="height: 36px;">
                                <option value="CSE">Computer Science (CSE)</option>
                                <option value="EEE">Electrical Engineering (EEE)</option>
                                <option value="BBA">Business Administration (BBA)</option>
                                <option value="Arch">Architecture</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CLUB EXEC FIELDS -->
                <div id="exec-fields" style="display: none; background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="font-weight: 700; font-size: 0.82rem; color: var(--brand-green); margin-bottom: 6px;">Club Executive Details</div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 3px; font-size: 0.8rem;">AIUB Club Name</label>
                        <select name="club_name" class="form-control" style="height: 36px;">
                            <option value="AIUB Computer Club (ACC)">AIUB Computer Club (ACC)</option>
                            <option value="IEEE AIUB Student Branch">IEEE AIUB Student Branch</option>
                            <option value="AIUB Drama Club">AIUB Drama Club</option>
                            <option value="OSCAD">OSCAD</option>
                            <option value="AIUB Sports Club">AIUB Sports Club</option>
                        </select>
                    </div>
                </div>

                <!-- RECRUITER FIELDS -->
                <div id="recruiter-fields" style="display: none; background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 0.75rem; margin-bottom: 0.85rem;">
                    <div style="font-weight: 700; font-size: 0.82rem; color: var(--brand-purple); margin-bottom: 6px;">Corporate Recruiter Details</div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 3px; font-size: 0.8rem;">Hiring Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Brain Station 23, Enosis Solutions" style="height: 36px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 9px; margin-top: 4px;">
                    Complete Registration
                </button>
            </form>

            <div style="margin-top: 1rem; text-align: center; font-size: 0.84rem; color: var(--text-muted);">
                Already registered? <a href="index.php?controller=auth&action=login" style="font-weight: 700;">Sign In</a>
            </div>
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
