CREATE TABLE IF NOT EXISTS study_plans (
    id VARCHAR(64) PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    deadline DATETIME NOT NULL,
    status ENUM('pending','in_progress','done','overdue') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_user_status (user_id, status),
    INDEX idx_deadline (deadline)
);
