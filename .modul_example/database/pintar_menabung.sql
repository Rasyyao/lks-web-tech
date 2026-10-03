-- =============================================
-- PintarMenabung Database Dump
-- LKS Web Technologies - Server Side Module
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS `pintar_menabung`;
CREATE DATABASE `pintar_menabung` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pintar_menabung`;

-- =============================================
-- Table: users
-- =============================================
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: currencies
-- =============================================
CREATE TABLE `currencies` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `symbol` VARCHAR(255) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `currencies_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: categories
-- =============================================
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NOT NULL,
    `type` ENUM('EXPENSE', 'INCOME') NOT NULL,
    `color` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: wallets
-- =============================================
CREATE TABLE `wallets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `currency_code` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `wallets_user_id_foreign` (`user_id`),
    KEY `wallets_currency_code_foreign` (`currency_code`),
    CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `wallets_currency_code_foreign` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: transactions
-- =============================================
CREATE TABLE `transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `wallet_id` BIGINT UNSIGNED NOT NULL,
    `amount` BIGINT UNSIGNED NOT NULL,
    `note` VARCHAR(255) DEFAULT NULL,
    `date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `transactions_category_id_foreign` (`category_id`),
    KEY `transactions_wallet_id_foreign` (`wallet_id`),
    CONSTRAINT `transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Laravel Sanctum: personal_access_tokens
-- =============================================
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT DEFAULT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Laravel Migrations table
-- =============================================
CREATE TABLE `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- SEED DATA
-- =============================================

-- Currencies
INSERT INTO `currencies` (`id`, `name`, `symbol`, `code`, `created_at`, `updated_at`) VALUES
(1, 'US Dollar', '$', 'USD', NOW(), NOW()),
(2, 'Indonesian Rupiah', 'Rp', 'IDR', NOW(), NOW()),
(3, 'Euro', '€', 'EUR', NOW(), NOW()),
(4, 'Japanese Yen', '¥', 'JPY', NOW(), NOW()),
(5, 'British Pound', '£', 'GBP', NOW(), NOW());

-- Categories (EXPENSE)
INSERT INTO `categories` (`id`, `name`, `icon`, `type`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Outgoing Transfer', '💸', 'EXPENSE', '#FF6B6B', NOW(), NOW()),
(2, 'Shopping', '🛍️', 'EXPENSE', '#FF8C42', NOW(), NOW()),
(3, 'Food & Drinks', '🍔', 'EXPENSE', '#FFD166', NOW(), NOW()),
(4, 'Transportation', '🚗', 'EXPENSE', '#06D6A0', NOW(), NOW()),
(5, 'Entertainment', '🎬', 'EXPENSE', '#118AB2', NOW(), NOW()),
(6, 'Bills & Utilities', '📄', 'EXPENSE', '#073B4C', NOW(), NOW()),
(7, 'Health', '🏥', 'EXPENSE', '#EF476F', NOW(), NOW()),
(8, 'Education', '📚', 'EXPENSE', '#7209B7', NOW(), NOW()),
(9, 'Groceries', '🛒', 'EXPENSE', '#55EFC4', NOW(), NOW());

-- Categories (INCOME)
INSERT INTO `categories` (`id`, `name`, `icon`, `type`, `color`, `created_at`, `updated_at`) VALUES
(10, 'Incoming Transfer', '💰', 'INCOME', '#00B894', NOW(), NOW()),
(11, 'Salary', '💼', 'INCOME', '#0984E3', NOW(), NOW()),
(12, 'Bonus', '🎁', 'INCOME', '#6C5CE7', NOW(), NOW()),
(13, 'Investment', '📈', 'INCOME', '#FDCB6E', NOW(), NOW()),
(14, 'Freelance', '💻', 'INCOME', '#E17055', NOW(), NOW());

-- Sample Users (password: password123)
-- Password hash for 'password123' using bcrypt
INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Budi', 'budi@webtech.id', '$2y$12$XlS2UiYOGPnGOVnxqMqFJ.t9RjGfJQYPvwbEVkP5GiB3cZ6fC9u6y', NOW(), NOW()),
(2, 'Dedi', 'dedi@webtech.id', '$2y$12$XlS2UiYOGPnGOVnxqMqFJ.t9RjGfJQYPvwbEVkP5GiB3cZ6fC9u6y', NOW(), NOW());

-- Sample Wallets
INSERT INTO `wallets` (`id`, `user_id`, `name`, `currency_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cash IDR', 'IDR', NOW(), NOW()),
(2, 1, 'Bank', 'IDR', NOW(), NOW()),
(3, 1, 'Savings USD', 'USD', NOW(), NOW()),
(4, 2, 'Dompet', 'IDR', NOW(), NOW());

-- Sample Transactions for user 1
INSERT INTO `transactions` (`id`, `category_id`, `wallet_id`, `amount`, `note`, `date`, `created_at`, `updated_at`) VALUES
-- Income transactions
(1, 11, 1, 5000000, 'Gaji Juli', '2025-07-01', NOW(), NOW()),
(2, 11, 2, 10000000, 'Gaji Juli Bank', '2025-07-01', NOW(), NOW()),
(3, 12, 1, 500000, 'Bonus project', '2025-07-15', NOW(), NOW()),
(4, 10, 2, 2500000, 'Transfer dari klien', '2025-07-20', NOW(), NOW()),
-- Expense transactions
(5, 3, 1, 50000, 'Starbucks', '2025-07-31', NOW(), NOW()),
(6, 4, 1, 35000, 'Grab ke kantor', '2025-07-28', NOW(), NOW()),
(7, 2, 2, 250000, 'Beli baju', '2025-07-25', NOW(), NOW()),
(8, 9, 1, 150000, 'Belanja bulanan', '2025-07-10', NOW(), NOW()),
(9, 6, 2, 500000, 'Listrik Juli', '2025-07-05', NOW(), NOW()),
(10, 3, 1, 75000, 'Makan siang', '2025-07-30', NOW(), NOW()),
-- More income
(11, 14, 3, 200, 'Freelance payment', '2025-07-18', NOW(), NOW()),
(12, 13, 3, 100, 'Dividend', '2025-07-22', NOW(), NOW()),
-- Transactions for user 2
(13, 11, 4, 4000000, 'Gaji', '2025-07-01', NOW(), NOW()),
(14, 3, 4, 100000, 'Makan', '2025-07-15', NOW(), NOW());
