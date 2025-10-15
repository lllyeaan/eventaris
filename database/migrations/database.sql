-- Database schema for Eventory (Event App MVC)

CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(150) NOT NULL,
    event_date DATE NULL,
    participant_quota INT UNSIGNED NOT NULL DEFAULT 0,
    committee_quota INT UNSIGNED NOT NULL DEFAULT 0,
    registration_start DATETIME NULL,
    registration_end DATETIME NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS participant_statuses (
    code VARCHAR(20) PRIMARY KEY,
    label VARCHAR(50) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS committee_statuses (
    code VARCHAR(20) PRIMARY KEY,
    label VARCHAR(50) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS participant_regs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    notes TEXT NULL,
    status_code VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_participant_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_participant_status FOREIGN KEY (status_code) REFERENCES participant_statuses(code)
);

CREATE TABLE IF NOT EXISTS committee_apps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    primary_division VARCHAR(150) NOT NULL,
    secondary_division VARCHAR(150) NULL,
    motivation TEXT NOT NULL,
    status_code VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_committee_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_committee_status FOREIGN KEY (status_code) REFERENCES committee_statuses(code)
);

CREATE INDEX idx_participant_event ON participant_regs (event_id);
CREATE INDEX idx_participant_status ON participant_regs (status_code);
CREATE INDEX idx_participant_email ON participant_regs (email);

CREATE INDEX idx_committee_event ON committee_apps (event_id);
CREATE INDEX idx_committee_status ON committee_apps (status_code);
CREATE INDEX idx_committee_email ON committee_apps (email);

INSERT INTO participant_statuses (code, label, sort_order) VALUES
    ('pending', 'Pending', 1),
    ('approved', 'Approved', 2),
    ('rejected', 'Rejected', 3),
    ('attended', 'Attended', 4)
ON DUPLICATE KEY UPDATE label = VALUES(label), sort_order = VALUES(sort_order);

INSERT INTO committee_statuses (code, label, sort_order) VALUES
    ('pending', 'Pending', 1),
    ('approved', 'Approved', 2),
    ('rejected', 'Rejected', 3)
ON DUPLICATE KEY UPDATE label = VALUES(label), sort_order = VALUES(sort_order);
