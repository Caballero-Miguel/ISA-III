<?php

declare(strict_types=1);

namespace ISA3\Repository;

use PDO;

final class CasoRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function crear(array $datos): int
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO casos_prueba (
                codigo, modulo, tecnica, subtecnica, objetivo, precondiciones,
                datos_entrada, pasos_ejecucion, resultado_esperado,
                resultado_obtenido, estado, observaciones, evidencia, usuario_id
            ) VALUES (
                :codigo, :modulo, :tecnica, :subtecnica, :objetivo, :precondiciones,
                :datos_entrada, :pasos_ejecucion, :resultado_esperado,
                :resultado_obtenido, :estado, :observaciones, :evidencia, :usuario_id
            )'
        );
        $consulta->execute([
            'codigo' => $datos['codigo'],
            'modulo' => $datos['modulo'],
            'tecnica' => $datos['tecnica'],
            'subtecnica' => $datos['subtecnica'],
            'objetivo' => $datos['objetivo'],
            'precondiciones' => $datos['precondiciones'] ?? null,
            'datos_entrada' => $datos['datos_entrada'] ?? null,
            'pasos_ejecucion' => $datos['pasos_ejecucion'],
            'resultado_esperado' => $datos['resultado_esperado'],
            'resultado_obtenido' => $datos['resultado_obtenido'] ?? null,
            'estado' => $datos['estado'],
            'observaciones' => $datos['observaciones'] ?? null,
            'evidencia' => $datos['evidencia'] ?? null,
            'usuario_id' => $datos['usuario_id'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare(
            'SELECT c.*, u.nombre AS tester_nombre
             FROM casos_prueba c
             INNER JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.id = :id
             LIMIT 1'
        );
        $consulta->execute(['id' => $id]);
        $caso = $consulta->fetch();

        return $caso === false ? null : $caso;
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $consulta = $this->pdo->prepare(
            'SELECT c.*, u.nombre AS tester_nombre
             FROM casos_prueba c
             INNER JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.usuario_id = :usuario_id
             ORDER BY c.fecha_creacion DESC, c.id DESC'
        );
        $consulta->execute(['usuario_id' => $usuarioId]);

        return $consulta->fetchAll();
    }

    public function listarTodos(): array
    {
        $consulta = $this->pdo->prepare(
            'SELECT c.*, u.nombre AS tester_nombre
             FROM casos_prueba c
             INNER JOIN usuarios u ON u.id = c.usuario_id
             ORDER BY c.fecha_creacion DESC, c.id DESC'
        );
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $consulta = $this->pdo->prepare(
            'UPDATE casos_prueba SET
                modulo = :modulo,
                tecnica = :tecnica,
                subtecnica = :subtecnica,
                objetivo = :objetivo,
                precondiciones = :precondiciones,
                datos_entrada = :datos_entrada,
                pasos_ejecucion = :pasos_ejecucion,
                resultado_esperado = :resultado_esperado,
                resultado_obtenido = :resultado_obtenido,
                estado = :estado,
                observaciones = :observaciones,
                evidencia = :evidencia
             WHERE id = :id'
        );
        $consulta->execute([
            'modulo' => $datos['modulo'],
            'tecnica' => $datos['tecnica'],
            'subtecnica' => $datos['subtecnica'],
            'objetivo' => $datos['objetivo'],
            'precondiciones' => $datos['precondiciones'] ?? null,
            'datos_entrada' => $datos['datos_entrada'] ?? null,
            'pasos_ejecucion' => $datos['pasos_ejecucion'],
            'resultado_esperado' => $datos['resultado_esperado'],
            'resultado_obtenido' => $datos['resultado_obtenido'] ?? null,
            'estado' => $datos['estado'],
            'observaciones' => $datos['observaciones'] ?? null,
            'evidencia' => $datos['evidencia'] ?? null,
            'id' => $id,
        ]);

        return $consulta->rowCount() > 0;
    }

    public function eliminar(int $id): bool
    {
        $consulta = $this->pdo->prepare('DELETE FROM casos_prueba WHERE id = :id');
        $consulta->execute(['id' => $id]);

        return $consulta->rowCount() > 0;
    }

    public function obtenerSiguienteCodigo(): string
    {
        $consulta = $this->pdo->prepare(
            "SELECT codigo FROM casos_prueba
             WHERE codigo LIKE 'CP-%'
             ORDER BY id DESC
             LIMIT 1"
        );
        $consulta->execute();
        $ultimoCodigo = $consulta->fetchColumn();

        if ($ultimoCodigo === false) {
            return 'CP-001';
        }

        $numero = 0;
        if (preg_match('/^CP-(\d+)$/', (string) $ultimoCodigo, $coincidencias) === 1) {
            $numero = (int) $coincidencias[1];
        }

        return 'CP-' . str_pad((string) ($numero + 1), 3, '0', STR_PAD_LEFT);
    }

    public function contarPorEstado(?int $usuarioId): array
    {
        $condicion = $usuarioId === null ? '' : ' WHERE usuario_id = :usuario_id';
        $consulta = $this->pdo->prepare(
            'SELECT estado, COUNT(*) AS total
             FROM casos_prueba' . $condicion . '
             GROUP BY estado'
        );
        $parametros = $usuarioId === null ? [] : ['usuario_id' => $usuarioId];
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    public function contarPorTecnica(?int $usuarioId): array
    {
        $condicion = $usuarioId === null ? '' : ' WHERE usuario_id = :usuario_id';
        $consulta = $this->pdo->prepare(
            'SELECT tecnica, COUNT(*) AS total
             FROM casos_prueba' . $condicion . '
             GROUP BY tecnica'
        );
        $parametros = $usuarioId === null ? [] : ['usuario_id' => $usuarioId];
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }
}
