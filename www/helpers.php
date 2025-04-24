<?php
// Common helper functions for lookups

/**
 * Lookup a user's email by their user_id.
 *
 * @param int $userId
 * @return string|null
 */
function fetchUserEmail(int $userId): ?string {
    global $pdo;  // from db.php
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $email = $stmt->fetchColumn();
    return $email ?: null;
}
?>
