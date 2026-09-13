# CampusXP: Smart Campus Opportunity & Recruitment Platform (PHP + MySQL, MVC)

A unified 4-role campus recruitment, event radar, and placement management platform: **Student, Club Executive, Corporate Recruiter, Admin / Moderator**.
Written in plain PHP 8.x with procedural `mysqli` and prepared statements. No frameworks, no Composer, no build step. Copy it into XAMPP and it runs.

---

## 1. Install (XAMPP)

1. Copy the `webproject` folder into `C:\xampp\htdocs\` so it becomes `htdocs/webproject/`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin` → **New** → create database `campusxp_aiub` (Collation: `utf8mb4_unicode_ci`).
4. Click **Import** → Choose `database/schema.sql` → click **Go** / **Import**.
5. Open `http://localhost/webproject/index.php` in your browser.
6. Sign in using any of the pre-configured demo role accounts:

| Role | Default Email | Password | Primary Capabilities |
| :--- | :--- | :--- | :--- |
| **🎓 Student** | `student@aiub.edu` | `password123` | Profile & SOP Vault, Opportunity Radar, 1-Click Apply, Live Status Stepper |
| **⚡ Club Executive** | `exec@aiub.edu` | `password123` | Publish Drives, Drag-and-Drop Kanban Board, 15-Min Interview Slot Generator |
| **💼 Corporate Recruiter** | `recruiter@brainstation-23.com` | `password123` | Post Job Circulars, Dynamic Screening Forms, CGPA Talent Search, Shortlists |
| **🛡️ Admin / Moderator** | `admin@aiub.edu` | `password123` | Moderate Pending Campaigns, Allocate Physical Booth Spots, Equipment Logistics |

*(Everyone else can create a new account on the registration page with role-specific dynamic validation).*

---

## 2. Folder Structure

```
webproject/
├── index.php                  Front controller: the ONLY entry point (router)
├── README.md                  Quick setup and architecture guide
├── .gitignore                 Excludes temp logs, OS metadata and archives
│
├── config/
│   └── db.php                 DB connection (mysqli), cleanInput(), flash helpers, auth session
│
├── database/
│   └── schema.sql             MySQL 8.x InnoDB schema + demo seed data (13 tables)
│
├── models/                    M — Every SQL prepared query lives here
│   ├── UserModel.php          User authentication & registration
│   ├── StudentModel.php       Profile Vault, CGPA checks, applications & bookmarks
│   ├── DriveModel.php         Club & Corporate recruitment campaigns
│   ├── ApplicationModel.php   Club & Corporate application status lifecycles
│   ├── InterviewModel.php     15-minute venue interview timeslots
│   ├── RecruiterModel.php     Dynamic form fields, candidate search & shortlists
│   ├── LogisticsModel.php     Physical campus equipment requisitions
│   └── AdminModel.php         Campaign moderation queue & booth allocations
│
├── controllers/               C — Request handling, cross-validation, auth guards
│   ├── AuthController.php     Login, 30-day Remember-Me cookie, Register, Logout
│   ├── StudentController.php  Radar view, 1-Click apply, CGPA threshold validation
│   ├── ExecutiveController.php Drive publishing, Kanban updates, slot generation
│   ├── RecruiterController.php Circulars, dynamic screening forms, talent filtering
│   └── AdminController.php    Campaign moderation actions, booth spot allocation
│
├── views/                     V — Clean presentation layer (HTML & template variables)
│   ├── layouts/               header.php, footer.php (shared glassmorphic shell)
│   ├── auth/                  login.view.php, register.view.php
│   ├── student/               dashboard.view.php, profile.view.php, applications.view.php
│   ├── executive/             dashboard, kanban, scheduler, analytics, logistics
│   ├── recruiter/             dashboard, create_drive, talent_search, shortlists, kanban
│   └── admin/                 dashboard, moderation, booths, logistics
│
├── ajax/                      Asynchronous JSON endpoints
│   ├── search.php             Live debounced search for opportunities & talent
│   └── bookmark.php           Instant 1-click opportunity bookmark toggle
│
└── assets/
    ├── css/style.css          Vanilla CSS glassmorphism design system
    └── js/                    Client-side validation, modals, Kanban & form builders
        ├── main.js            Modal controllers, tab switchers, live search debounce
        ├── student.js         1-Click Apply auto-filler & CGPA ineligibility pop-up
        └── recruiter.js       Dynamic screening form questions builder
```

**The MVC Rule Used Throughout:** A view never executes an SQL query, and a model never prints HTML. The controller sits in the middle: it receives `$_POST`/`$_GET`, applies strict sanitization and validation, invokes the model's prepared statements, and `require`s the presentation view.

---

## 3. How the Router Works

Every URL routes through the single entry point (`index.php`):

```
index.php?controller=<module>&action=<action_name>&id=<optional_id>
```

| Route URL | Handled By | What Happens |
| :--- | :--- | :--- |
| `index.php?controller=auth&action=login` | `AuthController::login` | Renders sign-in form & handles 30-day Remember-Me cookie |
| `index.php?controller=auth&action=register` | `AuthController::register` | Dynamic role registration (Student / Exec / Recruiter) |
| `index.php?controller=student&action=dashboard` | `StudentController::dashboard` | Loads live Opportunity Radar & saved bookmarks |
| `index.php?controller=student&action=apply` | `StudentController::apply` | Validates CGPA cap, answers, and saves application |
| `index.php?controller=executive&action=kanban` | `ExecutiveController::kanban` | Renders 5-column drag-and-drop candidate pipeline |
| `index.php?controller=executive&action=scheduler` | `ExecutiveController::scheduler`| Batch-generates 15-minute interview venue slots |
| `index.php?controller=recruiter&action=talent_search`| `RecruiterController::talentSearch`| Filters verified AIUB student talent by CGPA & department |
| `index.php?controller=admin&action=moderation` | `AdminController::moderation` | Reviews pending campaigns with moderation feedback notes |
| `index.php?controller=admin&action=booths` | `AdminController::booths` | Assigns physical AIUB booth numbers (e.g. Annex 1 Plaza) |
| `index.php?controller=auth&action=logout` | `AuthController::logout` | Terminates session and redirects to sign-in |

