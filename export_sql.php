<?php
// Export SQL pour InfinityFREE
$host = 'localhost';
$db   = 'impact_emploi';
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "-- ======================================\n";
    echo "-- Export pour InfinityFREE\n";
    echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
    echo "-- ======================================\n\n";
    
    $tables = ['users', 'jobs', 'candidatures', 'feedbacks', 'activity_logs'];
    
    foreach($tables as $table) {
        echo "\n-- TABLE: $table\n";
        echo "DROP TABLE IF EXISTS `$table`;\n";
        
        // Créer la table
        $createStmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $createData = $createStmt->fetch();
        echo $createData['Create Table'] . ";\n\n";
        
        // Insérer les données
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($rows)) {
            $cols = array_keys($rows[0]);
            $colList = '`' . implode('`, `', $cols) . '`';
            
            foreach($rows as $row) {
                $vals = array_map(function($v) use ($pdo) {
                    if($v === null) return 'NULL';
                    return $pdo->quote($v);
                }, $row);
                $valList = implode(', ', $vals);
                echo "INSERT INTO `$table` ($colList) VALUES ($valList);\n";
            }
            echo "\n";
        }
    }
    
    echo "-- ======================================\n";
    echo "-- FIN DE L'EXPORT\n";
    echo "-- ======================================\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
