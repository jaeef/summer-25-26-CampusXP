# 🎓 CampusXP: AIUB Smart Opportunity, Club Recruitment & Corporate Drive Platform

A unified, role-based campus recruitment and opportunity management web application built for **American International University - Bangladesh (AIUB)** following pure **MVC architecture** with **HTML5, CSS3 Glassmorphism, JavaScript ES6+ (AJAX), PHP 8.x, and MySQL 8.x**.

---

## 🚀 Quick Setup Guide for Teammates

### Step 1: Place the Project in XAMPP
1. Unzip the downloaded folder.
2. Rename the unzipped folder to `webproject` (or keep as `webproject`).
3. Place the folder into your local XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\webproject
   ```

---

### Step 2: Start Apache & MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

---

### Step 3: Import the Database
1. Open your web browser and go to **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click **New** in the left sidebar.
3. Name the database **`campusxp_aiub`** and set collation to `utf8mb4_unicode_ci`, then click **Create**.
4. With `campusxp_aiub` selected, click the **Import** tab in the top navigation bar.
5. Click **Choose File**, select the SQL file from the project:
   ```
   C:\xampp\htdocs\webproject\database\schema.sql
   ```
6. Scroll down and click **Import** / **Go**.

---

### Step 4: Open and Test the Application
Open your web browser and visit:
```
http://localhost/webproject/index.php
```

---

## 🔑 Demo Login Accounts (All Roles)

All accounts share the same default password: **`password123`**

| Role | Email Address | Password | Features to Test |
| :--- | :--- | :--- | :--- |
| **🎓 Student** | `student@aiub.edu` | `password123` | • Browse Opportunity Radar<br>• Filter by Category & Bookmarks<br>• 1-Click Apply to Club/Corporate Drives<br>• View Application Stepper Progress<br>• View Profile Vault (Locked AIUB ID) |
| **⚡ Club Executive** | `exec@aiub.edu` | `password123` | • Post new Club Recruitment Drive<br>• Drag-and-drop Candidate Kanban Pipeline<br>• Batch generate 15-min Interview Venue Slots<br>• Candidate Demographic Analytics<br>• Equipment Logistics Requisitions |
| **💼 Corporate Recruiter** | `recruiter@brainstation-23.com` | `password123` | • Post Job Circulars & Build Dynamic Forms<br>• Filter Verified AIUB Talent (CGPA, Dept)<br>• Create Shortlist Buckets<br>• Send Direct Candidate Dashboard Alerts |
| **🛡️ OSA Administrator** | `admin@aiub.edu` | `password123` | • Campaign Moderation Queue (Approve/Reject with comments)<br>• Allocate AIUB Campus Booths (Annex Plaza, Canopy)<br>• Review Equipment Logistics (Sound, Microphones)<br>• System-wide Audit Metrics |

---

## 🧪 Testing New User Registrations
To test registration:
1. Click **"Create an Account"** on the Sign In page.
2. Select an Account Role:
   - **Student:** Prompts for strictly formatted AIUB Student ID (`XX-XXXXX-X`) and Department.
   - **Club Executive:** Prompts for Club Name.
   - **Corporate Recruiter:** Prompts for Company Name.
3. Enter valid credentials:
   - Full Name: Letters only (e.g., `John Doe`)
   - AIUB ID: Auto-formats to `22-12345-1`
   - Email: Valid email address
   - Password: Minimum 6 characters
4. Submit and log in to the newly created account.

---

## 📁 MVC Architecture Directory Map
```
c:\xampp\htdocs\webproject\
├── index.php                  # Front Controller (Single entry point & router)
├── config/
│   └── db.php                 # MySQLi connection & input sanitizers
├── controllers/
│   ├── AuthController.php      # Authentication, registration & cookies
│   ├── StudentController.php   # Student radar, applications & profile
│   ├── ExecutiveController.php # Club drives, Kanban, interview scheduler
│   ├── RecruiterController.php # Job posts, dynamic forms & talent search
│   └── AdminController.php     # Moderation, booths & equipment logistics
├── models/
│   ├── UserModel.php          # User credentials & authentication queries
│   ├── StudentModel.php        # Profiles, applications & bookmarks
│   ├── DriveModel.php          # Club recruitment campaigns
│   ├── ApplicationModel.php    # Application status lifecycles
│   ├── InterviewModel.php      # Timeslot generation queries
│   ├── RecruiterModel.php      # Corporate drives & dynamic forms
│   ├── LogisticsModel.php      # Campus equipment requisitions
│   └── AdminModel.php          # Moderation queue & booth allocations
├── views/
│   ├── auth/                  # login.view.php, register.view.php
│   ├── student/               # dashboard, profile, applications
│   ├── executive/             # dashboard, kanban, scheduler, logistics
│   ├── recruiter/             # dashboard, create_drive, talent_search
│   └── admin/                 # dashboard, moderation, booths, logistics
├── ajax/
│   ├── search.php             # Live debounced search endpoint
│   └── bookmark.php           # Asynchronous bookmark toggle endpoint
└── assets/
    ├── css/style.css          # Glassmorphism UI styles
    └── js/                    # Client-side scripts & form validations
```
