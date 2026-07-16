-- PCH Winners Portal - Database Schema
-- Import this via phpMyAdmin if you cannot run php artisan migrate
-- Drop tables in reverse order if re-running
-- DROP TABLE IF EXISTS transactions, withdrawals, deposits, job_batches, failed_jobs, jobs, notifications, activity_logs, documents, messages, winners, password_reset_tokens, sessions, cache_locks, cache, personal_access_tokens, users, payment_methods, pages, settings, user_messages, shop_orders, shop_products, giveaway_entries, giveaways, spin_results, spin_wheel_segments, spin_and_wins, membership_subscriptions, membership_tiers, migrations;

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    is_super_admin TINYINT(1) NOT NULL DEFAULT 0,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    permissions JSON NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS winners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    state VARCHAR(255) NULL,
    zip VARCHAR(255) NULL,
    prize_amount DECIMAL(12, 2) NOT NULL,
    prize_description TEXT NULL,
    email VARCHAR(255) NULL,
    unique_code VARCHAR(255) NOT NULL UNIQUE,
    is_claimed TINYINT(1) NOT NULL DEFAULT 0,
    claimed_at TIMESTAMP NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    status VARCHAR(255) NOT NULL DEFAULT 'new',
    next_steps TEXT NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    winner_id BIGINT UNSIGNED NOT NULL,
    subject VARCHAR(255) NULL,
    content TEXT NOT NULL,
    sent_by VARCHAR(255) NOT NULL,
    sent_by_admin TINYINT(1) NOT NULL DEFAULT 1,
    `read` TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT messages_winner_id_foreign FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    winner_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(255) NOT NULL,
    custom_type VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    file_size BIGINT UNSIGNED NULL,
    mime_type VARCHAR(255) NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'requested',
    admin_notes TEXT NULL,
    submitted_at TIMESTAMP NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT documents_winner_id_foreign FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(255) NOT NULL,
    collection VARCHAR(255) NOT NULL,
    document_id VARCHAR(255) NULL,
    user_id BIGINT UNSIGNED NULL,
    changes JSON NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT activity_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payment_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    purpose VARCHAR(255) NOT NULL DEFAULT 'deposit,withdrawal',
    type VARCHAR(255) NOT NULL DEFAULT 'offline',
    description TEXT NULL,
    instructions TEXT NULL,
    config JSON NULL,
    logo VARCHAR(255) NULL,
    barcode VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS deposits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    winner_id BIGINT UNSIGNED NOT NULL,
    payment_method_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    fee DECIMAL(12, 2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    proof_file VARCHAR(255) NULL,
    proof_file_name VARCHAR(255) NULL,
    notes TEXT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT deposits_winner_id_foreign FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE,
    CONSTRAINT deposits_payment_method_id_foreign FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS withdrawals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    winner_id BIGINT UNSIGNED NOT NULL,
    payment_method_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    fee DECIMAL(12, 2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    account_details JSON NULL,
    notes TEXT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    approved_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT withdrawals_winner_id_foreign FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE,
    CONSTRAINT withdrawals_payment_method_id_foreign FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    winner_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    fee DECIMAL(12, 2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    payment_method VARCHAR(255) NULL,
    reference_type VARCHAR(255) NULL,
    reference_id BIGINT UNSIGNED NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'completed',
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT transactions_winner_id_foreign FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE,
    INDEX transactions_reference_index (reference_type, reference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs, sessions, cache tables
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX tokenable_index (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX notifiable_index (notifiable_type, notifiable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Additional app tables
CREATE TABLE IF NOT EXISTS pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NULL,
    meta_description TEXT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    value TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    admin_id BIGINT UNSIGNED NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    direction VARCHAR(255) NOT NULL DEFAULT 'user_to_admin',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT user_messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT user_messages_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shop_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    category VARCHAR(255) NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shop_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    zip VARCHAR(255) NOT NULL,
    items JSON NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(255) NULL,
    payment_details JSON NULL,
    payment_proof VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS giveaways (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    prize VARCHAR(255) NULL,
    prize_value DECIMAL(12, 2) NULL,
    image VARCHAR(255) NULL,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    max_entries INT NOT NULL DEFAULT 0,
    status VARCHAR(255) NOT NULL DEFAULT 'draft',
    color VARCHAR(255) NULL DEFAULT '#D4AF37',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS giveaway_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    giveaway_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    is_winner TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT giveaway_entries_giveaway_id_foreign FOREIGN KEY (giveaway_id) REFERENCES giveaways(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spin_and_wins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    rules TEXT NULL,
    image VARCHAR(255) NULL,
    success_message TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    max_spins_per_day INT NOT NULL DEFAULT 5,
    cooldown_minutes INT NOT NULL DEFAULT 0,
    requires_login TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spin_wheel_segments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    spin_and_win_id BIGINT UNSIGNED NOT NULL,
    label VARCHAR(255) NOT NULL,
    color VARCHAR(255) NOT NULL DEFAULT '#D4AF37',
    prize_type VARCHAR(255) NOT NULL DEFAULT 'nothing',
    prize_value DECIMAL(12, 2) NOT NULL DEFAULT 0,
    prize_description TEXT NULL,
    weight INT NOT NULL DEFAULT 1,
    is_jackpot TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT spin_wheel_segments_spin_and_win_id_foreign FOREIGN KEY (spin_and_win_id) REFERENCES spin_and_wins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spin_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    spin_and_win_id BIGINT UNSIGNED NOT NULL,
    spin_wheel_segment_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    winner_name VARCHAR(255) NOT NULL,
    winner_email VARCHAR(255) NULL,
    prize_label VARCHAR(255) NOT NULL,
    prize_type VARCHAR(255) NOT NULL,
    prize_value DECIMAL(12, 2) NOT NULL DEFAULT 0,
    is_claimed TINYINT(1) NOT NULL DEFAULT 0,
    claimed_at TIMESTAMP NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT spin_results_spin_and_win_id_foreign FOREIGN KEY (spin_and_win_id) REFERENCES spin_and_wins(id) ON DELETE CASCADE,
    CONSTRAINT spin_results_segment_id_foreign FOREIGN KEY (spin_wheel_segment_id) REFERENCES spin_wheel_segments(id) ON DELETE CASCADE,
    CONSTRAINT spin_results_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS membership_tiers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    features JSON NULL,
    badge_color VARCHAR(255) NULL DEFAULT '#D4AF37',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS membership_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    membership_tier_id BIGINT UNSIGNED NOT NULL,
    subscriber_name VARCHAR(255) NOT NULL,
    subscriber_email VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'active',
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT membership_subscriptions_tier_id_foreign FOREIGN KEY (membership_tier_id) REFERENCES membership_tiers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migrations so Laravel knows they ran
INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('0001_01_01_000003_create_notifications_table', 1),
('0001_01_01_000004_create_personal_access_tokens_table', 1),
('0001_01_01_000005_create_sessions_table', 1),
('0002_01_01_000000_create_winners_table', 1),
('0003_01_01_000000_create_messages_table', 1),
('0004_01_01_000000_create_documents_table', 1),
('0005_01_01_000000_create_activity_logs_table', 1),
('2026_07_07_113208_create_payment_methods_table', 1),
('2026_07_11_095822_add_purpose_to_payment_methods_table', 2),
('2026_07_11_095823_create_deposits_table', 2),
('2026_07_11_095823_create_withdrawals_table', 2),
('2026_07_11_095823_create_transactions_table', 2),
('2026_07_11_114204_convert_payment_method_purpose_to_csv', 3),
('2026_07_11_121838_add_features_to_winners_table', 4),
('2026_07_11_130000_add_is_super_admin_and_granular_permissions', 5),
('2026_07_11_140000_add_logo_and_backcode_to_payment_methods', 6),
('2026_07_11_150000_replace_backcode_with_barcode', 7);
