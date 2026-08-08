# 🎓 EduNova — Enterprise School LMS & ERP Platform

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![License: MIT](https://img.shields.io/badge/License-MIT-emerald?style=for-the-badge)](LICENSE)

> A modern, enterprise-grade **School Learning Management System (LMS) and Enterprise Resource Planning (ERP)** platform. Designed with **Glassmorphism UI**, dynamic dark/light theme switching, multi-layer RBAC security, and 4 specialized portals for Administrators, Teachers, Students, and Parents.

---

## 🌟 Key Highlights & Feature Matrix

```
  ┌────────────────────────────────────────────────────────────────────────┐
  │                           EduNova ERP Platform                         │
  ├───────────────────┬────────────────────┬───────────────────────────────┤
  │ 🏛️ Super Admin    │ 👨‍🏫 Teacher Desk    │ 👨‍👩‍👧 Parent & 🎓 Student     │
  │ • Academic Setup  │ • Live Timetable   │ • Multi-Child Switcher        │
  │ • Student Wizard  │ • Daily Attendance │ • Term Report Cards & GPA     │
  │ • Faculty HR      │ • Exam Gradebook   │ • Online Card & Cash Payments │
  │ • 1-Click Fees    │ • LMS Studio (HW)  │ • Homework File Uploads       │
  │ • RBAC Matrix     │ • Printable Slips  │ • Download Study Handouts     │
  │ • Auxiliaries     │ • Score Submissions│ • Live Presence Tracker       │
  └───────────────────┴────────────────────┴───────────────────────────────┘
```

### 1. 🏛️ Super Admin & Leadership Portal
- **Executive KPI Dashboard**: Live revenue trends, student enrollment counts, and aggregate attendance analytics.
- **Academic Hierarchy**: Manage Sessions, Classes, Sections, Curriculum Subjects, and Time Slots.
- **Multi-Step Student Admission Wizard**: Personal demographics, academic placements, parent guardian connections, transport, and dormitory room allocation with automatic fee invoicing.
- **Faculty HR & Teaching Allocations**: Employee registration, basic salary setup, and subject-section teaching assignment matrix.
- **1-Click Batch Invoicing Engine**: Automatically generates individualized fee invoices across whole classes.
- **HR Cash Payment Approval Queue**: Verify and approve physical cash/bank payments with audit trails.
- **Enterprise RBAC Matrix**: Create custom security roles, edit display titles, and grant/revoke capabilities with 1-click toggles.
- **7 Auxiliary Subsystems**:
  - 📚 **Library**: ISBN catalog, book loans, and return ledger.
  - 🚌 **Transport**: Bus fleet, route management, driver rosters, and rider allocations.
  - 🏢 **Hostel**: Residential dormitory buildings, rooms, and bed capacities.
  - 📦 **Inventory**: School asset tracking, categories, and valuations.
  - 💰 **Faculty Payroll**: Monthly salary vouchers with allowances, deductions, and print/downloadable payslips.
  - 📢 **Notice Bulletins**: Broadcast announcements to target audiences (`all`, `teacher`, `student`, `parent`).

### 2. 👨‍🏫 Faculty Teacher Workspace
- **Live Timetable Schedule**: Today's active lectures and room allocations.
- **Daily Attendance Matrix**: Fast 1-click batch selection (`Present`, `Late`, `Absent`, `Excused`) with custom remarks.
- **Exam Assessment Gradebook**: Real-time score entry with instant percentage and Grade Rule evaluation (`A+`, `A`, `B`, `C`, `D`, `F`, GPA).
- **LMS Studio**: Upload lecture notes, PDF study handouts, and create homework assignments with deadlines.
- **Homework Grading Desk**: Download student uploaded files, enter marks, and provide feedback.
- **My Salary Payslips**: View, print, and download official monthly salary slips.

### 3. 🎓 Enrolled Student Hub
- **Learning Dashboard**: Class schedule, active homework deadlines, and term results.
- **Weekly Timetable Grid**: Full weekly lecture schedule with break intervals.
- **Study Materials Center**: 1-click download of teacher handouts and lecture notes.
- **Homework Submission Desk**: Upload solutions (PDF, DOCX, ZIP) and review grading remarks.
- **Printable Official Report Card**: Cumulative GPA, total marks, percentage, and signature blocks.
- **Student Fees**: View billing ledger and printable receipts.

### 4. 👨‍👩‍👧 Parent Guardian Portal
- **Multi-Child Switcher Dropdown**: Seamlessly switch between multiple children enrolled in the school.
- **Real-Time Attendance Alerts**: Daily presence logs and aggregate attendance rate.
- **Child Term Report Cards**: Track academic grades, GPA, and term scores.
- **Dual Payment Modes**:
  - 💳 **Instant Online Card Payment** (Direct automated settlement).
  - 💵 **Cash at School Desk** (Submits for HR counter verification).
- **Official Print-Ready Receipts**: Download vouchers with verified audit trails.

### 5. 🔔 Real-Time Notification & Popup Alerts
- **Glassmorphic Floating Toast**: Automatically slides in with audio chime when new announcements are published.
- **Global Bell Drawer**: Unread badge counter and role-targeted announcements.

---

## 🔑 Demo Login Credentials

> **Default Password for ALL Accounts:** `password123`

| Portal Role | Demo Email | Access Capabilities |
|---|---|---|
| **Super Admin / Principal** | `admin@school.com` | Full school administration, admissions, fees, HR, and RBAC |
| **Faculty Teacher 1** | `teacher@school.com` | Mathematics & Physics, daily attendance, gradebook & LMS |
| **Faculty Teacher 2** | `sarah@school.com` | Computer Science & AI, attendance, gradebook & LMS |
| **Parent Guardian** | `parent@school.com` | Multi-child switcher (Ethan & Emma Davis), report cards & online fees |
| **Student 1** | `student@school.com` | Grade 10-A (Ethan Davis) — homework, timetable & report cards |
| **Student 2** | `emma@school.com` | Grade 9-A (Emma Davis) — homework, timetable & report cards |
| **Student 3** | `lucas@school.com` | Grade 10-A (Lucas Sterling) — homework, timetable & report cards |

---

## 🛠️ Tech Stack & Architecture

- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL 8.0+
- **Frontend / Templating:** Laravel Blade with Tailwind CSS & Alpine.js
- **Design System:** Glassmorphism (`glass-card`, `ambient-bg`, `flat-table-wrapper`)
- **Visual Analytics:** Chart.js
- **Security Middlewares:** Custom RBAC traits, IDOR guards (`parent.child`, `student.self`), and active account verification (`active`).

---

## 🚀 Installation & Local Setup

### 1. Clone the Repository
```bash
git clone https://github.com/MuhammadIbad1/school-lms-erp-platform.git
cd school-lms-erp-platform
```

### 2. Install PHP & Node Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Setup Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure MySQL Database
Update your `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_lms
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations & Seed Database
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 6. Start the Development Server
```bash
php artisan serve
```
Open **`http://127.0.0.1:8000`** in your browser and use the **1-Click Demo Login Switcher**!

---

## 📄 License
This project is open-sourced software licensed under the [MIT License](LICENSE).
