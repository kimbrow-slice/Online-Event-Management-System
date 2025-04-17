CREATE DATABASE IF NOT EXISTS event_portal
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_portal;

CREATE TABLE IF NOT EXISTS users (
  user_id   INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(255) NOT NULL UNIQUE,
  pass_hash VARCHAR(255) NOT NULL,
  user_type ENUM('attendee','organizer','admin') DEFAULT 'attendee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    location_name TEXT(50) NOT NULL,
    is_attending BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS logs (
    id SERIAL PRIMARY KEY,
    log_name VARCHAR(255) NOT NULL,
    log_type VARCHAR(50) NOT NULL,
   -- user_id TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

