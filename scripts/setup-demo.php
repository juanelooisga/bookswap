<?php
// Creador: Juan Isais Garcia. Inicialización exclusiva de la base de la demo.
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
use Juanc\Bookswap\Config\Database;
use Juanc\Bookswap\Utils\Env;
Env::load(dirname(__DIR__));
if (!Env::bool('DEMO_MODE', false) || !preg_match('/^bookswap_demo(?:_[a-z0-9]+)*$/', Env::get('DB_NAME', ''))) {
    throw new RuntimeException('Utiliza una base dedicada con nombre bookswap_demo o bookswap_demo_prueba.');
}
$db = Database::connection();
$db->exec(file_get_contents(dirname(__DIR__) . '/database/migrations/001_create_bookswap_tables.sql'));
$db->beginTransaction();
try {
    if ((int) $db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 0) {
        $user = $db->prepare('INSERT INTO usuarios (nombre, correo, ubicacion, password) VALUES (?, ?, ?, ?)');
        $ids = [];
        foreach (['Lector Demo Uno', 'Lector Demo Dos'] as $i => $name) {
            $user->execute([$name, 'lector'.($i + 1).'@example.com', 'Ciudad Demo', password_hash(bin2hex(random_bytes(20)), PASSWORD_DEFAULT)]);
            $ids[] = (int) $db->lastInsertId();
        }
        $book = $db->prepare('INSERT INTO libros (usuario_id, titulo, autor, genero, descripcion_estado, ubicacion) VALUES (?, ?, ?, ?, ?, ?)');
        $book->execute([$ids[1], 'El principito', 'Antoine de Saint-Exupéry', 'Literatura', 'Buen estado', 'Ciudad Demo']);
        $book->execute([$ids[0], 'Libro de ejemplo', 'Autor de ejemplo', 'Ficción', 'Buen estado', 'Ciudad Demo']);
    }
    $db->commit();
    echo "Base de demo preparada. No se borraron registros.\n";
} catch (Throwable $e) {
    if ($db->inTransaction()) { $db->rollBack(); }
    throw $e;
}
