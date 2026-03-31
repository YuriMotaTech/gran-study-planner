CREATE TABLE IF NOT EXISTS weekly_goals (
    user_id INT NOT NULL,
    iso_year_week VARCHAR(8) NOT NULL,
    goal_pending INT NOT NULL DEFAULT 0,
    goal_in_progress INT NOT NULL DEFAULT 0,
    goal_done INT NOT NULL DEFAULT 0,
    goal_overdue INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (user_id, iso_year_week),
    INDEX idx_week (iso_year_week)
);

