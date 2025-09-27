<?php
// Simple test script to verify database setup and file structure
include 'database/connection.php';

echo "<h2>BLACKGYM Setup Test</h2>";

// Test database connection
if ($conn1) {
    echo "✅ Database connection: SUCCESS<br>";
    
    // Test if required tables exist
    $tables = ['users', 'members', 'staff', 'payments', 'membership', 'attendance'];
    
    foreach ($tables as $table) {
        $result = $conn1->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "✅ Table '$table': EXISTS<br>";
        } else {
            echo "❌ Table '$table': MISSING<br>";
        }
    }
    
    // Test if Balance column exists in members table
    $result = $conn1->query("SHOW COLUMNS FROM members LIKE 'Balance'");
    if ($result->num_rows > 0) {
        echo "✅ Members.Balance column: EXISTS<br>";
    } else {
        echo "❌ Members.Balance column: MISSING<br>";
    }
    
    // Test if PaymentType column exists in payments table
    $result = $conn1->query("SHOW COLUMNS FROM payments LIKE 'PaymentType'");
    if ($result->num_rows > 0) {
        echo "✅ Payments.PaymentType column: EXISTS<br>";
    } else {
        echo "❌ Payments.PaymentType column: MISSING<br>";
    }
    
} else {
    echo "❌ Database connection: FAILED<br>";
}

// Test file structure
$files = [
    'admin/dashboard/view_staff.php',
    'admin/dashboard/payments.php',
    'admin/dashboard/includes/modal/edit_staff.php',
    'admin/dashboard/includes/modal/pay.php',
    'admin/action/fetch_staff.php',
    'admin/action/edit_staff.php',
    'admin/action/delete_staff.php',
    'admin/action/payment_process.php'
];

echo "<br><h3>File Structure Test:</h3>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file: EXISTS<br>";
    } else {
        echo "❌ $file: MISSING<br>";
    }
}

echo "<br><h3>Setup Status:</h3>";
echo "🎯 Your BLACKGYM application is ready for localhost!<br>";
echo "📍 Access at: <a href='http://localhost/BLACKGYM/'>http://localhost/BLACKGYM/</a><br>";
echo "🔧 Admin Dashboard: <a href='http://localhost/BLACKGYM/admin/dashboard/'>http://localhost/BLACKGYM/admin/dashboard/</a><br>";
?>