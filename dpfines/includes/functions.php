<?php
// Get latest fines
function getLatestFines($conn, $limit = 10) {
    $stmt = $conn->prepare("SELECT * FROM global_fines ORDER BY fine_date DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search fines with filters
function searchFines($conn, $filters) {
    $sql = "SELECT * FROM global_fines WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($filters['search'])) {
        $sql .= " AND (organisation LIKE ? OR summary LIKE ? OR articles_breached LIKE ?)";
        $searchParam = "%{$filters['search']}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }

    if (!empty($filters['regulator'])) {
        $sql .= " AND regulator = ?";
        $params[] = $filters['regulator'];
        $types .= "s";
    }

    if (!empty($filters['sector'])) {
        $sql .= " AND sector = ?";
        $params[] = $filters['sector'];
        $types .= "s";
    }

    if (!empty($filters['year'])) {
        $sql .= " AND YEAR(fine_date) = ?";
        $params[] = $filters['year'];
        $types .= "i";
    }

    if (!empty($filters['violation_type'])) {
        $sql .= " AND violation_type = ?";
        $params[] = $filters['violation_type'];
        $types .= "s";
    }

    $sql .= " ORDER BY fine_date DESC LIMIT 50";

    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Get single fine by ID
function getFineById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM global_fines WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get regulator statistics
function getRegulatorStats($conn, $limit = 4) {
    $sql = "SELECT regulator, COUNT(*) as count 
            FROM global_fines 
            GROUP BY regulator 
            ORDER BY count DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get sector statistics
function getSectorStats($conn, $limit = 4) {
    $sql = "SELECT sector, COUNT(*) as count 
            FROM global_fines 
            GROUP BY sector 
            ORDER BY count DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Format currency amount
function formatAmount($amount, $currency) {
    $symbols = [
        'EUR' => '€',
        'GBP' => '£',
        'USD' => '$',
        'AUD' => 'A$',
        'CAD' => 'C$'
    ];
    
    $symbol = $symbols[$currency] ?? $currency;
    
    if ($amount >= 1000000000) {
        return $symbol . number_format($amount / 1000000000, 1) . 'B';
    } elseif ($amount >= 1000000) {
        return $symbol . number_format($amount / 1000000, 1) . 'M';
    } elseif ($amount >= 1000) {
        return $symbol . number_format($amount / 1000, 1) . 'K';
    }
    
    return $symbol . number_format($amount);
}

// Sanitize output
function clean($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>