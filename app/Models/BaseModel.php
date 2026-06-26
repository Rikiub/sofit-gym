<?php

namespace App\Models;

use PDO;
use PDOStatement;
use Throwable;

abstract class BaseModel
{
    public function __construct(
        protected PDO $pdo
    ) {}

    /**
     * Prepara una consulta con parametros y la ejecuta inmediatamente.
     *
     * En pocas palabras, es un helper para evitar el repetitivo patron:
     * ```
     * $stmt = $this->pdo->prepare($sql);
     * $stmt->execute($params);
     * ```
     *
     * @param $sql Codigo SQL a preparar.
     * @param $params Parametros a remplazar en el SQL.
     */
    protected function pdoQuery(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Busca y devuelve una sola fila.
     *
     * @param $sql Codigo SQL a agregar.
     * @param $conditions Array asociativo que sera convertido en `column = value`.
     */
    protected function pdoFetch(string $sql, array $conditions): ?array
    {
        $whereParts = [];
        foreach ($conditions as $col => $val) {
            $whereParts[] = "$col = :{$col}";
        }

        $row = $this->pdoQuery(
            sprintf(
                "%s WHERE %s",
                $sql,
                join(' AND ', $whereParts),
            )
        )->fetch();

        return $row ?? null;
    }

    /**
     * Insertar fila en una tabla.
     *
     * @param $table Tabla a seleccionar.
     * @param $data Array asociativo donde cada key-value debe corresponder a una columna.
     */
    protected function pdoInsert(string $table, array $data): PDOStatement
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            join(', ', $columns),
            join(', ', $placeholders),
        );

        return $this->pdoQuery($sql, $data);
    }

    /**
     * Actualizar fila en una tabla.
     *
     * @param $table Tabla a seleccionar.
     * @param $data Array asociativo donde cada key-value debe corresponder a una columna.
     * @param $conditions Array asociativo que sera convertido en `column = value`.
     */
    protected function pdoUpdate(string $table, array $data, array $conditions): PDOStatement
    {
        $params = [];

        // Preparar partes del SQL y prefixear parametros
        $setParts = [];
        foreach ($data as $col => $val) {
            $placeholder = ":set_$col";
            $setParts[] = "$col = $placeholder";
            $params[$placeholder] = $val;
        }

        $whereParts = [];
        foreach ($conditions as $col => $val) {
            $placeholder = ":where_$col";
            $whereParts[] = "$col = $placeholder";
            $params[$placeholder] = $val;
        }

        // Construir SQL
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            join(', ', $setParts),
            join(' AND ', $whereParts),
        );

        // Ejecutar consulta
        return $this->pdoQuery($sql, $params);
    }

    /**
     * Eliminar fila en una tabla.
     *
     * @param $table Tabla a seleccionar.
     * @param $conditions Array asociativo que sera convertido en `column = value`.
     */
    protected function pdoDelete(string $table, array $conditions): PDOStatement
    {
        $whereParts = [];
        foreach ($conditions as $col => $val) {
            $whereParts[] = "$col = :{$col}";
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            join(' AND ', $whereParts),
        );

        return $this->pdoQuery($sql, $conditions);
    }

    /** Inicia una transacción, hace commit en exito y rollback en excepciones automaticamente.
     * 
     * @template T
     * @param callable(PDO): T $callback
     * @return T
     */
    protected function pdoTransaction(callable $callback)
    {
        $this->pdo->beginTransaction();

        try {
            $result = $callback($this->pdo);
            $this->pdo->commit();
            return $result;
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }
}
