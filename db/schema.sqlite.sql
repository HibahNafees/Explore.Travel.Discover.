-- SQLite schema for local development. Run with: php bin/migrate.php

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name    TEXT NOT NULL,
    last_name     TEXT NOT NULL,
    gender        TEXT NULL,
    email         TEXT NOT NULL COLLATE NOCASE UNIQUE,
    phone         TEXT NULL,
    password_hash TEXT NOT NULL,
    role          TEXT NOT NULL DEFAULT 'user',
    created_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id            TEXT PRIMARY KEY,
    user_id       INTEGER NULL,
    data          TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity);
CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id);

CREATE TABLE IF NOT EXISTS password_resets (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    token_hash TEXT NOT NULL UNIQUE,
    expires_at TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS destinations (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    slug          TEXT NOT NULL UNIQUE,
    name          TEXT NOT NULL,
    country       TEXT NOT NULL,
    region        TEXT NOT NULL,
    summary       TEXT NOT NULL,
    description   TEXT NULL,
    image         TEXT NOT NULL,
    price         NUMERIC NOT NULL,
    old_price     NUMERIC NULL,
    duration_days INTEGER NOT NULL DEFAULT 1,
    rating        NUMERIC NOT NULL DEFAULT 0,
    latitude      REAL NOT NULL,
    longitude     REAL NOT NULL,
    featured      INTEGER NOT NULL DEFAULT 0,
    created_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS destinations_region_index ON destinations (region);
CREATE INDEX IF NOT EXISTS destinations_price_index ON destinations (price);

CREATE TABLE IF NOT EXISTS bookings (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id          INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    destination_id   INTEGER NULL REFERENCES destinations (id) ON DELETE SET NULL,
    destination_name TEXT NOT NULL,
    unit_price       NUMERIC NOT NULL,
    guests           INTEGER NOT NULL,
    arrival          TEXT NOT NULL,
    departure        TEXT NOT NULL,
    total            NUMERIC NOT NULL,
    status           TEXT NOT NULL DEFAULT 'pending',
    notes            TEXT NULL,
    created_at       TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS bookings_user_index ON bookings (user_id);
CREATE INDEX IF NOT EXISTS bookings_status_index ON bookings (status);

CREATE TABLE IF NOT EXISTS messages (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       TEXT NOT NULL,
    email      TEXT NOT NULL,
    phone      TEXT NULL,
    subject    TEXT NOT NULL,
    body       TEXT NOT NULL,
    is_read    INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS messages_is_read_index ON messages (is_read);
