<?php

declare(strict_types=1);

namespace ISA3\Repository;

use PDO;

final class UsuarioRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $consulta = $this->pdo->prepare(
            'SELECT id, nombre, correo, password, rol
             FROM usuarios
             WHERE correo = :correo
             LIMIT 1'
        );
        $consulta->execute(['correo' => $correo]);
        $usuario = $consulta->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare(
            'SELECT id, nombre, correo, password, rol
             FROM usuarios
             WHERE id = :id
             LIMIT 1'
        );
        $consulta->execute(['id' => $id]);
        $usuario = $consulta->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function crear(string $nombre, string $correo, string $password, string $rol): int
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO usuarios (nombre, correo, password, rol)
             VALUES (:nombre, :correo, :password, :rol)'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'password' => $password,
            'rol' => $rol,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function listarTodos(): array
    {
        $consulta = $this->pdo->prepare(
            'SELECT id, nombre, correo, rol, fecha_creacion
             FROM usuarios
             ORDER BY fecha_creacion DESC, id DESC'
        );
        $consulta->execute();

        return $consulta->fetchAll();
    }
}
