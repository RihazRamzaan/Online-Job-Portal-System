# Online Job Portal System

A vanilla web application built for the **SENG 21253 – Web Application Development (Practical)** 4-hour build challenge. 

For full project constraints, schema details, and endpoint contracts, please see the [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md).

## 🚀 Tech Stack
- **Frontend:** Pure HTML5, Vanilla CSS3, Vanilla JavaScript (No libraries/frameworks).
- **Backend:** Pure PHP (No frameworks).
- **Database:** MySQL (accessed via `mysqli` with prepared statements).

## 👥 Team & File Ownership

As per the strict exam constraints, every team member was strictly responsible for specific files to avoid conflicts.

| Role | Member Focus | Assigned Files / Folders |
|:---|:---|:---|
| **Member A** | Database & Admin CRUD | `config/db.php`, `admin/add_job.php`, `admin/dashboard.php`, `admin/edit_job.php`, `admin/delete_job.php` |
| **Member B** | Auth, Public Jobs Backend, Mock Payment | `admin/login.php`, `admin/logout.php`, `includes/validate.php`, `jobs/index.php`, `apply/submit_application.php`, `apply/payment.php`, `admin/view_applications.php` |
| **Member C** | Frontend Structure, HTML, CSS | `includes/header.php`, `includes/footer.php`, `apply/application_form.php`, All files inside the `css/` directory |
| **Member D** | Client-Side Logic (JavaScript) | All files inside the `js/` directory (`validation.js`, `main.js`) |

## ⚙️ Setup & Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/RihazRamzaan/Online-Job-Portal-System.git
   ```
2. **Server Environment:** 
   Place the project folder inside your `www` folder (for WAMP) or `htdocs` folder (for XAMPP).
3. **Database Setup:** 
   Open `http://localhost/phpmyadmin/` and import the provided `setup.sql` file. This will automatically create the `job_portal_db` database and all necessary tables.
4. **Database Configuration:** 
   Verify that your database port and credentials match the settings in `config/db.php`.
5. **Run the Application:** 
   Navigate to the project root in your browser (e.g., `http://localhost/root/Online-Job-Portal-System/`). It will automatically redirect you to the public job board.

## 🎯 Core Features
- **Job Seekers:** Browse active job listings, filter by category/type, perform real-time live searches, and submit job applications via a simulated payment gateway.
- **Administrators:** Secure dashboard login, create/edit/delete job postings, and review all incoming applications along with their payment statuses.