# PROJECT_CONTEXT.md — Online Job Portal System
SENG 21253 – Web Application Development (Practical) | 4-hour build

> **Usage note:** Commit this file as-is into the repo root as `PROJECT_CONTEXT.md`. Every member points their AI coding assistant at this file before generating code, so all 4 machines produce consistent, framework-free, similarly-styled output.

> **Tech stack constraint:** Pure **HTML, CSS, JavaScript, and PHP** only (with MySQL as the database, accessed via raw `mysqli`/`PDO` — never an ORM). No CSS frameworks (Bootstrap/Tailwind), no JS frameworks/libraries (React/Vue/jQuery), no PHP frameworks (Laravel/CodeIgniter). Everything hand-written so every member can explain every line.

---

## For AI Coding Assistants (Copilot, Cursor, Claude Code, etc.)

**Read this block before generating any code in this repository.** This is a timed team exam with 4 collaborators working in separate IDEs. Follow these rules exactly:

- **Stack:** HTML, CSS, JavaScript, PHP, and MySQL only. Never suggest, install, or import a framework, UI library, JS library, PHP framework, or ORM — even if "easier" or "more standard." Vanilla code only.
- **Payment is a MOCK/simulated flow, not a real gateway.** Do not attempt to integrate Stripe, PayPal, or any real payment API. Build a simple form that collects dummy card-style fields, validates format only (not real card validity), and on submit marks the application's `payment_status` as `'paid'` with a generated fake `transaction_ref`. Comment clearly that this is simulated for demo purposes.
- **Database access:** `mysqli` or `PDO` with **prepared statements only**. Never build SQL with string concatenation.
- **Naming conventions:** `snake_case` for database tables/columns and PHP variables/functions; `camelCase` for JavaScript variables/functions; file names in `snake_case.php` / `snake_case.js` matching the endpoint contract below.
- **File ownership:** Do not create, rename, or restructure files/folders outside the structure in Section 2. Flag new-file needs to the team rather than creating them unprompted.
- **Shared files** (`header.php`, `footer.php`, and everything under `css/`) belong to one owner (see `README.md`) — don't modify unless you are that owner.
- **UI style:** Clean, minimalistic (see Section 5) — neutral palette, one accent color, one font family, generous whitespace, consistent buttons/tables/forms. No gradients, animations, or icon libraries.
- **Code comments are mandatory.** Every function/query/non-trivial block needs a comment explaining what it does and why — e.g. `// Prepared statement + bound params to prevent SQL injection`. You will be questioned live on your own files.
- **Security basics always included:** `password_hash()`/`password_verify()` for admin passwords, `session_start()` + `$_SESSION` for admin login state, server-side validation on every form even when client-side validation exists.
- **Strictly no editing outside your assigned files**, even for "helpful" fixes — flag the issue to the owner instead.
- **When unsure, match existing code in the file/folder** rather than introducing a new pattern.

---

## 1. Actors & Core Features (from the spec)

**Job Seeker (no login required)**
- Browse job board (title, company, location, salary range, mock logo)
- Search by title; filter by category and job type
- Apply: fill form (name, email, cover letter) → pay mock "Premium Application Fee" → application saved

**Administrator/Recruiter (login required)**
- Secure login to admin dashboard
- Job CRUD: create, view, update (salary/description/status), delete postings
- View all applications with applicant details and payment status

---

## 2. Database Schema (MySQL)

```sql
CREATE DATABASE job_portal_db;
USE job_portal_db;

-- Admin/recruiter accounts
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,   -- store hashed with password_hash()
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Job categories for filtering
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL   -- IT, Healthcare, Finance, etc.
);

-- Job postings
CREATE TABLE jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    company_logo VARCHAR(10) DEFAULT NULL,  -- mock logo: store initial letter, generate avatar in CSS/HTML
    location VARCHAR(100) NOT NULL,
    salary_min INT,
    salary_max INT,
    job_type ENUM('Full-Time','Part-Time','Remote') NOT NULL,
    category_id INT,
    description TEXT,
    status ENUM('active','closed') DEFAULT 'active',
    posted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (posted_by) REFERENCES admins(admin_id)
);

-- Job applications (includes mock payment tracking)
CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_name VARCHAR(100) NOT NULL,
    applicant_email VARCHAR(100) NOT NULL,
    cover_letter TEXT,
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    payment_amount DECIMAL(6,2) DEFAULT 5.00,   -- mock "Premium Application Fee"
    transaction_ref VARCHAR(50),                -- mock/fake transaction id, e.g. uniqid()
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id)
);
```

**Why this schema fits the spec:** `jobs` ↔ `categories` and `jobs` ↔ `admins` cover the two required foreign keys; `applications` ↔ `jobs` lets you demo a join for "view all applications with job title" in the admin dashboard. `payment_status`/`payment_amount`/`transaction_ref` directly model the mock payment flow without needing a separate payments table — simpler to explain in the architecture Q&A.

---

## 3. Folder / File Structure

