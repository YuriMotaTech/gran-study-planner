CREATE TABLE IF NOT EXISTS activity_events (
    id VARCHAR(64) PRIMARY KEY,
    user_id INT NOT NULL,
    entity_type VARCHAR(32) NOT NULL,
    entity_id VARCHAR(64) NOT NULL,
    event_type VARCHAR(32) NOT NULL,
    payload JSON NULL,
    occurred_at DATETIME NOT NULL,
    INDEX idx_user_occurred (user_id, occurred_at),
    INDEX idx_entity (entity_type, entity_id, occurred_at)
);
