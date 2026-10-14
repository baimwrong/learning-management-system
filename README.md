# Learning Management System (LMS)

> **Production-Ready Enterprise Learning Management System**  
> Built with **Laravel 13**, **PostgreSQL 18**, **Bootstrap 5.3**, and **Bootstrap Icons & FontAwesome 6**.  
> **100% Offline & Internal Network (Air-Gapped) Ready** — Zero external CDN dependencies.

---

## 🌟 Overview

**Learning Management System** is a modular, high-concurrency Learning Management System engineered for enterprise workforce upskilling, university curriculums, and internal intranet training environments. 

The platform is designed to operate completely self-contained on isolated local networks (intranets) without requiring public internet access. All stylesheets, icons, fonts, and scripts are bundled directly in the project.

---

## 🚀 Key Highlights & Tech Stack

| Layer | Technology | Details |
|---|---|---|
| **Backend Framework** | **Laravel 13** | Built on PHP 8.3 / 8.4 with modern streamlined bootstrap & route architecture |
| **Database Engine** | **PostgreSQL 18** | High-performance schema leveraging native `JSONB` for quiz structures & bulk import logs |
| **Frontend UI** | **Bootstrap 5.3.3** | Clean, responsive design, dark-mode ready color palettes, cards, and modal dialogs |
| **Icon Libraries** | **Bootstrap Icons & FontAwesome 6** | Downloaded locally into `public/vendor/` with full WOFF2/WOFF font bundling |
| **Ingestion Engine** | **Admin Bulk Import Center** | High-speed transactional CSV ingestion for Users, Courses, Questions, and Enrollments |
| **Network Mode** | **Internal Network / Air-Gapped** | Runs with zero internet connectivity; no external Google Fonts or CDN requests |

---

## 🎓 Core Capabilities

### 1. 🛡️ Role-Based Portals
* **System Administrator**: Full platform moderation, user directory management, category administration, course moderation, review approvals, and the **Bulk Data Ingestion Center**.
* **Instructor Studio**: Course authoring, module & curriculum hierarchy builder, video/reading lesson manager, auto-graded quiz question bank creator, and enrolled student tracking.
* **Student Portal**: Course catalog browsing, instant enrollment, interactive learning player, automated quiz taking with timers, lesson Q&A discussions, and verifiable certificate issuance.
* **One-Click Demo Switcher**: Dedicated toolbar allowing immediate role swapping between Admin, Instructor, and Student for testing.

### 2. ⚡ Admin Bulk Data Import Center
A dedicated ingestion hub for importing large datasets via CSV with row-by-row validation:
* **Users & Accounts**: Ingest students, faculty, or staff with automatic bcrypt password hashing and role assignments.
* **Course Catalog**: Ingest full course definitions with category resolution, pricing, and assigned instructor mapping.
* **Quiz Question Bank**: Ingest single-choice, multiple-choice, and true/false assessment questions with answer keys and explanations.
* **Student Enrollments**: Ingest bulk course registrations.
* **PostgreSQL JSONB Error Telemetry**: Every failed row is captured with line number, error diagnostic message, and raw submitted payload for instant troubleshooting.
* **Downloadable Templates**: Pre-configured sample CSV templates available directly from the admin interface.

### 3. 🖥️ Interactive Learning Player
* **Distraction-Free Classroom**: Fullscreen learning portal with fixed navigation and sticky progress tracking.
* **Curriculum Sidebar**: Dynamic module accordion with completion checkmarks and instant lesson navigation.
* **Multimodal Content**: Support for video lectures, rich HTML/markdown technical articles, and embedded quiz assessments.
* **Lesson Q&A Forum**: Integrated discussion thread beneath each lesson with instructor reply badges.

### 4. 📝 Quiz & Assessment Engine
* Single choice, multiple choice, and true/false question types.
* Auto-grading on submission with pass/fail evaluation based on configurable passing thresholds.
* Optional countdown timer with automatic submission on expiration.
* Diagnostic post-exam breakdown explaining correct answers and technical rationales.

### 5. 🏅 Verifiable Certificates
* Automatically awarded when a student achieves **100% curriculum completion**.
* Issued with a unique cryptographic verification identifier (e.g., `LMS-2026-PG18-9842`).
* Public verification endpoint (`/verify-certificate/{code}`) for third-party validation.
* Dedicated printable/downloadable certificate view with seal, instructor signature, and print CSS.

---

## 🗄️ Database Architecture (PostgreSQL 18)

The Learning Management System includes 6 dedicated migration sets optimized for PostgreSQL 18:

```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2026_01_01_000001_create_categories_table.php
├── 2026_01_01_000002_create_courses_and_curriculum_tables.php
├── 2026_01_01_000003_create_quizzes_and_assessments_tables.php
├── 2026_01_01_000004_create_enrollments_progress_and_certificates_tables.php
├── 2026_01_01_000005_create_discussions_and_reviews_tables.php
└── 2026_01_01_000006_create_bulk_imports_table.php
```

### PostgreSQL 18 JSONB Features:
* `users.metadata`: Custom profile attributes and settings.
* `courses.requirements` & `courses.learning_outcomes`: Indexed prerequisite lists.
* `quiz_questions.options` & `quiz_questions.correct_answers`: Dynamic assessment choices.
* `quiz_attempts.answers_submitted`: Full diagnostic submission audit trails.
* `bulk_imports.error_log`: Detailed row-by-row validation error telemetry.

---

## 💻 Installation & Setup Guide

