<?php
// includes/messages.php
declare(strict_types=1);

// AUTH
define('MSG_FILL_ALL_FIELDS',       'Please fill in all required fields.');
define('MSG_INVALID_EMAIL',         'Please enter a valid email address.');
define('MSG_PASSWORD_TOO_SHORT',    'Password must be at least 8 characters long.');
define('MSG_PASSWORD_MISMATCH',     'Passwords do not match.');
define('MSG_EMAIL_TAKEN',           'This email address is already registered.');
define('MSG_INVALID_CREDENTIALS',   'Email or password is incorrect.');
define('MSG_ACCOUNT_INACTIVE',      'Your account is not active. Please contact support.');
define('MSG_REGISTER_SUCCESS',      'Your account has been created successfully.');
define('MSG_LOGIN_SUCCESS',         'Welcome back!');
define('MSG_LOGOUT_SUCCESS',        'You have been logged out successfully.');
define('MSG_SESSION_EXPIRED',       'Your session has expired. Please log in again.');

// DASHBOARD
define('MSG_NO_CALCULATIONS',       'No calculations yet.');
define('MSG_NO_REPORTS',            'No reports yet.');
define('MSG_NO_CERTIFICATES',       'No certificates yet.');
define('MSG_NO_NOTIFICATIONS',      'No notifications.');
define('MSG_NO_SESSIONS',           'No active sessions.');
define('MSG_NO_PAYMENTS',           'No payments yet.');

// CALCULATOR
define('MSG_CALC_SUCCESS',          'Calculation saved successfully.');
define('MSG_CALC_ERROR',            'Error saving calculation. Please try again.');

// ERROR
define('MSG_ERROR_OCCURRED',        'An error occurred. Please try again.');
define('MSG_UNAUTHORIZED',          'You are not authorized to access this page.');

// ADMIN
define('MSG_ACTION_SUCCESS',        'Action completed successfully.');
define('MSG_DELETE_SUCCESS',        'Item deleted successfully.');
define('MSG_UPDATE_SUCCESS',        'Item updated successfully.');