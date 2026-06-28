SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS join_requests;
DROP TABLE IF EXISTS project_requirements;
DROP TABLE IF EXISTS user_skills;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(80) NOT NULL,
  lastname VARCHAR(80) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  profile_picture VARCHAR(255) DEFAULT NULL,
  skills TEXT DEFAULT NULL,
  interests TEXT DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  status ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admins (
  admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE skills (
  skill_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  skill_name VARCHAR(100) NOT NULL UNIQUE,
  skill_category VARCHAR(59) NOT NULL,
  description TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE projects (
  project_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  project_title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  required_skills TEXT NOT NULL,
  category VARCHAR(120) NOT NULL,
  project_status ENUM('open', 'in_progress', 'completed') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_projects_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_skills (
  user_skill_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  skill_id INT UNSIGNED NOT NULL,
  proficiency_level VARCHAR(20) NOT NULL DEFAULT 'Beginner',
  years_experience TINYINT UNSIGNED NOT NULL DEFAULT 0,
  added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_skill (user_id, skill_id),
  CONSTRAINT fk_user_skills_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_user_skills_skill
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE project_requirements (
  requirement_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  skill_id INT UNSIGNED NOT NULL,
  proficiency_required VARCHAR(100) NOT NULL DEFAULT 'Beginner',
  slots_needed INT UNSIGNED NOT NULL DEFAULT 1,
  description VARCHAR(255) DEFAULT NULL,
  UNIQUE KEY unique_project_skill (project_id, skill_id),
  CONSTRAINT fk_project_requirements_project
    FOREIGN KEY (project_id) REFERENCES projects(project_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_project_requirements_skill
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE join_requests (
  request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  skill_id INT UNSIGNED DEFAULT NULL,
  message TEXT DEFAULT NULL,
  request_status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_project_applicant (project_id, user_id),
  CONSTRAINT fk_requests_project
    FOREIGN KEY (project_id) REFERENCES projects(project_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_requests_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_requests_skill
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO users
  (firstname, lastname, username, email, password_hash, skills, interests, bio, role, status)
VALUES
  ('Admin', 'User', 'admin', 'admin@cocreate.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq.QnLqRfeS5eV.zJ2W', 'Moderation, PHP, MySQL', 'Community building', 'System administrator for CoCreate.', 'admin', 'active'),
  ('Mika', 'Santos', 'mika', 'mika@cocreate.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq.QnLqRfeS5eV.zJ2W', 'UI design, Figma, branding', 'Creative tools, web apps', 'Designer who enjoys making polished project interfaces.', 'user', 'active'),
  ('Leo', 'Reyes', 'leo', 'leo@cocreate.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq.QnLqRfeS5eV.zJ2W', 'PHP, JavaScript, MySQL', 'Open source, campus tools', 'Backend learner who likes practical collaboration apps.', 'user', 'active'),
  ('Ari', 'Cruz', 'ari', 'ari@cocreate.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq.QnLqRfeS5eV.zJ2W', 'Video editing, writing, sound design', 'Short films, music videos', 'Story-focused creator looking for production teammates.', 'user', 'active');

INSERT INTO admins (username, email, password_hash)
VALUES ('admin', 'admin@cocreate.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq.QnLqRfeS5eV.zJ2W');

INSERT INTO skills
  (skill_name, skill_category, description)
VALUES
  ('PHP', 'Programming', 'Server-side programming for web applications.'),
  ('MySQL', 'Database', 'Relational database design and querying.'),
  ('JavaScript', 'Programming', 'Interactive frontend behavior and browser logic.'),
  ('CSS', 'Design', 'Responsive layout and visual styling.'),
  ('UI design', 'Design', 'Interface design, usability, and visual systems.'),
  ('Figma', 'Design', 'Collaborative interface prototyping.'),
  ('Branding', 'Design', 'Identity systems and visual direction.'),
  ('Copywriting', 'Writing', 'Clear product and project writing.'),
  ('UX research', 'Research', 'User interviews, testing, and research synthesis.'),
  ('Video editing', 'Media', 'Editing video for story and pacing.'),
  ('Writing', 'Writing', 'Narrative and script writing.'),
  ('Sound design', 'Media', 'Audio editing and atmosphere creation.'),
  ('Art direction', 'Media', 'Visual planning and creative direction.'),
  ('Moderation', 'Community', 'Community safety and content review.');

INSERT INTO projects
  (user_id, project_title, description, required_skills, category, project_status)
VALUES
  (2, 'Portfolio Builder for Student Creatives', 'A simple web app where students can publish portfolios, project notes, and contact details in one place.', 'PHP, CSS, UI design, copywriting', 'Web App', 'open'),
  (3, 'Campus Event Finder', 'A searchable board for clubs to post workshops, rehearsals, and meetups with filtering by date and topic.', 'JavaScript, MySQL, UX research', 'Community Tool', 'in_progress'),
  (4, 'Indie Music Video Concept', 'A collaborative music video project needing people who enjoy storyboarding, editing, and production planning.', 'Video editing, writing, art direction', 'Creative Media', 'open');

INSERT INTO user_skills
  (user_id, skill_id, proficiency_level, years_experience)
VALUES
  (1, 1, 'Advanced', 5),
  (1, 2, 'Advanced', 5),
  (1, 14, 'Advanced', 4),
  (2, 5, 'Advanced', 3),
  (2, 6, 'Intermediate', 2),
  (2, 7, 'Intermediate', 2),
  (3, 1, 'Intermediate', 2),
  (3, 2, 'Intermediate', 2),
  (3, 3, 'Intermediate', 2),
  (4, 10, 'Advanced', 4),
  (4, 11, 'Intermediate', 3),
  (4, 12, 'Intermediate', 2);

INSERT INTO project_requirements
  (project_id, skill_id, proficiency_required, slots_needed, description)
VALUES
  (1, 1, 'Intermediate', 1, 'Backend work'),
  (1, 4, 'Intermediate', 1, 'Responsive styling'),
  (1, 5, 'Intermediate', 1, 'Interface polish'),
  (1, 8, 'Beginner', 1, 'Project copy'),
  (2, 3, 'Intermediate', 1, 'Search filters'),
  (2, 2, 'Intermediate', 1, 'Database schema'),
  (2, 9, 'Beginner', 1, 'User feedback'),
  (3, 10, 'Intermediate', 1, 'Edit footage'),
  (3, 11, 'Beginner', 1, 'Script ideas'),
  (3, 13, 'Intermediate', 1, 'Visual direction');

INSERT INTO join_requests
  (project_id, user_id, skill_id, message, request_status)
VALUES
  (1, 3, 1, 'I can help build the PHP and MySQL side of the portfolio builder.', 'pending'),
  (1, 4, 8, 'I can help with copywriting and creative direction.', 'accepted'),
  (3, 2, 5, 'I would like to help with layout and visual identity.', 'pending');