### Prerequisites
* **Laragon** (installed) or local PHP runtime:
  * **PHP >= 8.3** (Laragon provides PHP 8.3 & 8.4 in `C:\laragon\bin\php\`)
  * PHP extensions enabled: `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `curl`, `fileinfo`
* **PostgreSQL 18**:
  * Default port: `5432`
  * Default database: `learning_management_system`
* **Composer** (installed)

---

### Step 1: PostgreSQL 18 Setup (via Laragon or Standalone)
1. In PostgreSQL 18, create the database:
   ```sql
   CREATE DATABASE learning_management_system;
   ```
2. Open `.env` and verify your credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=learning_management_system
   DB_USERNAME=postgres
   DB_PASSWORD=postgres
   DB_SCHEMA=public
   DB_SSLMODE=prefer
   ```

---

### Step 2: Initialize Database & Seed Demo Data
Run migrations and the comprehensive seed dataset using PHP 8.3/8.4 (e.g. from Laragon or system PATH):

```bash
# If using Laragon PHP 8.3 directly:
C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan migrate:fresh --seed

# Or standard artisan command (when Laragon is active):
php artisan migrate:fresh --seed
```

---

### Step 3: Run the Web Application
Start the local server:
```bash
php artisan serve
```
The application will be accessible at: **`http://localhost:8000`**

---

## 🔑 Default Accounts (Database Seeder)

The Learning Management System includes pre-seeded accounts for instant local testing:

| Role | Email Address | Password | Permissions |
|---|---|---|---|
| **Administrator** | `admin@lms.local` | `Admin123!` | Complete administrative control & Bulk Import Center |
| **Instructor** | `instructor@lms.local` | `Instructor123!` | Course authoring, curriculum & quiz builder |
| **Instructor 2** | `ford@lms.local` | `Instructor123!` | Senior database researcher & instructor |
| **Student** | `student@lms.local` | `Student123!` | Enrolled learner with active & completed courses |
| **Student 2** | `alice@lms.local` | `Student123!` | Student enrolled in Laravel 13 course |

> 💡 **Quick-Login Toolbar**: In development mode, click any role icon in the top header bar to switch accounts without entering passwords.

---

## 📁 Bulk Import CSV File Specifications

Sample CSV files are provided in `database/data/sample_imports/`:

### 1. Users Import (`users_sample.csv`)
```csv
name,email,role,password,phone,bio
Samantha Reed,samantha.reed@example.com,student,StudentPass123!,+1-555-0199,Undergraduate researcher in data structures
Dr. Elena Rostova,elena.rostova@example.com,instructor,InstructorPass123!,+1-555-0188,Associate Professor of Distributed Systems
```

### 2. Courses Import (`courses_sample.csv`)
```csv
title,category,instructor_email,level,price,summary,description,is_published
Zero Trust Architecture with TLS 1.3,Cybersecurity,ford@lms.local,advanced,99.00,Deploy mutual TLS and zero-trust perimeter defenses,In this hands-on course you will design defense-in-depth architectures.,1
```

### 3. Quiz Questions Import (`quiz_questions_sample.csv`)
```csv
course,quiz_title,question_text,question_type,options,correct_answer,points,explanation
mastering-postgresql-18-architecture-and-tuning,PostgreSQL 18 Architecture Mastery Exam,Which command is used to reclaim dead tuple storage in PostgreSQL?,single_choice,PRUNE|PURGE|VACUUM|CLEANUP,C,2,VACUUM reclaims storage occupied by dead tuples.
```

### 4. Enrollments Import (`enrollments_sample.csv`)
```csv
student_email,course,status
alice@lms.local,mastering-postgresql-18-architecture-and-tuning,active
```

---

## 📦 Offline Assets Directory Layout

All frontend assets are located locally in `public/vendor/`:
```
public/vendor/
├── bootstrap/
│   ├── css/bootstrap.min.css
│   └── js/bootstrap.bundle.min.js
├── bootstrap-icons/
│   ├── bootstrap-icons.min.css
│   └── fonts/
│       ├── bootstrap-icons.woff
│       └── bootstrap-icons.woff2
└── fontawesome/
    ├── css/all.min.css
    └── webfonts/
        ├── fa-solid-900.woff2
        ├── fa-regular-400.woff2
        └── fa-brands-400.woff2
```

---

## 📂 Project Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php         # Admin metrics, users, courses, moderation
│   │   ├── AuthController.php          # Authentication & quick role switcher
│   │   ├── BulkImportController.php    # Admin bulk CSV upload & telemetry
│   │   ├── CertificateController.php   # Printable certificates & public verification
│   │   ├── HomeController.php           # Landing page & course catalog
│   │   ├── InstructorController.php     # Course curriculum & quiz builder
│   │   └── StudentController.php        # Player, lesson completion, quiz submit
│   └── Middleware/
│       └── EnsureUserHasRole.php       # Role guard middleware
├── Models/
│   ├── BulkImport.php                  # Import tracking with JSONB error log
│   ├── Category.php                    # Course taxonomy
│   ├── Certificate.php                 # Verifiable completion credentials
│   ├── Course.php                      # Course master model
│   ├── CourseModule.php                # Sections & modules
│   ├── CourseReview.php                # Student feedback & ratings
│   ├── Discussion.php                  # Lesson Q&A community threads
│   ├── Enrollment.php                  # Student enrollment & automated progress
│   ├── Lesson.php                      # Video, reading, and quiz lessons
│   ├── LessonCompletion.php            # Lesson completion tracker
│   ├── Quiz.php                        # Assessment container
│   ├── QuizAttempt.php                 # Graded attempts & submission details
│   ├── QuizQuestion.php                # Question bank & scoring options
│   └── User.php                        # User accounts & role helpers
└── Services/
    └── BulkImportService.php           # Transactional CSV reader & validator
```

---

## 📄 License
This Learning Management System application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
