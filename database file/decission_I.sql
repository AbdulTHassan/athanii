/* =========================================================
   ZAKKATPRO DATABASE V2
   Database: decission_i

   IMPORTANT:
   - Does NOT DROP the database
   - Does NOT DROP existing tables
   - Uses CREATE TABLE IF NOT EXISTS
   - Adds missing columns to existing core tables
   - Designed for MySQL / MariaDB on XAMPP
========================================================= */


/* =========================================================
   1. DATABASE
========================================================= */

CREATE DATABASE IF NOT EXISTS decission_i
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE decission_i;


/* =========================================================
   2. CORE: ROLES
========================================================= */

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    role_key VARCHAR(50) NULL UNIQUE,
    description VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   3. CORE: PERMISSIONS
========================================================= */

CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    permission_key VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   4. CORE: ROLE PERMISSIONS
========================================================= */

CREATE TABLE IF NOT EXISTS role_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,

    UNIQUE KEY uq_role_permission (
        role_id,
        permission_id
    ),

    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id)
        REFERENCES permissions(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   5. USERS
========================================================= */

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    role_id INT UNSIGNED NOT NULL,

    full_name VARCHAR(150) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    phone VARCHAR(30) NULL,

    language ENUM('en','ha','ar')
        NOT NULL DEFAULT 'en',

    user_type ENUM(
        'individual',
        'organization',
        'institution'
    )
        NOT NULL DEFAULT 'individual',

    status ENUM(
        'active',
        'inactive',
        'suspended'
    )
        NOT NULL DEFAULT 'active',

    last_login_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_users_role (role_id),

    INDEX idx_users_status (status),

    INDEX idx_users_type (user_type)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   6. ORGANIZATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS organizations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(200) NOT NULL,

    organization_type ENUM(
        'zakat_institution',
        'ngo',
        'foundation',
        'company',
        'government',
        'other'
    )
        NOT NULL DEFAULT 'other',

    registration_number VARCHAR(100) NULL,

    email VARCHAR(150) NULL,

    phone VARCHAR(50) NULL,

    address TEXT NULL,

    city VARCHAR(100) NULL,

    state VARCHAR(100) NULL,

    country VARCHAR(100) NOT NULL DEFAULT 'Nigeria',

    logo VARCHAR(500) NULL,

    status ENUM(
        'active',
        'inactive',
        'pending',
        'suspended'
    )
        NOT NULL DEFAULT 'pending',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_organizations_type (
        organization_type
    ),

    INDEX idx_organizations_status (
        status
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   7. ORGANIZATION USERS
========================================================= */

CREATE TABLE IF NOT EXISTS organization_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    organization_id INT UNSIGNED NOT NULL,

    user_id INT UNSIGNED NOT NULL,

    organization_role ENUM(
        'owner',
        'admin',
        'reviewer',
        'finance_officer',
        'staff'
    )
        NOT NULL DEFAULT 'staff',

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_organization_user (
        organization_id,
        user_id
    ),

    CONSTRAINT fk_org_users_org
        FOREIGN KEY (organization_id)
        REFERENCES organizations(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_org_users_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   8. CURRENCIES
========================================================= */

CREATE TABLE IF NOT EXISTS currencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    code CHAR(3) NOT NULL UNIQUE,

    name VARCHAR(100) NOT NULL,

    symbol VARCHAR(10) NOT NULL,

    decimal_places TINYINT UNSIGNED NOT NULL DEFAULT 2,

    is_default BOOLEAN NOT NULL DEFAULT FALSE,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   9. METHODOLOGIES / MADHHABS
========================================================= */

CREATE TABLE IF NOT EXISTS methodologies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    code VARCHAR(50) NOT NULL UNIQUE,

    name VARCHAR(100) NOT NULL,

    description TEXT NULL,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   10. METAL PRICES
========================================================= */

CREATE TABLE IF NOT EXISTS metal_prices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    metal ENUM(
        'gold',
        'silver'
    )
        NOT NULL,

    price_per_gram DECIMAL(18,4) NOT NULL,

    currency_code CHAR(3) NOT NULL,

    source_name VARCHAR(255) NULL,

    source_reference VARCHAR(500) NULL,

    effective_from DATETIME NOT NULL,

    effective_to DATETIME NULL,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_metal_price (
        metal,
        currency_code,
        effective_from
    ),

    CONSTRAINT fk_metal_price_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   11. NISAB SETTINGS
========================================================= */

CREATE TABLE IF NOT EXISTS nisab_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    methodology_id INT UNSIGNED NULL,

    metal ENUM(
        'gold',
        'silver'
    )
        NOT NULL,

    threshold_grams DECIMAL(12,4) NOT NULL,

    currency_code CHAR(3) NOT NULL,

    valuation_price DECIMAL(18,4) NULL,

    effective_from DATETIME NOT NULL,

    effective_to DATETIME NULL,

    source_reference TEXT NULL,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_nisab_methodology
        FOREIGN KEY (methodology_id)
        REFERENCES methodologies(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_nisab_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_nisab_active (
        metal,
        currency_code,
        effective_from
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   12. ZAKAT CALCULATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS zakat_calculations (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    reference_no VARCHAR(60) NOT NULL UNIQUE,

    methodology_id INT UNSIGNED NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    calculation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    cash DECIMAL(18,2) NOT NULL DEFAULT 0,

    gold_grams DECIMAL(12,4) NOT NULL DEFAULT 0,

    silver_grams DECIMAL(12,4) NOT NULL DEFAULT 0,

    business_assets DECIMAL(18,2) NOT NULL DEFAULT 0,

    property_assets DECIMAL(18,2) NOT NULL DEFAULT 0,

    agriculture_value DECIMAL(18,2) NOT NULL DEFAULT 0,

    livestock_value DECIMAL(18,2) NOT NULL DEFAULT 0,

    investment_assets DECIMAL(18,2) NOT NULL DEFAULT 0,

    other_assets DECIMAL(18,2) NOT NULL DEFAULT 0,

    debts DECIMAL(18,2) NOT NULL DEFAULT 0,

    total_wealth DECIMAL(18,2) NOT NULL DEFAULT 0,

    nisab_basis ENUM(
        'gold',
        'silver',
        'custom'
    )
        NOT NULL DEFAULT 'gold',

    nisab_value DECIMAL(18,2) NOT NULL DEFAULT 0,

    gold_price DECIMAL(18,4) NOT NULL DEFAULT 0,

    silver_price DECIMAL(18,4) NOT NULL DEFAULT 0,

    zakat_rate DECIMAL(6,3) NOT NULL DEFAULT 2.500,

    eligible BOOLEAN NOT NULL DEFAULT FALSE,

    zakat_due DECIMAL(18,2) NOT NULL DEFAULT 0,

    status ENUM(
        'draft',
        'calculated',
        'saved',
        'cancelled'
    )
        NOT NULL DEFAULT 'calculated',

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_calculation_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_calculation_methodology
        FOREIGN KEY (methodology_id)
        REFERENCES methodologies(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_calculation_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_calculation_user (
        user_id
    ),

    INDEX idx_calculation_date (
        calculation_date
    ),

    INDEX idx_calculation_status (
        status
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   13. ZAKAT CATEGORY BREAKDOWN
========================================================= */

CREATE TABLE IF NOT EXISTS zakat_category_breakdown (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    calculation_id INT UNSIGNED NOT NULL,

    category_code VARCHAR(50) NOT NULL,

    category_name VARCHAR(150) NOT NULL,

    gross_amount DECIMAL(18,2) NOT NULL DEFAULT 0,

    deductions DECIMAL(18,2) NOT NULL DEFAULT 0,

    net_amount DECIMAL(18,2) NOT NULL DEFAULT 0,

    zakat_amount DECIMAL(18,2) NOT NULL DEFAULT 0,

    details JSON NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_breakdown_calculation
        FOREIGN KEY (calculation_id)
        REFERENCES zakat_calculations(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_breakdown_calculation (
        calculation_id
    ),

    INDEX idx_breakdown_category (
        category_code
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   14. REPORTS
========================================================= */

CREATE TABLE IF NOT EXISTS reports (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    calculation_id INT UNSIGNED NOT NULL,

    report_no VARCHAR(60) NOT NULL UNIQUE,

    title VARCHAR(255) NOT NULL,

    file_path VARCHAR(500) NULL,

    report_format ENUM(
        'html',
        'pdf'
    )
        NOT NULL DEFAULT 'html',

    status ENUM(
        'generated',
        'archived'
    )
        NOT NULL DEFAULT 'generated',

    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reports_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_reports_calculation
        FOREIGN KEY (calculation_id)
        REFERENCES zakat_calculations(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_reports_user (
        user_id
    ),

    INDEX idx_reports_calculation (
        calculation_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   15. PAYMENT PROVIDERS
========================================================= */

CREATE TABLE IF NOT EXISTS payment_providers (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    provider_name VARCHAR(100) NOT NULL,

    provider_code VARCHAR(50) NOT NULL UNIQUE,

    provider_type ENUM(
        'online',
        'bank',
        'offline'
    )
        NOT NULL,

    public_key VARCHAR(500) NULL,

    secret_key VARCHAR(500) NULL,

    webhook_secret VARCHAR(500) NULL,

    configuration JSON NULL,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   16. ZAKAT PAYMENTS
========================================================= */

CREATE TABLE IF NOT EXISTS zakat_payments (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    calculation_id INT UNSIGNED NOT NULL,

    report_id INT UNSIGNED NULL,

    payment_reference VARCHAR(80) NOT NULL UNIQUE,

    amount DECIMAL(18,2) NOT NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    payment_method ENUM(
        'online',
        'bank_transfer',
        'offline'
    )
        NOT NULL,

    payment_provider_id INT UNSIGNED NULL,

    transaction_reference VARCHAR(150) NULL,

    offline_receipt VARCHAR(500) NULL,

    status ENUM(
        'initiated',
        'pending',
        'processing',
        'under_review',
        'confirmed',
        'approved',
        'rejected',
        'failed',
        'cancelled',
        'refunded'
    )
        NOT NULL DEFAULT 'initiated',

    payment_date DATETIME NULL,

    notes TEXT NULL,

    reviewed_by INT UNSIGNED NULL,

    reviewed_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_payment_calculation
        FOREIGN KEY (calculation_id)
        REFERENCES zakat_calculations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_payment_report
        FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_payment_provider
        FOREIGN KEY (payment_provider_id)
        REFERENCES payment_providers(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_payment_reviewer
        FOREIGN KEY (reviewed_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_payment_user (
        user_id
    ),

    INDEX idx_payment_status (
        status
    ),

    INDEX idx_payment_reference (
        payment_reference
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   17. PAYMENT TRANSACTIONS
========================================================= */

CREATE TABLE IF NOT EXISTS payment_transactions (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    payment_id INT UNSIGNED NOT NULL,

    provider_id INT UNSIGNED NULL,

    transaction_reference VARCHAR(150) NULL,

    gateway_reference VARCHAR(150) NULL,

    amount DECIMAL(18,2) NOT NULL,

    currency_code CHAR(3) NOT NULL,

    status ENUM(
        'initiated',
        'pending',
        'successful',
        'failed',
        'reversed'
    )
        NOT NULL DEFAULT 'initiated',

    request_payload JSON NULL,

    response_payload JSON NULL,

    initiated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    completed_at DATETIME NULL,

    verified_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transaction_payment
        FOREIGN KEY (payment_id)
        REFERENCES zakat_payments(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_transaction_provider
        FOREIGN KEY (provider_id)
        REFERENCES payment_providers(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_transaction_payment (
        payment_id
    ),

    INDEX idx_gateway_reference (
        gateway_reference
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   18. PAYMENT REVIEWS
========================================================= */

CREATE TABLE IF NOT EXISTS payment_reviews (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    payment_id INT UNSIGNED NOT NULL,

    reviewer_id INT UNSIGNED NOT NULL,

    action ENUM(
        'submitted',
        'reviewed',
        'approved',
        'rejected',
        'requested_information'
    )
        NOT NULL,

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_review_payment
        FOREIGN KEY (payment_id)
        REFERENCES zakat_payments(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_payment_review_reviewer
        FOREIGN KEY (reviewer_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_payment_reviews_payment (
        payment_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   19. CERTIFICATES
========================================================= */

CREATE TABLE IF NOT EXISTS certificates (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    payment_id INT UNSIGNED NOT NULL,

    calculation_id INT UNSIGNED NOT NULL,

    certificate_no VARCHAR(80) NOT NULL UNIQUE,

    verification_code VARCHAR(150) NOT NULL UNIQUE,

    issued_to VARCHAR(255) NOT NULL,

    category_summary VARCHAR(255) NULL,

    amount DECIMAL(18,2) NOT NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    payment_date DATETIME NULL,

    status ENUM(
        'pending',
        'valid',
        'revoked',
        'expired'
    )
        NOT NULL DEFAULT 'pending',

    issued_by INT UNSIGNED NULL,

    issued_at DATETIME NULL,

    revoked BOOLEAN NOT NULL DEFAULT FALSE,

    revoked_reason TEXT NULL,

    qr_payload TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_certificate_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_certificate_payment
        FOREIGN KEY (payment_id)
        REFERENCES zakat_payments(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_certificate_calculation
        FOREIGN KEY (calculation_id)
        REFERENCES zakat_calculations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_certificate_issuer
        FOREIGN KEY (issued_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_certificate_status (
        status
    ),

    INDEX idx_certificate_verification (
        verification_code
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   20. CERTIFICATE VERIFICATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS certificate_verifications (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    certificate_id INT UNSIGNED NOT NULL,

    verification_code VARCHAR(150) NOT NULL,

    ip_address VARCHAR(45) NULL,

    user_agent TEXT NULL,

    verification_result ENUM(
        'valid',
        'invalid',
        'revoked',
        'expired'
    )
        NOT NULL,

    verified_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_certificate_verification
        FOREIGN KEY (certificate_id)
        REFERENCES certificates(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_certificate_verification_code (
        verification_code
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   21. KNOWLEDGE CATEGORIES
========================================================= */

CREATE TABLE IF NOT EXISTS knowledge_categories (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    slug VARCHAR(180) NULL UNIQUE,

    description TEXT NULL,

    icon VARCHAR(100) NULL,

    sort_order INT UNSIGNED NOT NULL DEFAULT 0,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_knowledge_category_status (
        status
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   22. KNOWLEDGE ARTICLES
========================================================= */

CREATE TABLE IF NOT EXISTS knowledge_articles (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_id INT UNSIGNED NOT NULL,

    language ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL DEFAULT 'en',

    title VARCHAR(255) NOT NULL,

    slug VARCHAR(255) NULL UNIQUE,

    excerpt TEXT NULL,

    content LONGTEXT NOT NULL,

    featured_image VARCHAR(500) NULL,

    reading_time_minutes TINYINT UNSIGNED NOT NULL DEFAULT 5,

    status ENUM(
        'draft',
        'review',
        'published',
        'archived'
    )
        NOT NULL DEFAULT 'draft',

    is_featured BOOLEAN NOT NULL DEFAULT FALSE,

    featured_order INT UNSIGNED NOT NULL DEFAULT 0,

    view_count BIGINT UNSIGNED NOT NULL DEFAULT 0,

    created_by INT UNSIGNED NULL,

    reviewed_by INT UNSIGNED NULL,

    published_at DATETIME NULL,

    seo_title VARCHAR(255) NULL,

    seo_description VARCHAR(320) NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_article_category
        FOREIGN KEY (category_id)
        REFERENCES knowledge_categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_article_creator
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_article_reviewer
        FOREIGN KEY (reviewed_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_article_category (
        category_id
    ),

    INDEX idx_article_status (
        status
    ),

    INDEX idx_article_featured (
        is_featured,
        featured_order
    ),

    FULLTEXT INDEX ft_knowledge_article_search (
        title,
        excerpt,
        content
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   23. KNOWLEDGE TRANSLATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS knowledge_article_translations (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    article_id INT UNSIGNED NOT NULL,

    language_code ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL,

    title VARCHAR(255) NOT NULL,

    slug VARCHAR(255) NOT NULL,

    excerpt TEXT NULL,

    content LONGTEXT NOT NULL,

    seo_title VARCHAR(255) NULL,

    seo_description VARCHAR(320) NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_article_translation (
        article_id,
        language_code
    ),

    UNIQUE KEY uq_article_translation_slug (
        slug
    ),

    CONSTRAINT fk_article_translation
        FOREIGN KEY (article_id)
        REFERENCES knowledge_articles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   24. KNOWLEDGE SOURCES
========================================================= */

CREATE TABLE IF NOT EXISTS knowledge_article_sources (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    article_id INT UNSIGNED NOT NULL,

    source_type ENUM(
        'quran',
        'hadith',
        'fiqh_book',
        'scholar',
        'institution',
        'website',
        'other'
    )
        NOT NULL DEFAULT 'other',

    title VARCHAR(255) NOT NULL,

    author VARCHAR(255) NULL,

    publisher VARCHAR(255) NULL,

    publication_year YEAR NULL,

    reference_text VARCHAR(500) NULL,

    source_url VARCHAR(1000) NULL,

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_article_source
        FOREIGN KEY (article_id)
        REFERENCES knowledge_articles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_article_sources (
        article_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   25. FATWA CATEGORIES
========================================================= */

CREATE TABLE IF NOT EXISTS fatwa_categories (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    slug VARCHAR(180) NOT NULL UNIQUE,

    description TEXT NULL,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   26. FATWAS
========================================================= */

CREATE TABLE IF NOT EXISTS fatwas (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_id INT UNSIGNED NULL,

    language ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL DEFAULT 'en',

    title VARCHAR(255) NULL,

    slug VARCHAR(255) NULL UNIQUE,

    question VARCHAR(500) NOT NULL,

    answer LONGTEXT NOT NULL,

    source VARCHAR(500) NULL,

    scholar VARCHAR(255) NULL,

    madhhab VARCHAR(100) NULL,

    excerpt TEXT NULL,

    status ENUM(
        'draft',
        'pending_review',
        'under_review',
        'approved',
        'published',
        'rejected',
        'archived'
    )
        NOT NULL DEFAULT 'draft',

    is_featured BOOLEAN NOT NULL DEFAULT FALSE,

    created_by INT UNSIGNED NULL,

    reviewed_by INT UNSIGNED NULL,

    published_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_fatwa_category
        FOREIGN KEY (category_id)
        REFERENCES fatwa_categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_fatwa_creator
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_fatwa_reviewer
        FOREIGN KEY (reviewed_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_fatwa_category (
        category_id
    ),

    INDEX idx_fatwa_status (
        status
    ),

    FULLTEXT INDEX ft_fatwa_search (
        question,
        answer
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   27. FATWA REVIEWS
========================================================= */

CREATE TABLE IF NOT EXISTS fatwa_reviews (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    fatwa_id INT UNSIGNED NOT NULL,

    reviewer_id INT UNSIGNED NOT NULL,

    action ENUM(
        'submitted',
        'reviewed',
        'approved',
        'rejected',
        'revision_requested'
    )
        NOT NULL,

    comments TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fatwa_review_fatwa
        FOREIGN KEY (fatwa_id)
        REFERENCES fatwas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_fatwa_review_user
        FOREIGN KEY (reviewer_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_fatwa_reviews (
        fatwa_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   28. FAQ
========================================================= */

CREATE TABLE IF NOT EXISTS faq (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    language ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL DEFAULT 'en',

    question VARCHAR(500) NOT NULL,

    answer LONGTEXT NOT NULL,

    category VARCHAR(150) NULL,

    sort_order INT UNSIGNED NOT NULL DEFAULT 0,

    status ENUM(
        'active',
        'inactive'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   29. USER QUESTIONS
========================================================= */

CREATE TABLE IF NOT EXISTS user_questions (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NULL,

    category_id INT UNSIGNED NULL,

    language ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL DEFAULT 'en',

    question LONGTEXT NOT NULL,

    reply LONGTEXT NULL,

    workflow_status ENUM(
        'pending',
        'under_review',
        'answered',
        'published',
        'rejected'
    )
        NOT NULL DEFAULT 'pending',

    answered_by INT UNSIGNED NULL,

    answered_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_question_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_user_question_category
        FOREIGN KEY (category_id)
        REFERENCES fatwa_categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_user_question_answerer
        FOREIGN KEY (answered_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_user_questions_status (
        workflow_status
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   30. MOTIVATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS motivations (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    language ENUM(
        'en',
        'ha',
        'ar'
    )
        NOT NULL DEFAULT 'en',

    type ENUM(
        'Quran',
        'Hadith'
    )
        NOT NULL,

    title VARCHAR(255) NOT NULL,

    content LONGTEXT NOT NULL,

    reference_text VARCHAR(500) NULL,

    status ENUM(
        'draft',
        'published',
        'archived'
    )
        NOT NULL DEFAULT 'draft',

    sort_order INT UNSIGNED NOT NULL DEFAULT 0,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   31. NOTIFICATIONS
========================================================= */

CREATE TABLE IF NOT EXISTS notifications (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    type VARCHAR(50) NULL,

    title VARCHAR(255) NOT NULL,

    message TEXT NOT NULL,

    link VARCHAR(500) NULL,

    is_read BOOLEAN NOT NULL DEFAULT FALSE,

    read_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notification_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_notifications_user (
        user_id,
        is_read
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   32. USER SESSIONS
========================================================= */

CREATE TABLE IF NOT EXISTS user_sessions (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    session_token VARCHAR(255) NOT NULL UNIQUE,

    ip_address VARCHAR(45) NULL,

    user_agent TEXT NULL,

    expires_at DATETIME NOT NULL,

    last_activity_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_session_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_session_user (
        user_id
    ),

    INDEX idx_session_expiry (
        expires_at
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   33. ACTIVITY LOGS
========================================================= */

CREATE TABLE IF NOT EXISTS activity_logs (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NULL,

    action VARCHAR(100) NULL,

    activity VARCHAR(255) NOT NULL,

    entity_type VARCHAR(100) NULL,

    entity_id INT NULL,

    ip_address VARCHAR(45) NULL,

    user_agent TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_activity_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_activity_user (
        user_id
    ),

    INDEX idx_activity_created (
        created_at
    ),

    INDEX idx_activity_entity (
        entity_type,
        entity_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   34. AUDIT LOGS
========================================================= */

CREATE TABLE IF NOT EXISTS audit_logs (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NULL,

    action VARCHAR(100) NOT NULL,

    entity_type VARCHAR(100) NOT NULL,

    entity_id INT NULL,

    old_values JSON NULL,

    new_values JSON NULL,

    ip_address VARCHAR(45) NULL,

    user_agent TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_audit_entity (
        entity_type,
        entity_id
    ),

    INDEX idx_audit_user (
        user_id
    ),

    INDEX idx_audit_date (
        created_at
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   35. PASSWORD RESETS
========================================================= */

CREATE TABLE IF NOT EXISTS password_resets (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    token VARCHAR(255) NOT NULL UNIQUE,

    expires_at DATETIME NOT NULL,

    used_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_password_reset_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_password_reset_expiry (
        expires_at
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   36. BENEFICIARIES
========================================================= */

CREATE TABLE IF NOT EXISTS beneficiaries (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    organization_id INT UNSIGNED NULL,

    beneficiary_code VARCHAR(80) NOT NULL UNIQUE,

    full_name VARCHAR(200) NOT NULL,

    phone VARCHAR(50) NULL,

    address TEXT NULL,

    city VARCHAR(100) NULL,

    state VARCHAR(100) NULL,

    country VARCHAR(100) NOT NULL DEFAULT 'Nigeria',

    beneficiary_category VARCHAR(150) NULL,

    verification_status ENUM(
        'pending',
        'verified',
        'rejected'
    )
        NOT NULL DEFAULT 'pending',

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_beneficiary_org
        FOREIGN KEY (organization_id)
        REFERENCES organizations(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_beneficiary_status (
        verification_status
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   37. ZAKAT FUNDS
========================================================= */

CREATE TABLE IF NOT EXISTS zakat_funds (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    organization_id INT UNSIGNED NULL,

    fund_name VARCHAR(200) NOT NULL,

    description TEXT NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    target_amount DECIMAL(18,2) NOT NULL DEFAULT 0,

    current_amount DECIMAL(18,2) NOT NULL DEFAULT 0,

    status ENUM(
        'active',
        'inactive',
        'closed'
    )
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_fund_organization
        FOREIGN KEY (organization_id)
        REFERENCES organizations(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_fund_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   38. DISTRIBUTIONS
========================================================= */

CREATE TABLE IF NOT EXISTS distributions (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    fund_id INT UNSIGNED NOT NULL,

    distribution_reference VARCHAR(80) NOT NULL UNIQUE,

    total_amount DECIMAL(18,2) NOT NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    distribution_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    approved_by INT UNSIGNED NULL,

    status ENUM(
        'draft',
        'pending',
        'approved',
        'completed',
        'cancelled'
    )
        NOT NULL DEFAULT 'draft',

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_distribution_fund
        FOREIGN KEY (fund_id)
        REFERENCES zakat_funds(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_distribution_approver
        FOREIGN KEY (approved_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_distribution_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   39. DISTRIBUTION ITEMS
========================================================= */

CREATE TABLE IF NOT EXISTS distribution_items (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    distribution_id INT UNSIGNED NOT NULL,

    beneficiary_id INT UNSIGNED NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    currency_code CHAR(3) NOT NULL DEFAULT 'NGN',

    notes TEXT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_distribution_item_distribution
        FOREIGN KEY (distribution_id)
        REFERENCES distributions(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_distribution_item_beneficiary
        FOREIGN KEY (beneficiary_id)
        REFERENCES beneficiaries(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_distribution_item_currency
        FOREIGN KEY (currency_code)
        REFERENCES currencies(code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   40. SYSTEM SETTINGS
========================================================= */

CREATE TABLE IF NOT EXISTS system_settings (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    setting_key VARCHAR(100) NOT NULL UNIQUE,

    setting_value TEXT NULL,

    setting_type ENUM(
        'string',
        'number',
        'boolean',
        'json'
    )
        NOT NULL DEFAULT 'string',

    description VARCHAR(255) NULL,

    updated_by INT UNSIGNED NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_setting_user
        FOREIGN KEY (updated_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   41. CONDITIONAL MIGRATION FOR EXISTING CORE TABLES
   Adds missing columns without deleting existing data.
========================================================= */

DELIMITER $$


/* ---------- roles ---------- */

CREATE PROCEDURE add_column_if_missing(
    IN p_table VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_definition TEXT
)
BEGIN

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
        AND table_name = p_table
        AND column_name = p_column
    ) THEN

        SET @sql_text = CONCAT(
            'ALTER TABLE `',
            p_table,
            '` ADD COLUMN `',
            p_column,
            '` ',
            p_definition
        );

        PREPARE stmt FROM @sql_text;

        EXECUTE stmt;

        DEALLOCATE PREPARE stmt;

    END IF;

END$$

DELIMITER ;


/* Existing users */

CALL add_column_if_missing(
    'users',
    'user_type',
    "VARCHAR(30) NOT NULL DEFAULT 'individual'"
);

CALL add_column_if_missing(
    'users',
    'last_login_at',
    'DATETIME NULL'
);


/* Existing roles */

CALL add_column_if_missing(
    'roles',
    'role_key',
    'VARCHAR(50) NULL'
);

CALL add_column_if_missing(
    'roles',
    'description',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'roles',
    'status',
    "VARCHAR(20) NOT NULL DEFAULT 'active'"
);


/* Existing knowledge categories */

CALL add_column_if_missing(
    'knowledge_categories',
    'slug',
    'VARCHAR(180) NULL'
);

CALL add_column_if_missing(
    'knowledge_categories',
    'description',
    'TEXT NULL'
);

CALL add_column_if_missing(
    'knowledge_categories',
    'icon',
    'VARCHAR(100) NULL'
);

CALL add_column_if_missing(
    'knowledge_categories',
    'sort_order',
    'INT UNSIGNED NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'knowledge_categories',
    'status',
    "VARCHAR(20) NOT NULL DEFAULT 'active'"
);


/* Existing knowledge articles */

CALL add_column_if_missing(
    'knowledge_articles',
    'slug',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'excerpt',
    'TEXT NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'featured_image',
    'VARCHAR(500) NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'reading_time_minutes',
    'TINYINT UNSIGNED NOT NULL DEFAULT 5'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'status',
    "VARCHAR(30) NOT NULL DEFAULT 'draft'"
);

CALL add_column_if_missing(
    'knowledge_articles',
    'is_featured',
    'TINYINT(1) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'featured_order',
    'INT UNSIGNED NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'view_count',
    'BIGINT UNSIGNED NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'created_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'reviewed_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'published_at',
    'DATETIME NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'seo_title',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'knowledge_articles',
    'seo_description',
    'VARCHAR(320) NULL'
);


/* Existing zakat calculations */

CALL add_column_if_missing(
    'zakat_calculations',
    'methodology_id',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'currency_code',
    "CHAR(3) NOT NULL DEFAULT 'NGN'"
);

CALL add_column_if_missing(
    'zakat_calculations',
    'calculation_date',
    'DATETIME NULL'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'property_assets',
    'DECIMAL(18,2) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'agriculture_value',
    'DECIMAL(18,2) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'livestock_value',
    'DECIMAL(18,2) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'investment_assets',
    'DECIMAL(18,2) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'other_assets',
    'DECIMAL(18,2) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'nisab_basis',
    "VARCHAR(20) NOT NULL DEFAULT 'gold'"
);

CALL add_column_if_missing(
    'zakat_calculations',
    'gold_price',
    'DECIMAL(18,4) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'silver_price',
    'DECIMAL(18,4) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'zakat_rate',
    'DECIMAL(6,3) NOT NULL DEFAULT 2.500'
);

CALL add_column_if_missing(
    'zakat_calculations',
    'status_v2',
    "VARCHAR(30) NOT NULL DEFAULT 'calculated'"
);

CALL add_column_if_missing(
    'zakat_calculations',
    'notes',
    'TEXT NULL'
);


/* Existing system settings */

CALL add_column_if_missing(
    'system_settings',
    'setting_key',
    'VARCHAR(100) NULL'
);

CALL add_column_if_missing(
    'system_settings',
    'setting_value',
    'TEXT NULL'
);

CALL add_column_if_missing(
    'system_settings',
    'setting_type',
    "VARCHAR(20) NOT NULL DEFAULT 'string'"
);


/* Existing fatwas */

CALL add_column_if_missing(
    'fatwas',
    'category_id',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'title',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'slug',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'madhhab',
    'VARCHAR(100) NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'status_v2',
    "VARCHAR(30) NOT NULL DEFAULT 'draft'"
);

CALL add_column_if_missing(
    'fatwas',
    'is_featured',
    'TINYINT(1) NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'fatwas',
    'created_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'reviewed_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'fatwas',
    'published_at',
    'DATETIME NULL'
);


/* Existing FAQ */

CALL add_column_if_missing(
    'faq',
    'category',
    'VARCHAR(150) NULL'
);

CALL add_column_if_missing(
    'faq',
    'sort_order',
    'INT UNSIGNED NOT NULL DEFAULT 0'
);

CALL add_column_if_missing(
    'faq',
    'status',
    "VARCHAR(20) NOT NULL DEFAULT 'active'"
);


/* Existing user questions */

CALL add_column_if_missing(
    'user_questions',
    'language',
    "VARCHAR(5) NOT NULL DEFAULT 'en'"
);

CALL add_column_if_missing(
    'user_questions',
    'workflow_status',
    "VARCHAR(30) NOT NULL DEFAULT 'pending'"
);

CALL add_column_if_missing(
    'user_questions',
    'answered_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'user_questions',
    'answered_at',
    'DATETIME NULL'
);


/* Existing motivations */

CALL add_column_if_missing(
    'motivations',
    'status',
    "VARCHAR(20) NOT NULL DEFAULT 'draft'"
);

CALL add_column_if_missing(
    'motivations',
    'sort_order',
    'INT UNSIGNED NOT NULL DEFAULT 0'
);


/* Existing notifications */

CALL add_column_if_missing(
    'notifications',
    'type',
    'VARCHAR(50) NULL'
);

CALL add_column_if_missing(
    'notifications',
    'link',
    'VARCHAR(500) NULL'
);

CALL add_column_if_missing(
    'notifications',
    'read_at',
    'DATETIME NULL'
);


/* Existing activity logs */

CALL add_column_if_missing(
    'activity_logs',
    'action',
    'VARCHAR(100) NULL'
);

CALL add_column_if_missing(
    'activity_logs',
    'entity_type',
    'VARCHAR(100) NULL'
);

CALL add_column_if_missing(
    'activity_logs',
    'entity_id',
    'INT NULL'
);

CALL add_column_if_missing(
    'activity_logs',
    'ip_address',
    'VARCHAR(45) NULL'
);

CALL add_column_if_missing(
    'activity_logs',
    'user_agent',
    'TEXT NULL'
);


/* Existing zakat payments */

CALL add_column_if_missing(
    'zakat_payments',
    'payment_reference',
    'VARCHAR(80) NULL'
);

CALL add_column_if_missing(
    'zakat_payments',
    'currency_code',
    "CHAR(3) NOT NULL DEFAULT 'NGN'"
);

CALL add_column_if_missing(
    'zakat_payments',
    'payment_provider_id',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'zakat_payments',
    'transaction_reference',
    'VARCHAR(150) NULL'
);

CALL add_column_if_missing(
    'zakat_payments',
    'offline_receipt',
    'VARCHAR(500) NULL'
);

CALL add_column_if_missing(
    'zakat_payments',
    'reviewed_by',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'zakat_payments',
    'reviewed_at',
    'DATETIME NULL'
);


/* Existing certificates */

CALL add_column_if_missing(
    'certificates',
    'payment_id',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'certificates',
    'calculation_id',
    'INT UNSIGNED NULL'
);

CALL add_column_if_missing(
    'certificates',
    'issued_to',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'certificates',
    'category_summary',
    'VARCHAR(255) NULL'
);

CALL add_column_if_missing(
    'certificates',
    'amount',
    'DECIMAL(18,2) NULL'
);

CALL add_column_if_missing(
    'certificates',
    'currency_code',
    "CHAR(3) NOT NULL DEFAULT 'NGN'"
);

CALL add_column_if_missing(
    'certificates',
    'payment_date',
    'DATETIME NULL'
);

CALL add_column_if_missing(
    'certificates',
    'status_v2',
    "VARCHAR(30) NOT NULL DEFAULT 'pending'"
);

CALL add_column_if_missing(
    'certificates',
    'qr_payload',
    'TEXT NULL'
);


/* Remove migration helper */

DROP PROCEDURE IF EXISTS add_column_if_missing;


/* =========================================================
   42. SEED: ROLES
========================================================= */

INSERT IGNORE INTO roles
(
    name,
    role_key,
    description
)
VALUES
(
    'Admin',
    'admin',
    'System administrator'
),
(
    'User',
    'user',
    'Normal Zakat platform user'
),
(
    'Reviewer',
    'reviewer',
    'Reviews platform content and records'
),
(
    'Scholar',
    'scholar',
    'Qualified reviewer for Islamic knowledge and Fatwa content'
),
(
    'Finance Officer',
    'finance_officer',
    'Handles payment and financial review'
),
(
    'Organization Admin',
    'organization_admin',
    'Manages an institutional organization account'
),
(
    'Super Admin',
    'super_admin',
    'Highest level system administration'
);


/* =========================================================
   43. SEED: PERMISSIONS
========================================================= */

INSERT IGNORE INTO permissions
(
    name,
    permission_key,
    description
)
VALUES

(
    'Manage Users',
    'users.manage',
    'Create, update and manage users'
),

(
    'View Users',
    'users.view',
    'View user records'
),

(
    'Manage Knowledge',
    'knowledge.manage',
    'Create, update and manage knowledge articles'
),

(
    'Publish Knowledge',
    'knowledge.publish',
    'Publish reviewed knowledge articles'
),

(
    'Manage Fatwa',
    'fatwa.manage',
    'Manage Fatwa records'
),

(
    'Review Fatwa',
    'fatwa.review',
    'Review Fatwa content'
),

(
    'Manage Calculations',
    'calculations.manage',
    'View and manage calculations'
),

(
    'Manage Payments',
    'payments.manage',
    'Manage Zakat payments'
),

(
    'Review Payments',
    'payments.review',
    'Review and approve payments'
),

(
    'Manage Certificates',
    'certificates.manage',
    'Generate and manage certificates'
),

(
    'View Reports',
    'reports.view',
    'View system reports'
),

(
    'Manage Organizations',
    'organizations.manage',
    'Manage institutional organizations'
),

(
    'Manage Beneficiaries',
    'beneficiaries.manage',
    'Manage beneficiaries'
),

(
    'Manage Distribution',
    'distribution.manage',
    'Manage Zakat distribution'
),

(
    'View Audit Logs',
    'audit.view',
    'View system audit records'
);


/* =========================================================
   44. SEED: CURRENCIES
========================================================= */

INSERT IGNORE INTO currencies
(
    code,
    name,
    symbol,
    decimal_places,
    is_default
)
VALUES
(
    'NGN',
    'Nigerian Naira',
    '₦',
    2,
    TRUE
),
(
    'USD',
    'United States Dollar',
    '$',
    2,
    FALSE
),
(
    'GBP',
    'British Pound',
    '£',
    2,
    FALSE
),
(
    'EUR',
    'Euro',
    '€',
    2,
    FALSE
),
(
    'SAR',
    'Saudi Riyal',
    '﷼',
    2,
    FALSE
),
(
    'AED',
    'United Arab Emirates Dirham',
    'د.إ',
    2,
    FALSE
);


/* =========================================================
   45. SEED: METHODOLOGIES
========================================================= */

INSERT IGNORE INTO methodologies
(
    code,
    name,
    description
)
VALUES

(
    'maliki',
    'Mālikī',
    'Mālikī methodology'
),

(
    'hanafi',
    'Ḥanafī',
    'Ḥanafī methodology'
),

(
    'shafii',
    'Shāfiʿī',
    'Shāfiʿī methodology'
),

(
    'hanbali',
    'Ḥanbalī',
    'Ḥanbalī methodology'
);


/* =========================================================
   46. SEED: KNOWLEDGE CATEGORIES
========================================================= */

INSERT IGNORE INTO knowledge_categories
(
    name,
    slug,
    description,
    icon,
    sort_order
)
VALUES

(
    'Zakat Basics',
    'zakat-basics',
    'Fundamental concepts, conditions and principles of Zakat.',
    'book-open',
    1
),

(
    'Zakat on Wealth',
    'zakat-on-wealth',
    'General guidance concerning qualifying wealth.',
    'wallet',
    2
),

(
    'Cash & Savings',
    'cash-and-savings',
    'Educational resources about cash and savings.',
    'banknote',
    3
),

(
    'Gold & Silver',
    'gold-and-silver',
    'Resources concerning gold, silver and Nisab.',
    'coins',
    4
),

(
    'Business Zakat',
    'business-zakat',
    'Educational resources about business and commercial wealth.',
    'briefcase',
    5
),

(
    'Agriculture',
    'agriculture',
    'Resources concerning agricultural produce.',
    'wheat',
    6
),

(
    'Livestock',
    'livestock',
    'Resources concerning livestock Zakat.',
    'paw-print',
    7
),

(
    'Fiqh & Methodology',
    'fiqh-methodology',
    'Scholarly methodology and source-based Zakat guidance.',
    'scale',
    8
);


/* =========================================================
   47. SEED: FATWA CATEGORIES
========================================================= */

INSERT IGNORE INTO fatwa_categories
(
    name,
    slug,
    description
)
VALUES

(
    'General Zakat',
    'general-zakat',
    'General questions concerning Zakat'
),

(
    'Gold & Jewellery',
    'gold-jewellery',
    'Questions concerning gold and jewellery'
),

(
    'Cash & Savings',
    'cash-savings',
    'Questions concerning cash and savings'
),

(
    'Business',
    'business',
    'Questions concerning business Zakat'
),

(
    'Agriculture',
    'agriculture',
    'Questions concerning agricultural Zakat'
),

(
    'Livestock',
    'livestock',
    'Questions concerning livestock'
),

(
    'Property',
    'property',
    'Questions concerning property'
),

(
    'Madhhab & Methodology',
    'madhhab-methodology',
    'Questions concerning scholarly methodology'
);


/* =========================================================
   48. SEED: PAYMENT PROVIDERS
========================================================= */

INSERT IGNORE INTO payment_providers
(
    provider_name,
    provider_code,
    provider_type,
    status
)
VALUES

(
    'Bank Transfer',
    'bank_transfer',
    'bank',
    'active'
),

(
    'Offline Payment',
    'offline',
    'offline',
    'active'
),

(
    'Paystack',
    'paystack',
    'online',
    'inactive'
),

(
    'Flutterwave',
    'flutterwave',
    'online',
    'inactive'
);


/* =========================================================
   49. SYSTEM DEFAULT SETTINGS
========================================================= */

INSERT IGNORE INTO system_settings
(
    setting_key,
    setting_value,
    setting_type,
    description
)
VALUES

(
    'system_name',
    'ZAKKATPRO',
    'string',
    'Application name'
),

(
    'default_language',
    'en',
    'string',
    'Default platform language'
),

(
    'default_currency',
    'NGN',
    'string',
    'Default platform currency'
),

(
    'zakat_rate',
    '2.5',
    'number',
    'Default rate used by applicable Zakat calculations'
),

(
    'default_methodology',
    'maliki',
    'string',
    'Default methodology'
);


/* =========================================================
   50. INITIAL NISAB RECORDS
   These are structural defaults.
   Actual current valuation should be maintained through
   metal_prices / nisab_settings after approved sourcing.
========================================================= */

INSERT INTO nisab_settings
(
    methodology_id,
    metal,
    threshold_grams,
    currency_code,
    effective_from,
    source_reference
)

SELECT
    m.id,
    'gold',
    85.0000,
    'NGN',
    NOW(),
    'Default system structure; valuation requires approved current source.'
FROM methodologies m
WHERE m.code = 'maliki'
AND NOT EXISTS (
    SELECT 1
    FROM nisab_settings n
    WHERE n.methodology_id = m.id
    AND n.metal = 'gold'
    AND n.currency_code = 'NGN'
);


/* =========================================================
   51. FINAL
========================================================= */

SET FOREIGN_KEY_CHECKS = 1;


/* =========================================================
   52. VERIFY DATABASE
========================================================= */

SELECT
    'ZAKKATPRO DATABASE READY' AS message,
    DATABASE() AS database_name;


/* Show all tables */

SHOW TABLES;