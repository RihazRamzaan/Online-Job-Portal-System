CREATE DATABASE IF NOT EXISTS job_portal_db;
USE job_portal_db;

-- Admin/recruiter accounts
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,   -- store hashed with password_hash()
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Job categories for filtering
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL   -- IT, Healthcare, Finance, etc.
);

-- Job postings
CREATE TABLE IF NOT EXISTS jobs (
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
CREATE TABLE IF NOT EXISTS applications (
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

-- --------------------------------------------------------
-- SAMPLE DATA INSERTS
-- --------------------------------------------------------

-- 1. Insert an Admin (Password is 'password')
INSERT IGNORE INTO admins (admin_id, full_name, email, password) VALUES 
(1, 'Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 2. Insert Categories
INSERT IGNORE INTO categories (category_id, category_name) VALUES 
(1, 'IT & Software'),
(2, 'Healthcare'),
(3, 'Finance'),
(4, 'Marketing');

-- 3. Insert Jobs
INSERT IGNORE INTO jobs (job_id, title, company_name, company_logo, location, salary_min, salary_max, job_type, category_id, description, status, posted_by) VALUES 
(1, 'Senior Full Stack Developer', 'TechCorp', 'T', 'Remote', 90000, 120000, 'Remote', 1, 'We are looking for an experienced Full Stack Developer to lead our backend team.', 'active', 1),
(2, 'Nursing Assistant', 'HealthFirst Clinic', 'H', 'New York, NY', 40000, 50000, 'Full-Time', 2, 'Join our amazing healthcare team. Must be certified and willing to work night shifts.', 'active', 1),
(3, 'Financial Analyst', 'MoneyBank', 'M', 'Chicago, IL', 70000, 95000, 'Full-Time', 3, 'Looking for an analyst with 3+ years of experience in financial modeling and projections.', 'active', 1),
(4, 'Digital Marketing Specialist', 'AdMakers', 'A', 'San Francisco, CA', 60000, 80000, 'Part-Time', 4, 'Help us manage our social media campaigns and run targeted ad sets.', 'active', 1),
(5, 'Junior PHP Developer', 'WebSolutions', 'W', 'Remote', 50000, 70000, 'Remote', 1, 'Great opportunity for a junior developer. Experience with vanilla PHP and MySQL required.', 'active', 1),
(6, 'Systems Administrator', 'TechCorp', 'T', 'Austin, TX', 75000, 95000, 'Full-Time', 1, 'Maintain and upgrade our IT infrastructure. Looking for someone with Linux expertise.', 'active', 1);

-- 4. Insert Applications
INSERT IGNORE INTO applications (application_id, job_id, applicant_name, applicant_email, cover_letter, payment_status, payment_amount, transaction_ref) VALUES 
(1, 1, 'Alice Smith', 'alice@example.com', 'I have 5 years of full stack experience using vanilla JS and PHP.', 'paid', 5.00, 'TXN64A3F1'),
(2, 2, 'Bob Jones', 'bob@example.com', 'I am a certified nursing assistant with 2 years experience.', 'pending', 5.00, NULL),
(3, 1, 'Charlie Brown', 'charlie@example.com', 'Very interested in the Senior Developer role. I have architected 3 major systems.', 'paid', 5.00, 'TXN64B9D2');
