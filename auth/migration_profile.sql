-- Run on decission after backing it up. Check SHOW COLUMNS FROM users first.
ALTER TABLE users
  ADD COLUMN city VARCHAR(100) NULL AFTER address,
  ADD COLUMN state VARCHAR(100) NULL AFTER city,
  ADD COLUMN country VARCHAR(100) NULL AFTER state,
  ADD COLUMN profile_image VARCHAR(255) NULL AFTER country,
  ADD COLUMN profile_completed_at DATETIME NULL AFTER profile_image;
