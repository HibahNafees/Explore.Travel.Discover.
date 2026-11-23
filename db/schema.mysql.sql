-- MySQL 8 / TiDB schema. Run with: php bin/migrate.php  (or import this file manually)

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100) NOT NULL,
    last_name     VARCHAR(100) NOT NULL,
    gender        VARCHAR(10)  NULL,
    email         VARCHAR(190) NOT NULL,
    phone         VARCHAR(30)  NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(10)  NOT NULL DEFAULT 'user',
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id            CHAR(64)     NOT NULL PRIMARY KEY,
    user_id       INT UNSIGNED NULL,
    data          MEDIUMTEXT   NOT NULL,
    last_activity INT UNSIGNED NOT NULL,
    KEY sessions_last_activity_index (last_activity),
    KEY sessions_user_id_index (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token_hash CHAR(64)     NOT NULL,
    expires_at DATETIME     NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY password_resets_token_unique (token_hash),
    CONSTRAINT password_resets_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS destinations (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug          VARCHAR(80)   NOT NULL,
    name          VARCHAR(100)  NOT NULL,
    country       VARCHAR(100)  NOT NULL,
    region        VARCHAR(50)   NOT NULL,
    summary       VARCHAR(255)  NOT NULL,
    description   TEXT          NULL,
    image         VARCHAR(500)  NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    old_price     DECIMAL(10,2) NULL,
    duration_days SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    rating        DECIMAL(2,1)  NOT NULL DEFAULT 0,
    latitude      DECIMAL(9,6)  NOT NULL,
    longitude     DECIMAL(9,6)  NOT NULL,
    featured      TINYINT(1)    NOT NULL DEFAULT 0,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY destinations_slug_unique (slug),
    KEY destinations_region_index (region),
    KEY destinations_price_index (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED  NOT NULL,
    destination_id   INT UNSIGNED  NULL,
    destination_name VARCHAR(100)  NOT NULL,
    unit_price       DECIMAL(10,2) NOT NULL,
    guests           SMALLINT UNSIGNED NOT NULL,
    arrival          DATE          NOT NULL,
    departure        DATE          NOT NULL,
    total            DECIMAL(12,2) NOT NULL,
    status           VARCHAR(10)   NOT NULL DEFAULT 'pending',
    notes            TEXT          NULL,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY bookings_user_index (user_id),
    KEY bookings_status_index (status),
    CONSTRAINT bookings_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT bookings_destination_fk FOREIGN KEY (destination_id) REFERENCES destinations (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(190) NOT NULL,
    phone      VARCHAR(30)  NULL,
    subject    VARCHAR(150) NOT NULL,
    body       TEXT         NOT NULL,
    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY messages_is_read_index (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
