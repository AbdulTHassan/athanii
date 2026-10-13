<?php

declare(strict_types=1);

/**
 * Get a random active Zakat motivation.
 *
 * The record contains:
 * - Arabic text
 * - English text
 * - Hausa text
 * - Reference
 */
function getRandomMotivation(PDO $pdo, string $language = 'en'): ?array
{
    $sql = "
        SELECT
            id,
            type,
            title,
            arabic_text,
            text AS english_text,
            hausa_text,
            reference,
            source_url
        FROM zakat_motivations
        WHERE status = 'active'
        ORDER BY RAND()
        LIMIT 1
    ";

    $stmt = $pdo->query($sql);

    $motivation = $stmt->fetch(PDO::FETCH_ASSOC);

    return $motivation ?: null;
}