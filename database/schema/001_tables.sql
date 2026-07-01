-- AI Analysis Platform Generic Schema
-- ========================================================

-- 1. wp_ai_analysis_types
CREATE TABLE IF NOT EXISTS wp_ai_analysis_types (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    code varchar(50) NOT NULL UNIQUE,
    name_en varchar(100) NOT NULL,
    name_hi varchar(100) DEFAULT NULL,
    description text DEFAULT NULL,
    icon varchar(100) DEFAULT NULL,
    status tinyint(1) DEFAULT 1,
    sort_order int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. wp_ai_features
CREATE TABLE IF NOT EXISTS wp_ai_features (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    feature_code varchar(100) NOT NULL,
    feature_name_en varchar(150) NOT NULL,
    feature_name_hi varchar(150) DEFAULT NULL,
    description text DEFAULT NULL,
    display_order int(11) DEFAULT 0,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY type_feature (analysis_type_id, feature_code),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. wp_ai_feature_values
CREATE TABLE IF NOT EXISTS wp_ai_feature_values (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    feature_id bigint(20) unsigned NOT NULL,
    value_code varchar(100) NOT NULL,
    value_name_en varchar(150) NOT NULL,
    value_name_hi varchar(150) DEFAULT NULL,
    description text DEFAULT NULL,
    display_order int(11) DEFAULT 0,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY feature_val (feature_id, value_code),
    FOREIGN KEY (feature_id) REFERENCES wp_ai_features(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. wp_ai_report_sections
CREATE TABLE IF NOT EXISTS wp_ai_report_sections (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    section_code varchar(100) NOT NULL,
    title_en varchar(150) NOT NULL,
    title_hi varchar(150) DEFAULT NULL,
    sort_order int(11) DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY type_section (analysis_type_id, section_code),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. wp_ai_analysis_rules
CREATE TABLE IF NOT EXISTS wp_ai_analysis_rules (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    feature_id bigint(20) unsigned NOT NULL,
    feature_value_id bigint(20) unsigned NOT NULL,
    section_id bigint(20) unsigned DEFAULT NULL,
    prediction_en text NOT NULL,
    prediction_hi text DEFAULT NULL,
    priority int(11) DEFAULT 0,
    confidence_weight decimal(5,2) DEFAULT 1.00,
    report_type varchar(50) DEFAULT 'free', -- 'free' or 'premium'
    is_active tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE,
    FOREIGN KEY (feature_id) REFERENCES wp_ai_features(id) ON DELETE CASCADE,
    FOREIGN KEY (feature_value_id) REFERENCES wp_ai_feature_values(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES wp_ai_report_sections(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. wp_ai_uploads
CREATE TABLE IF NOT EXISTS wp_ai_uploads (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    user_id bigint(20) unsigned DEFAULT NULL,
    storage_provider varchar(50) DEFAULT 'local', -- 'local', 'r2', 's3'
    storage_key varchar(255) NOT NULL,
    public_url varchar(500) NOT NULL,
    thumbnail_url varchar(500) DEFAULT NULL,
    mime varchar(50) DEFAULT NULL,
    size bigint(20) unsigned DEFAULT 0,
    width int(11) DEFAULT NULL,
    height int(11) DEFAULT NULL,
    checksum varchar(64) DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. wp_ai_readings
CREATE TABLE IF NOT EXISTS wp_ai_readings (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    user_id bigint(20) unsigned DEFAULT NULL,
    upload_id bigint(20) unsigned DEFAULT NULL,
    session_id varchar(100) NOT NULL,
    detected_features longtext DEFAULT NULL,
    free_report longtext DEFAULT NULL,
    premium_report longtext DEFAULT NULL,
    language varchar(10) DEFAULT 'en',
    status varchar(50) DEFAULT 'pending',
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE,
    FOREIGN KEY (upload_id) REFERENCES wp_ai_uploads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. wp_ai_prompts
CREATE TABLE IF NOT EXISTS wp_ai_prompts (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    prompt_name varchar(100) NOT NULL,
    model varchar(100) DEFAULT 'gemini-1.5-flash',
    temperature decimal(3,2) DEFAULT 0.10,
    prompt_text text NOT NULL,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY type_prompt (analysis_type_id, prompt_name),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. wp_ai_payments
CREATE TABLE IF NOT EXISTS wp_ai_payments (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    reading_id bigint(20) unsigned NOT NULL,
    user_id bigint(20) unsigned DEFAULT NULL,
    gateway varchar(50) DEFAULT 'razorpay',
    order_id varchar(100) DEFAULT NULL,
    payment_id varchar(100) DEFAULT NULL,
    signature varchar(255) DEFAULT NULL,
    amount decimal(10,2) NOT NULL,
    currency varchar(10) DEFAULT 'INR',
    coupon varchar(50) DEFAULT NULL,
    tax decimal(10,2) DEFAULT 0.00,
    status varchar(50) DEFAULT 'pending',
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (reading_id) REFERENCES wp_ai_readings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. wp_ai_report_templates
CREATE TABLE IF NOT EXISTS wp_ai_report_templates (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    analysis_type_id int(11) unsigned NOT NULL,
    language varchar(10) DEFAULT 'en',
    template_name varchar(100) NOT NULL,
    header longtext DEFAULT NULL,
    footer longtext DEFAULT NULL,
    css longtext DEFAULT NULL,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (analysis_type_id) REFERENCES wp_ai_analysis_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. wp_ai_api_logs
CREATE TABLE IF NOT EXISTS wp_ai_api_logs (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    provider varchar(50) NOT NULL, -- e.g., 'gemini', 'openai'
    model varchar(100) NOT NULL,
    request_tokens int(11) DEFAULT 0,
    response_tokens int(11) DEFAULT 0,
    cost decimal(10,6) DEFAULT 0.000000,
    latency int(11) DEFAULT 0, -- in milliseconds
    status varchar(50) DEFAULT 'success',
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. wp_ai_jobs
CREATE TABLE IF NOT EXISTS wp_ai_jobs (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    job_type varchar(100) NOT NULL, -- e.g., 'process_image', 'generate_report'
    payload longtext NOT NULL,
    attempts int(11) DEFAULT 0,
    max_attempts int(11) DEFAULT 3,
    locked_at datetime DEFAULT NULL,
    locked_by varchar(100) DEFAULT NULL,
    status varchar(50) DEFAULT 'pending', -- pending, processing, failed, completed
    error_log text DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
