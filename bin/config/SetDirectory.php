<?php

/**
 * ╔════════════════════════════════════════════════════════════════════════════╗
 * ║                            EPAPHRODITES FRAMEWORK                          ║
 * ║                           → Global Constants File ←                        ║
 * ╠════════════════════════════════════════════════════════════════════════════╣
 * ║ This file defines all core constants used throughout the Epaphrodites      ║
 * ║ framework. These constants govern system behavior, environment paths,      ║
 * ║ session/auth protocols, database setup, and third-party integrations.      ║
 * ║                                                                            ║
 * ║ ▸ Update these values cautiously, especially in production environments.   ║
 * ║ ▸ Designed for cross-language support (PHP / Python / C modules).          ║
 * ║                                                                            ║
 * ║ Structure:                                                                 ║
 * ║   - Language & Runtime Options                                             ║
 * ║   - Directory Structure                                                    ║
 * ║   - Session & Authentication Settings                                      ║
 * ║   - Database Driver Definitions                                            ║
 * ║   - External Services & Logging                                            ║
 * ╚════════════════════════════════════════════════════════════════════════════╝
 */


/*----------------------------------------------------------
 | SYSTEM LANGUAGE & RUNTIME OPTIONS
 *----------------------------------------------------------*/

# System language (accepted: 'eng', 'fr', etc.)
define('_LANG_', 'fr');

# Python (accepted: 'python', 'python3')
# For linux os use : python3
define('_PYTHON_', 'python');

# Run python server (accepted 'true' or 'false')
define('_RUN_PYTHON_SERVER_', false);

# Define python server default port
define('_PYTHON_SERVER_PORT_', 5001);

# Language used for background processing (accepted: 'php' or 'python')
define('__EMAIL_METHOD__', 'php');

# Activate OTP-based session validation (accepted: true or false)
define('_OTP_METHOD_', false);

# Enable production mode (true = suppress debug info, false = dev mode)
define('_PRODUCTION_', false);

# Enable environment variables modification 
# (true = allow modification after load, false = prevent modification after load)
define('_ENV_EDITABLE_', false);

# File extension used for frontend rendering (e.g., .html, .php)
define('_FRONT_', '.html');

# Default HTTP CSRF token field name
define('_KEYGEN_', 'CRSF');

/*----------------------------------------------------------
 | DEFAULT DATABASE DRIVER
 *----------------------------------------------------------*/

# Default database engine (accepted: mysql, oracle, pgsql, sqlserver, sqlite, mongodb, redis)
define('_FIRST_DRIVER_', 'mysql');

/*----------------------------------------------------------
 | DIRECTORY PATHS & MAIN STRUCTURE
 *----------------------------------------------------------*/

# Root folder for main binaries
define('_DIR_MAIN_', 'bin');

# Main vendor directory (for Composer or packages)
define('_DIR_VENDOR_', 'vendor');

# Extension for main route or module references
define('_MAIN_EXTENSION_', '_ep');

# Views directory for public content
define('_DIR_VIEWS_', 'public');

# Static media/documents directory
define('_DIR_MEDIA_', 'static/docs/');

# Root base path of the project
define('_ROOT_', dirname(__DIR__));

# Directory for public images
define('_DIR_IMG_', 'static/img/');

# Directory for PDF and printable files
define('_DIR_PDF_', 'static/docs/');

# Template folder for all users
define('_DIR_MAIN_TEMP_', '/views/main/');

# Template folder for admin panel
define('_DIR_ADMIN_TEMP_', '/views/admin/');

# Database-related scripts and configurations
define('_DIR_database_', 'bin/database');

# Migration system directory
define('_DIR_MIGRATION_', 'bin/database/gearShift');

# Main Epaphrodites system folder
define('_EPAPHRODITE_', 'bin/epaphrodites');

# CLI/Console model files
define('_CONSOLE_', 'bin/epaphrodites/Console/Models');

# Static array configuration files
define('_DIR_CONFIG_', 'bin/database/datas/arrays/');

# JSON configuration/data files
define('_DIR_JSON_DATAS_', 'bin/database/datas/json');

# TOML data files (optional use)
define('_DIR_TOML_DATAS_', 'bin/database/datas/toml/');

# SQLite-based data store files
define('_DIR_SQLITE_DATAS_', 'bin/database/datas/SqlLite/');

# INI configuration files
define('_DIR_CONFIG_INI_', 'bin/config/');

# Project domain base when working in local (e.g., 'epaphrodite-framework/')
define('_DOMAINE_', '');

# Path used for routing to fake (virtual) folders
define('_FAKE_', 'view/');

# Homepage path (default entry)
define('_HOME_', _FAKE_ . 'index/');

# Login page route
define('_LOGIN_', _FAKE_ . 'login/');

# Logout route
define('_LOGOUT_', 'logout/');

# Python execution files folder
define('_PYTHON_FILE_FOLDERS_', 'bin/epaphrodites/python/');

# Main dashboard entry point
define('_DASHBOARD_', 'dashboard/');

# Main dashboard modules folder
define('_DASHBOARD_FOLDERS_', 'dashboardFolder/');

/*----------------------------------------------------------
 | SESSION & AUTHENTICATION IDENTIFIERS
 *----------------------------------------------------------*/

# Global user session name
define('_SESSION_', 'session');

# CSRF protection token name
define('_CRSF_TOKEN_', 'crsfToken');

# Session keys for authenticated user context
define('_AUTH_ID_', 'id');
define('_AUTH_TYPE_', 'type');
define('_AUTH_NAME_', 'usersname');
define('_AUTH_LOGIN_', 'login');
define('_AUTH_CONTACT_', 'contact');
define('_AUTH_EMAIL_', 'email');
define('_AUTH_OTP_', 'sessionOTP');
define('_AUTH_VERIFY_', 'otpVerify');
define('_AUTH_CONFIRM_', 'otpConfirm');

# CSRF token input field
define('CSRF_FIELD_NAME', 'token_csrf');

/*----------------------------------------------------------
 | LOGGING & EXTERNAL SERVICES
 *----------------------------------------------------------*/

# Default backend log file
define('_SERVER_LOG_', 'server.log');

# DeepL API key (used for translation if activated)
define('_YOUR_DEEPL_API_KEY', 'INSERT_YOUR_API_KEY');

# Path to frontend color config (JSON format)
define('_DIR_COLORS_PATH_', 'bin/config/Config.json');

/*----------------------------------------------------------
 | TAXECONNECT PUBLIC LINKS
 *----------------------------------------------------------*/

# Public website
define('_TAXECONNECT_SITE_URL_', 'https://taxeconnect.ci');

# Documentation website
define('_TAXECONNECT_DOCS_URL_', 'https://taxeconnect.ci/docs');

# Source repository
define('_TAXECONNECT_GITHUB_URL_', 'https://github.com/taxeconnect-ci/taxeconnect-ci');

# Community/support
define('_TAXECONNECT_SUPPORT_URL_', 'https://taxeconnect.slack.com');

# Public videos/training
define('_TAXECONNECT_VIDEOS_URL_', 'https://www.youtube.com/@taxeconnectci');

# Shared secret for signed payment callbacks (replace in production)
define('_TAXECONNECT_CALLBACK_SECRET_', 'change-me-in-production');
