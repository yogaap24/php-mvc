CREATE TABLE IF NOT EXISTS users (
    id {{STRING}}(36) PRIMARY KEY,
    email {{STRING}}(255) UNIQUE NOT NULL,
    password {{STRING}}(255) NOT NULL,
    created_at {{TIMESTAMP}} DEFAULT {{NOW}},
    updated_at {{TIMESTAMP}} DEFAULT {{NOW}} ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id {{STRING}}(36) PRIMARY KEY,
    user_id {{STRING}}(36) NOT NULL,
    created_at {{TIMESTAMP}} DEFAULT {{NOW}},
    expires_at {{TIMESTAMP}} DEFAULT {{INTERVAL_12_HOURS}},
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_sessions_user_id ON sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_sessions_expires_at ON sessions(expires_at);