Each controller enforces a private **Role Guard** (e.g., `checkStudentAuth()`, `checkExecAuth()`, `checkRecruiterAuth()`, `checkAdminAuth()`). Unauthorized role access attempts are blocked and redirected to login.

---

## 4. Key Architectural Features

### 🎓 1. Student Portal
- **Unified Profile & SOP Vault:** AIUB Student ID (`XX-XXXXX-X`) and Department are permanently locked as verified credentials. Students maintain a cloud resume link and Statement of Purpose (SOP).
- **1-Click Application Auto-Fill:** Clicking apply instantly populates the modal with vault credentials.
- **Minimum CGPA Protection:** If a corporate circular requires a minimum CGPA (e.g. `3.00`) and the student's CGPA is below the threshold, an instant warning modal pops up and blocks ineligible submissions.
- **Duplicate Prevention:** Multi-apply locks prevent redundant applications to the same drive.

### ⚡ 2. Club Executive Portal
- **Recruitment Campaigns:** Create recruitment drives held in pending moderation queue for OSA review.
- **Visual Kanban Pipeline:** Candidate cards move across *Applied ➔ Under Review ➔ Interview ➔ Selected ➔ Rejected* stages.
- **Interview Auto-Scheduler:** Automatically generates 15-minute timeslots between start and end times for specific campus rooms.
- **Demographic Analytics:** Real-time statistical charts for applicant CGPA distributions and member selection rates.

### 💼 3. Corporate Recruiter Portal
- **Dynamic Form Builder:** Build custom screening questions (Portfolio URL, Text prompt, Long Paragraph) saved and rendered dynamically.
- **Strict Position Title Validation:** Recruiter position titles are strictly validated to alphabetic strings (`a-z, A-Z`).
- **Verified Talent Pool Search:** Filter students matching `CGPA >= X.XX` and specific engineering/business departments.
- **Candidate Shortlist Buckets & Direct Alerts:** Save candidates into target buckets and dispatch direct interview invitations.

### 🛡️ 4. University Administrator Portal (OSA)
- **Two-Tier Event Moderation:** Moderate club and corporate campaigns before publishing them to the student radar.
- **Campus Booth Allocator:** Map physical AIUB campus spots (Annex 1 Plaza, D-Building Canopy) to approved event drives.
- **Logistics Requisitions:** Review and track physical campus equipment (microphones, sound systems, tables).

---

## 5. Security & Validation Highlights

- **SQL Injection Prevention:** 100% of database queries use procedural `mysqli_prepare()`, `mysqli_stmt_bind_param()`, and `mysqli_stmt_execute()`.
- **Cross-Site Scripting (XSS):** Global sanitization via `cleanInput()` and `htmlspecialchars()` output escaping.
- **Password Security:** One-way password hashing using PHP `password_hash($pass, PASSWORD_BCRYPT)`.
- **Cookie Security:** Persistent "Remember Me" cookie configured with `HttpOnly` security flags.
- **AIUB Student ID Format:** Strict `XX-XXXXX-X` (2-5-1 digits) formatting with real-time numeric masking and duplicate ID blocking.
- **Native Data Validation:** URL inputs verified using PHP native `filter_var(..., FILTER_VALIDATE_URL)`.

---

## 6. Database Schema (13 Tables)

| Table | Purpose | Managed Entity |
| :--- | :--- | :--- |
| `users` | Accounts & authentication credentials | Core Auth |
| `student_profiles` | AIUB ID, CGPA, Resume URL, Default SOP | Student Entity 1 |
| `opportunity_bookmarks` | Pinned drives and events | Student Entity 2 |
| `club_drives` | Club recruitment campaigns & deadlines | Executive Entity 4 |
| `club_applications` | Candidate applications & Kanban status | Student Entity 3 |
| `interview_slots` | 15-minute room venue timeslots | Executive Entity 5 |
| `candidate_evaluations` | Star ratings (1-5) and fit remarks | Executive Entity 6 |
| `corporate_drives` | Job circulars with CGPA caps | Recruiter Entity 7 |
| `custom_form_fields` | Dynamic screening question schema | Recruiter Entity 7 Component |
| `corporate_applications`| Screening responses & candidate CVs | Recruiter Entity 7 Component |
| `recruiter_shortlists` | Filtered talent candidate buckets | Recruiter Entity 8 |
| `recruiter_outreach_logs`| Direct dashboard alerts sent to students | Recruiter Entity 9 |
| `approval_requests` | Two-tier moderation review queue | Admin Entity 10 |
| `venue_booth_allocations`| Physical AIUB booth reservations | Admin Entity 11 |
| `logistics_requests` | Event equipment inventory requisitions | Admin Entity 12 |

---

## 7. License & Credits

Developed for the **Web Technologies** course at the **American International University - Bangladesh (AIUB)**.
Created by the **CampusXP Development Team** (Academic Year 2026).
