<?php
try {
    $host = 'aws-1-eu-central-2.pooler.supabase.com';
    $db = 'postgres';
    $user = 'postgres.wqhqiqgkveueeplsnmum';
    $pass = 'smsm.tota.hesho';
    $port = '5432';

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    echo "Attempting connection to $dsn...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully!";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
