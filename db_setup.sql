-- vote_data テーブルを作成
CREATE TABLE IF NOT EXISTS vote_data (
    team_id VARCHAR(50) PRIMARY KEY,
    vote_count INT NOT NULL DEFAULT 0
);

-- 初期データを投入
INSERT INTO vote_data (team_id, vote_count) VALUES ('japan', 0) ON DUPLICATE KEY UPDATE vote_count = vote_count;
INSERT INTO vote_data (team_id, vote_count) VALUES ('usa', 0) ON DUPLICATE KEY UPDATE vote_count = vote_count;
INSERT INTO vote_data (team_id, vote_count) VALUES ('dr', 0) ON DUPLICATE KEY UPDATE vote_count = vote_count;
INSERT INTO vote_data (team_id, vote_count) VALUES ('mexico', 0) ON DUPLICATE KEY UPDATE vote_count = vote_count;