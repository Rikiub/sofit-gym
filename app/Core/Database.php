<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use Throwable;

class Database extends PDO
{
    public function __construct(
        string $host = "localhost",
        string $database = "sofit_gym",
        string $username = "root",
        string $password = "",
    ) {
        $charset = 'utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => sprintf("SET time_zone = '%s'", TIMEZONE_OFFSET),
        ];
        $dsn = "mysql:host={$host};dbname={$database};charset={$charset};";

        try {
            parent::__construct(
                dsn: $dsn,
                username: $username,
                password: $password,
                options: $options
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Failed database connection: ' . $e->getMessage());
        }
    }

    /**
     * Prepara una consulta con parametros y la ejecuta inmediatamente.
     *
     * En pocas palabras, es un helper para evitar el repetitivo patron:
     * ```
     * $stmt = $this->db->prepare($sql);
     * $stmt->execute($params);
     * ```
     *
     * @param $sql Codigo SQL a preparar.
     * @param $params Parametros a remplazar en el SQL.
     */
    public function pdoQuery(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Busca y devuelve una sola fila.
     *
     * @param $sql Codigo SQL a agregar.
     * @param $conditions Array asociativo que sera convertido en `column = value`.
     */
    public function pdoFetch(string $sql, array $conditions): ?array
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
    public function pdoInsert(string $table, array $data): PDOStatement
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
    public function pdoUpdate(string $table, array $data, array $conditions): PDOStatement
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
    public function pdoDelete(string $table, array $conditions): PDOStatement
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
    public function pdoTransaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $error) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $error;
        }
    }
}