```
project/
│
├── PROJECT_CONTEXT.md
├── README.md
│
├── config/
│   └── db.php                       # DB connection (mysqli or PDO)
│
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php                # list of jobs, admin view (Read)
│   ├── add_job.php                  # Create
│   ├── edit_job.php                 # Update
│   ├── delete_job.php               # Delete
│   └── view_applications.php        # all applications + payment status
│
├── jobs/
│   └── index.php                    # public job board: list + search/filter
│
├── apply/
│   ├── application_form.php         # ?job_id=X — name/email/cover letter form
│   ├── payment.php                  # mock payment form
│   └── submit_application.php       # processes form + mock payment, inserts DB row
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── validate.php                 # shared server-side validation functions
│
├── css/
│   ├── variables.css                # colors, fonts, spacing as CSS custom properties
│   ├── base.css                     # reset, body defaults, typography
│   ├── layout.css                   # header/footer/nav, page containers
│   ├── forms.css                    # inputs, labels, buttons, focus states
│   ├── tables.css                   # admin tables: borders, striping, padding
│   └── components.css               # job cards, mock logo avatars, status badges
│
├── js/
│   ├── validation.js                # client-side form validation
│   └── main.js                      # search/filter behavior, dynamic UI
│
└── index.php                        # redirects to jobs/index.php
```

---

## 4. Endpoint Contract (agree on this FIRST, as a team)

| File | Method | Expects | Returns |
|---|---|---|---|
| `admin/login.php` | POST | `email`, `password` | session + redirect to `dashboard.php`, or error |
| `admin/logout.php` | GET | — | destroys session, redirect to login |
| `admin/add_job.php` | POST | `title`, `company_name`, `location`, `salary_min`, `salary_max`, `job_type`, `category_id`, `description` | success/fail message |
| `admin/edit_job.php` | POST | `job_id` + updated fields | success/fail message |
| `admin/delete_job.php` | POST | `job_id` | success/fail message |
| `admin/dashboard.php` | GET | — | admin's list of jobs |
| `admin/view_applications.php` | GET | optional `job_id` filter | applications joined with job title + payment status |
| `jobs/index.php` | GET | optional `search`, `category_id`, `job_type` | public job listing |
| `apply/application_form.php` | GET | `job_id` | job summary + application form |
| `apply/payment.php` | GET/POST | applicant draft data (session or hidden fields) | mock payment form → posts to `submit_application.php` |
| `apply/submit_application.php` | POST | `job_id`, `name`, `email`, `cover_letter`, mock payment fields | inserts application row (`payment_status = 'paid'`), shows confirmation |

Agreeing on **exact field names** up front is the biggest time-saver — it lets frontend and backend build simultaneously instead of blocking each other.

---

## 5. UI Design Guidelines — Keep It Clean, Minimalistic, Simple

**Layout**
- Generous whitespace, `max-width` container (~1000px) centered, not stretched full-width
- One consistent shell (`header.php`/`footer.php`) reused on every page
- Job board as a card grid; admin dashboard as a table

**Color**
- One neutral base (white/light gray) + one accent color (used only for buttons/links/status highlights) + one dark text color
- Payment status can use color meaningfully: green for paid, amber for pending, red for failed — don't overuse color elsewhere

**Typography**
- One font family (`font-family: 'Segoe UI', Arial, sans-serif;`), 2–3 sizes max, line-height 1.4–1.6

**Mock Company Logo**
- No real image assets needed — generate a simple **initial-letter avatar**: a colored circle/square with the company's first letter, done purely in CSS (`.logo-avatar { ... }`) using the `company_logo` column or `substr($company_name, 0, 1)`

**Components**
- Job cards: title, company + logo avatar, location, salary range, job type badge, "Apply" button
- Buttons: consistent padding, small border-radius, one style per action type (primary / danger-delete)
- Admin tables: light borders or zebra-striping, adequate cell padding
- Status badges (payment status, job status) as small colored pill labels — simple, not decorative

**Practical tip for the 4-hour constraint**
Write `variables.css` and `base.css` first (10–15 min) since everything else depends on them. Reuse the same button/card/table/badge classes everywhere instead of styling each page from scratch.

---

## 6. Presentation-Readiness Checklist

The spec names three explicit presentation sections — prepare for each directly:

- [ ] **System Architecture** — each member can explain the schema, the 3 foreign key relationships, and why the mock payment fields live inside `applications` rather than a separate table
- [ ] **System Simulation (live demo)** — rehearsed end-to-end: admin logs in → posts a new job → job seeker finds it via search/filter → applies → mock payment → application appears in admin's application view with `payment_status = paid`
- [ ] **AI Usage discussion** — each member can describe specifically what they asked their AI assistant for, what it got wrong or needed correcting, and one thing they had to fix or understand themselves
- [ ] Every function/query has an explanatory comment
- [ ] No member edited another member's assigned files
- [ ] Passwords hashed, not stored in plain text; admin sessions handled correctly
- [ ] UI is clean, minimalistic, and visually consistent across all pages
- [ ] Enough clean sample data (jobs + a couple of test applications) left in the DB for the live demo — don't leave it mid-test and broken
