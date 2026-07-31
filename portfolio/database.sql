-- ============================================================
--  PORTFOLIO DATABASE
--  Web Developer Portfolio + Admin Panel
--  Tech: PHP, MySQL
--  Run this file in phpMyAdmin or via mysql CLI to install.
--  Default admin login:  admin  /  admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- ------------------------------------------------------------
--  Admin users
-- ------------------------------------------------------------
DROP TABLE IF EXISTS admin_users;
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- password hash for: admin123
INSERT INTO admin_users (username, email, password) VALUES
('admin', 'admin@example.com', '$2y$10$ephC/5aLd7jC93.SNaTSCuCA116MfSA8HqKy3ODN14tjp/yTCs7Ny');

-- ------------------------------------------------------------
--  Projects (portfolio pieces)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  category VARCHAR(100) NOT NULL,
  tech_stack VARCHAR(255) NOT NULL,
  description TEXT,
  image VARCHAR(255) DEFAULT NULL,
  url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO projects (title, category, tech_stack, description, url) VALUES
('Business Landing Page', 'Website', 'HTML, CSS, JavaScript, Bootstrap', 'A modern, responsive landing page built for a local business with animated sections and an enquiry form.', 'https://example.com'),
('E-Commerce Store', 'Web App', 'PHP, MySQL, Bootstrap, JavaScript', 'Full online store with product catalogue, shopping cart and order management.', 'https://example.com'),
('School Management System', 'Web App', 'PHP, MySQL, Angular.js', 'Dashboard for managing students, attendance and results with role-based access.', 'https://example.com'),
('Restaurant Website', 'WordPress', 'WordPress, Elementor, PHP', 'WordPress site with menu, reservations and SEO-optimised content.', 'https://example.com'),
('Portfolio Template', 'Website', 'Mobirise, Bootstrap, CSS', 'Clean one-page template built with Mobirise and customised with Bootstrap.', 'https://example.com');

-- ------------------------------------------------------------
--  Services
-- ------------------------------------------------------------
DROP TABLE IF EXISTS services;
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  icon VARCHAR(60) NOT NULL DEFAULT 'bi-code-slash',
  description TEXT,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO services (title, icon, description, sort_order) VALUES
('Web Development', 'bi-code-slash', 'Custom websites and web applications built with HTML, CSS, JavaScript, PHP and MySQL.', 1),
('WordPress Development', 'bi-wordpress', 'Professional WordPress themes, plugins and full site setup with Elementor and WooCommerce.', 2),
('UI / UX & Responsive Design', 'bi-palette', 'Mobile-first, pixel-perfect designs using Bootstrap and modern CSS techniques.', 3),
('Mobirise Sites', 'bi-columns-gap', 'Fast drag-and-drop website building with Mobirise, customised to match your brand.', 4);

-- ------------------------------------------------------------
--  Skills
-- ------------------------------------------------------------
DROP TABLE IF EXISTS skills;
CREATE TABLE skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  percent INT NOT NULL DEFAULT 80,
  category VARCHAR(60) NOT NULL DEFAULT 'Web'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO skills (name, percent, category) VALUES
('HTML5', 95, 'Web'),
('CSS3 / Bootstrap', 90, 'Web'),
('JavaScript / TypeScript', 85, 'Web'),
('Angular.js', 80, 'Web'),
('PHP', 85, 'Backend'),
('MySQL', 80, 'Backend'),
('WordPress', 88, 'CMS'),
('Mobirise', 90, 'CMS');

-- ------------------------------------------------------------
--  Contact messages (from the portfolio form)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS messages;
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO messages (name, email, subject, message, is_read) VALUES
('Jane Doe', 'jane@example.com', 'Project enquiry', 'Hi, I would like a quote for a business website. Please get in touch.', 1),
('John Smith', 'john@example.com', 'WordPress help', 'Need help fixing my WordPress site speed issues.', 0);

-- ------------------------------------------------------------
--  Settings (site content shown on the portfolio)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Alex Carter - Web Developer'),
('hero_greeting', 'Hello, I am'),
('hero_name', 'Alex Carter'),
('hero_role', 'Web Developer & Designer'),
('hero_tagline', 'I build fast, modern and responsive websites using HTML, CSS, JavaScript, PHP, MySQL, Angular.js, WordPress and Mobirise.'),
('hero_btn_text', 'View My Work'),
('about_title', 'About Me'),
('about_text', 'I am a passionate web developer with experience building websites and web applications for small businesses and startups. I love turning ideas into clean, functional and beautiful digital products using modern technologies like PHP, MySQL, Angular.js and WordPress.'),
('about_photo', ''),
('resume_title', 'Resume'),
('resume_text', 'Web Developer with 5+ years of experience delivering responsive, SEO-friendly websites and web apps.'),
('experience_1_year', '2021 - Present'),
('experience_1_role', 'Freelance Web Developer'),
('experience_1_text', 'Designing and building custom websites, WordPress themes and e-commerce stores for clients worldwide.'),
('experience_2_year', '2019 - 2021'),
('experience_2_role', 'Junior Web Developer'),
('experience_2_text', 'Maintained and extended client websites built with HTML, CSS, Bootstrap and PHP.'),
('education_1_year', '2015 - 2019'),
('education_1_role', 'BSc Computer Science'),
('education_1_text', 'Graduated with a focus on web technologies and software engineering.'),
('footer_text', '© 2026 Alex Carter. Built with HTML, CSS, JavaScript, PHP, MySQL & Bootstrap.'),
('email', 'hello@alexcarter.dev'),
('phone', '+1 555 000 1234'),
('location', 'New York, USA'),
('social_github', 'https://github.com'),
('social_linkedin', 'https://linkedin.com'),
('social_twitter', 'https://twitter.com'),
('contact_title', 'Contact Me'),
('contact_subtitle', 'Have a project in mind? Send me a message and I will get back to you soon.');
