CREATE DATABASE IF NOT EXISTS event_portal
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_portal;

CREATE TABLE IF NOT EXISTS users (
  user_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(191) NOT NULL UNIQUE,
  pass_hash   CHAR(60)     NOT NULL,              -- 60 for bcrypt
  user_type   ENUM('attendee','organizer','admin') DEFAULT 'attendee',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
  event_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(255) NOT NULL,
  description  TEXT,
  event_date   DATE NOT NULL,                                    -- ← changed from DATETIME to just DATE
  event_time   TIME            NOT NULL DEFAULT '00:00:00',      -- ← added missing column
  location     VARCHAR(255)    NOT NULL,                         -- ← added missing column
  status       ENUM('upcoming','open','closed','cancelled') NOT NULL DEFAULT 'upcoming',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registrations (
  registration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id        INT UNSIGNED NOT NULL,
  user_id         INT UNSIGNED NULL,
  guest_name      VARCHAR(200) NULL,
  guest_email     VARCHAR(255) NULL,
  registered_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(event_id),
  FOREIGN KEY (user_id)  REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS feedback (
  feedback_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  registration_id INT UNSIGNED NOT NULL,
  rating          TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comments        TEXT,
  submitted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (registration_id) REFERENCES registrations(registration_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
