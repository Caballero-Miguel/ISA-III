<?php

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

$mensajes = [];

try {
    $conexion = db();
    $buscarUsuario = $conexion->prepare(
        'SELECT id FROM usuarios WHERE correo = :correo LIMIT 1'
    );
    $insertarUsuario = $conexion->prepare(
        'INSERT INTO usuarios (nombre, correo, password, rol)
         VALUES (:nombre, :correo, :password, :rol)'
    );

    $usuariosIniciales = [
        [
            'nombre' => 'Administrador ISA3',
            'correo' => 'admin@isa3.local',
            'password' => 'Admin123!',
            'rol' => 'admin',
        ],
        [
            'nombre' => 'Tester ISA3',
            'correo' => 'tester@isa3.local',
            'password' => 'Tester123!',
            'rol' => 'tester',
        ],
    ];

    foreach ($usuariosIniciales as $usuario) {
        $buscarUsuario->execute(['correo' => $usuario['correo']]);

        if ($buscarUsuario->fetch() !== false) {
            $mensajes[] = [
                'tipo' => 'aviso',
                'texto' => 'El usuario ' . $usuario['correo'] . ' ya existe; no se realizaron cambios.',
            ];
            continue;
        }

        $insertarUsuario->execute([
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'password' => password_hash($usuario['password'], PASSWORD_DEFAULT),
            'rol' => $usuario['rol'],
        ]);

        $mensajes[] = [
            'tipo' => 'exito',
            'texto' => 'El usuario ' . $usuario['correo'] . ' fue creado correctamente.',
        ];
    }
} catch (Throwable $error) {
    $mensajes[] = [
        'tipo' => 'error',
        'texto' => 'No fue posible completar la inicialización: ' . $error->getMessage(),
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicialización ISA3 QA System</title>
</head>
<body>
    <main>
        <h1>Inicialización de usuarios ISA3 QA System</h1>
        <?php foreach ($mensajes as $mensaje): ?>
            <p>
                <strong><?= htmlspecialchars(ucfirst($mensaje['tipo']), ENT_QUOTES, 'UTF-8') ?>:</strong>
                <?= htmlspecialchars($mensaje['texto'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endforeach; ?>
        <p><strong>Recomendación:</strong> elimina el archivo seed.php después de usarlo.</p>
    </main>
</body>
</html>
