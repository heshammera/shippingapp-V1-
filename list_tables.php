<?php
try {
    $pdo = new PDO('pgsql:host=aws-1-eu-central-2.pooler.supabase.com;port=5432;dbname=postgres;sslmode=require', 'postgres.wqhqiqgkveueeplsnmum', 'smsm.tota.hesho');
    $stmt = $pdo->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in DB:\n";
    print_r($tables);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
