<?php
$host = "ep-divine-wind-axrdxgm2-pooler.c-4.us-east-2.aws.neon.tech";
$db = "neondb";
$user = "ep-divine-wind-axrdxgm2\$neondb_owner";
$pass = "npg_JRwMVgrTS89G";

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass);
    echo "Connected via username!\n";
} catch (Exception $e) {
    echo "Username fail: " . $e->getMessage() . "\n";
}

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options=endpoint=ep-divine-wind-axrdxgm2";
    $pdo = new PDO($dsn, "neondb_owner", $pass);
    echo "Connected via options in DSN!\n";
} catch (Exception $e) {
    echo "Options fail: " . $e->getMessage() . "\n";
}

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options='endpoint=ep-divine-wind-axrdxgm2'";
    $pdo = new PDO($dsn, "neondb_owner", $pass);
    echo "Connected via quoted options in DSN!\n";
} catch (Exception $e) {
    echo "Quoted Options fail: " . $e->getMessage() . "\n";
}

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $passWithEndpoint = "endpoint=ep-divine-wind-axrdxgm2;" . $pass;
    $pdo = new PDO($dsn, "neondb_owner", $passWithEndpoint);
    echo "Connected via password trick!\n";
} catch (Exception $e) {
    echo "Password trick fail: " . $e->getMessage() . "\n";
}
