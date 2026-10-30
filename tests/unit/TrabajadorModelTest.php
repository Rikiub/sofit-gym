<?php

namespace Tests\Unit\Models;

use App\Models\Personas\Persona;
use App\Models\Personas\PersonaModel;
use App\Models\Trabajador;
use App\Models\TrabajadorModel;
use DateTimeImmutable;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class TrabajadorModelTest extends TestCase
{
    private TrabajadorModel&MockObject $model;
    private PersonaModel&MockObject    $personaModel;
    private PDOStatement&MockObject    $stmt;

    protected function setUp(): void
    {
        $this->personaModel = $this->createMock(PersonaModel::class);
        $this->stmt         = $this->createMock(PDOStatement::class);

        $this->model = $this->getMockBuilder(TrabajadorModel::class)
            ->onlyMethods(['dbQuery', 'dbInsert', 'dbUpdate', 'dbDelete', 'dbTransaction'])
            ->setConstructorArgs([$this->personaModel])
            ->getMock();
    }

    /** Fila devuelta por sqlSelect(). */
    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'cedula'              => 'V-12345678',
            'nombre'              => 'Juan',
            'apellido'            => 'Perez',
            'nombre_completo'     => 'Juan Perez',
            'correo'              => 'juan@example.com',
            'telefono'            => '0414-1234567',
            'direccion'           => 'Av. Siempre Viva',
            'activo'              => 1,
            'fecha_nacimiento'    => '1990-05-10',
            'fecha_creacion'      => '2024-01-01 10:00:00',
            'id_rol'              => 1,
            'rol'                 => 'Administrador',
            'salario'             => 1500.0,
            'fecha_contratacion'  => '2024-01-15',
        ], $overrides);
    }

    // getSummary

    public function testGetSummaryReturnsRow(): void
    {
        $expected = ['total_trabajadores' => 5, 'salario_total_pagado' => 7500.0];

        $this->stmt->method('fetch')->willReturn($expected);
        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with($this->stringContains('FROM trabajador'))
            ->willReturn($this->stmt);

        $this->assertSame($expected, $this->model->getSummary());
    }

    // find

    public function testFindReturnsTrabajadorWhenFound(): void
    {
        $this->stmt->method('fetch')->willReturn($this->makeRow());

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->stringContains('WHERE trabajador.cedula = ?'),
                ['V-12345678']
            )
            ->willReturn($this->stmt);

        $result = $this->model->find('V-12345678');

        $this->assertInstanceOf(Trabajador::class, $result);
        $this->assertSame('V-12345678', $result->cedula);
        $this->assertSame(1, $result->id_rol);
    }

    public function testFindReturnsNullWhenNotFound(): void
    {
        $this->stmt->method('fetch')->willReturn(false);

        $this->model->method('dbQuery')->willReturn($this->stmt);

        $this->assertNull($this->model->find('99999999'));
    }

    // query

    public function testQueryWithoutFiltersSelectsAll(): void
    {
        $this->stmt->method('fetchAll')->willReturn([
            $this->makeRow(['cedula' => 'V-11111111']),
            $this->makeRow(['cedula' => 'V-22222222']),
        ]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->callback(fn(string $sql) => !str_contains($sql, 'WHERE')),
                []
            )
            ->willReturn($this->stmt);

        $result = $this->model->query();

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Trabajador::class, $result);
    }

    public function testQueryWithSearchBuildsLikeClausesForEveryColumn(): void
    {
        $this->stmt->method('fetchAll')->willReturn([$this->makeRow()]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->callback(function (string $sql): bool {
                    // 10 columnas -> 10 LIKE ?
                    return str_contains($sql, 'LIKE ?')
                        && substr_count($sql, 'LIKE ?') === 10;
                }),
                $this->callback(function (array $params): bool {
                    return count($params) === 10
                        && $params[0] === '%Juan%'
                        && $params[9] === '%Juan%';
                })
            )
            ->willReturn($this->stmt);

        $result = $this->model->query('Juan');

        $this->assertCount(1, $result);
    }

    public function testQueryWithRolFilter(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->stringContains('rol.id_rol = ?'),
                [3]
            )
            ->willReturn($this->stmt);

        $this->assertSame([], $this->model->query(null, 3));
    }

    public function testQueryWithSearchAndRolUsesAndBetweenClauses(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('LIKE ?'),
                    $this->stringContains('rol.id_rol = ?'),
                    $this->stringContains(' AND ')
                ),
                $this->callback(fn(array $p) => count($p) === 11 && end($p) === 2)
            )
            ->willReturn($this->stmt);

        $this->model->query('Perez', 2);
    }

    // checkDuplicate

    public function testCheckDuplicateReturnsTrueWhenPersonaExists(): void
    {
        $persona = $this->createMock(Persona::class);
        $this->personaModel->expects($this->once())
            ->method('find')
            ->with('V-12345678')
            ->willReturn($persona);

        $this->assertTrue($this->model->checkDuplicate('V-12345678'));
    }

    public function testCheckDuplicateReturnsFalseWhenPersonaNotFound(): void
    {
        $this->personaModel->method('find')->willReturn(null);

        $this->assertFalse($this->model->checkDuplicate('00000000'));
    }

    // insert

    public function testInsertPersistsPersonaAndTrabajadorInTransaction(): void
    {
        $trabajador = new Trabajador(
            cedula: 'V-12345678',
            nombre: 'Juan',
            apellido: 'Perez',
            id_rol: 1,
            salario: 1500.0,
            fecha_contratacion: new DateTimeImmutable('2024-01-15'),
        );

        // Ejecuta el closure dentro de la "transacción"
        $this->model->method('dbTransaction')
            ->willReturnCallback(fn(callable $fn) => $fn());

        // Persona::insert debe ser invocado
        $this->personaModel->expects($this->once())
            ->method('insert')
            ->with($trabajador);

        // dbInsert recibe columnas con PK incluida y fecha formateada
        $this->model->expects($this->once())
            ->method('dbInsert')
            ->with(
                'trabajador',
                $this->callback(
                    fn(array $data) =>
                    $data['cedula'] === 'V-12345678'
                        && $data['id_rol'] === 1
                        && $data['salario'] === 1500.0
                        && $data['fecha_contratacion'] === '2024-01-15'
                )
            );

        // find() final
        $this->stmt->method('fetch')->willReturn($this->makeRow());
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $result = $this->model->insert($trabajador);

        $this->assertInstanceOf(Trabajador::class, $result);
        $this->assertSame('V-12345678', $result->cedula);
    }

    public function testInsertRollsBackWhenDbInsertFails(): void
    {
        $trabajador = new Trabajador(
            cedula: 'V-12345678',
            nombre: 'Juan',
            apellido: 'Perez',
            id_rol: 1,
            salario: 1500.0,
            fecha_contratacion: new DateTimeImmutable('2024-01-15'),
        );

        $this->model->method('dbTransaction')
            ->willReturnCallback(function (callable $fn) {
                try {
                    return $fn();
                } catch (Throwable $e) {
                    throw $e;
                }
            });

        $this->personaModel->method('insert');
        $this->model->method('dbInsert')->willThrowException(new \RuntimeException('boom'));

        $this->expectException(\RuntimeException::class);

        $this->model->insert($trabajador);
    }

    // update

    public function testUpdatePersistsPersonaAndTrabajador(): void
    {
        $cedula     = 'V-12345678';
        $trabajador = new Trabajador(
            cedula: $cedula,
            nombre: 'Juan',
            apellido: 'Perez',
            id_rol: 2,
            salario: 2000.0,
            fecha_contratacion: new DateTimeImmutable('2024-06-01'),
        );

        $this->model->method('dbTransaction')
            ->willReturnCallback(fn(callable $fn) => $fn());

        $this->personaModel->expects($this->once())
            ->method('update')
            ->with($cedula, $trabajador);

        $this->model->expects($this->once())
            ->method('dbUpdate')
            ->with(
                'trabajador',
                [
                    'id_rol'             => 2,
                    'salario'            => 2000.0,
                    'fecha_contratacion' => '2024-06-01',
                ],
                ['cedula' => $cedula]
            );

        $this->stmt->method('fetch')->willReturn($this->makeRow(['id_rol' => 2]));
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $result = $this->model->update($cedula, $trabajador);

        $this->assertInstanceOf(Trabajador::class, $result);
    }

    // delete

    public function testDeleteCallsDbDeleteWithPrimaryKey(): void
    {
        $this->model->expects($this->once())
            ->method('dbDelete')
            ->with('trabajador', ['cedula' => 'V-12345678']);

        $this->model->delete('V-12345678');
    }

    // DTO validation

    public function testValidateInsertAcceptsCompleteData(): void
    {
        $trabajador = new Trabajador(
            cedula: 'V-12345678',
            nombre: 'Juan',
            apellido: 'Perez',
            id_rol: 1,
            fecha_contratacion: new DateTimeImmutable('2024-01-15'),
        );

        // No debe lanzar excepción
        $trabajador->validateInsert();
        $this->addToAssertionCount(1);
    }

    public function testValidateInsertThrowsWhenIdRolMissing(): void
    {
        $trabajador = new Trabajador(
            cedula: 'V-12345678',
            nombre: 'Juan',
            apellido: 'Perez',
            fecha_contratacion: new DateTimeImmutable('2024-01-15'),
        );

        $this->expectException(\Throwable::class);

        $trabajador->validateInsert();
    }

    public function testValidateInsertThrowsWhenFechaContratacionMissing(): void
    {
        $trabajador = new Trabajador(
            cedula: 'V-12345678',
            nombre: 'Juan',
            apellido: 'Perez',
            id_rol: 1,
        );

        $this->expectException(Throwable::class);

        $trabajador->validateInsert();
    }
}
