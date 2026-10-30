<?php

namespace Tests\Unit\Models;

use App\Models\BitacoraModel;
use App\Models\Clientes\Cliente;
use App\Models\Clientes\ClienteModel;
use App\Models\Personas\Persona;
use App\Models\Personas\PersonaModel;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ClienteModelTest extends TestCase
{
    private ClienteModel&MockObject  $model;
    private PersonaModel&MockObject  $personaModel;
    private BitacoraModel&MockObject $logger;
    private PDOStatement&MockObject  $stmt;

    protected function setUp(): void
    {
        $this->personaModel = $this->createMock(PersonaModel::class);
        $this->logger       = $this->createMock(BitacoraModel::class);
        $this->stmt         = $this->createMock(PDOStatement::class);

        $this->model = $this->getMockBuilder(ClienteModel::class)
            ->onlyMethods(['dbQuery', 'dbInsert', 'dbUpdate', 'dbDelete', 'dbTransaction'])
            ->setConstructorArgs([$this->logger, $this->personaModel])
            ->getMock();
    }

    /** Fila típica devuelta por sqlSelect(). */
    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'cedula'           => 'V-12345678',
            'nombre'           => 'Ana',
            'apellido'         => 'Garcia',
            'nombre_completo'  => 'Ana Garcia',
            'correo'           => 'ana@example.com',
            'telefono'         => '0414-1234567',
            'direccion'        => 'Av. Siempre Viva',
            'activo'           => 1,
            'fecha_nacimiento' => '1995-03-20',
            'fecha_creacion'   => '2024-01-01 10:00:00',
            'membresia'        => null,
        ], $overrides);
    }

    // getSummary

    public function testGetSummaryReturnsAggregates(): void
    {
        $expected = [
            'total_clientes'     => 10,
            'membresias_activas' => 6,
            'ganancias_totales'  => 15000.0,
        ];

        $this->stmt->method('fetch')->willReturn($expected);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with($this->logicalAnd(
                $this->stringContains('total_clientes'),
                $this->stringContains('membresias_activas'),
                $this->stringContains('ganancias_totales'),
                $this->stringContains('FROM cliente'),
            ))
            ->willReturn($this->stmt);

        $this->assertSame($expected, $this->model->getSummary());
    }

    // find

    public function testFindReturnsClienteWhenFound(): void
    {
        $this->stmt->method('fetch')->willReturn($this->makeRow());

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->stringContains('WHERE cliente.cedula = ?'),
                ['V-12345678']
            )
            ->willReturn($this->stmt);

        $result = $this->model->find('V-12345678');

        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertSame('V-12345678', $result->cedula);
        $this->assertSame('Ana', $result->nombre);
    }

    public function testFindReturnsNullWhenNotFound(): void
    {
        $this->stmt->method('fetch')->willReturn(false);
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $this->assertNull($this->model->find('99999999'));
    }

    public function testFindKeepsNullMembresiaWhenSqlReturnsNull(): void
    {
        $this->stmt->method('fetch')->willReturn($this->makeRow(['membresia' => null]));
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $result = $this->model->find('V-12345678');

        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertNull($result->membresia);
    }

    // query

    public function testQueryWithoutFiltersSelectsAllAndLogs(): void
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

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                'Clientes listados',
                $this->callback(
                    fn(array $ctx) =>
                    $ctx['modulo'] === 'clientes' && $ctx['accion'] === 'listar'
                )
            );

        $result = $this->model->query();

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Cliente::class, $result);
    }

    public function testQueryWithSearchBuildsNineLikeClauses(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->callback(
                    fn(string $sql) =>
                    substr_count($sql, 'LIKE ?') === 9
                ),
                $this->callback(
                    fn(array $p) =>
                    count($p) === 9
                        && $p[0] === '%Ana%'
                        && $p[8] === '%Ana%'
                )
            )
            ->willReturn($this->stmt);

        $this->model->query('Ana');
    }

    public function testQueryWithExactFiltersUsesEqualityOperators(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('persona.activo = ?'),
                    $this->stringContains('m.id_tipo = ?'),
                    $this->stringContains('me.nombre = ?'),
                ),
                $this->callback(
                    fn(array $p) =>
                    // bool true se castea a int 1
                    $p[0] === 1
                        && $p[1] === 2
                        && $p[2] === 'Activa'
                )
            )
            ->willReturn($this->stmt);

        $this->model->query(null, [
            'activo'           => true,
            'id_tipo'          => 2,
            'estado_membresia' => 'Activa',
        ]);
    }

    public function testQueryWithDateRangeFilters(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('m.fecha_inicio >= ?'),
                    $this->stringContains('m.fecha_inicio <= ?'),
                    $this->stringContains('m.fecha_fin >= ?'),
                    $this->stringContains('m.fecha_fin <= ?'),
                ),
                ['2024-01-01', '2024-12-31', '2024-01-01', '2024-12-31']
            )
            ->willReturn($this->stmt);

        $this->model->query(null, [
            'fecha_inicio_desde' => '2024-01-01',
            'fecha_inicio_hasta' => '2024-12-31',
            'fecha_fin_desde'    => '2024-01-01',
            'fecha_fin_hasta'    => '2024-12-31',
        ]);
    }

    public function testQueryCombinesSearchAndFiltersWithAnd(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('LIKE ?'),
                    $this->stringContains('persona.activo = ?'),
                    $this->stringContains(' AND ')
                ),
                $this->callback(
                    fn(array $p) =>
                    count($p) === 10
                        && $p[0] === '%Ana%'
                        && end($p) === 1
                )
            )
            ->willReturn($this->stmt);

        $this->model->query('Ana', ['activo' => true]);
    }

    public function testQuerySkipsUnknownFilterKeys(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->callback(fn(string $sql) => !str_contains($sql, 'WHERE')),
                []
            )
            ->willReturn($this->stmt);

        $this->model->query(null, [
            'unknown_filter' => 'x',
            'otra_cosa'      => 'y',
        ]);
    }

    public function testQuerySkipsFiltersWithNullOrEmptyValue(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->callback(fn(string $sql) => !str_contains($sql, 'WHERE')),
                []
            )
            ->willReturn($this->stmt);

        $this->model->query(null, [
            'activo'  => null,
            'id_tipo' => '',
        ]);
    }

    public function testQueryLikeFiltersUseCollation(): void
    {
        $this->stmt->method('fetchAll')->willReturn([]);

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->stringContains('COLLATE utf8mb4_unicode_ci'),
                $this->anything()
            )
            ->willReturn($this->stmt);

        $this->model->query(null, ['nombre' => 'Ana']);
    }

    // checkDuplicate

    public function testCheckDuplicateReturnsTrueWhenPersonaExists(): void
    {
        $this->personaModel->expects($this->once())
            ->method('find')
            ->with('V-12345678')
            ->willReturn($this->createMock(Persona::class));

        $this->assertTrue($this->model->checkDuplicate('V-12345678'));
    }

    public function testCheckDuplicateReturnsFalseWhenPersonaNotFound(): void
    {
        $this->personaModel->method('find')->willReturn(null);

        $this->assertFalse($this->model->checkDuplicate('00000000'));
    }

    // insert

    public function testInsertPersistsPersonaAndClienteAndLogs(): void
    {
        $cliente = new Cliente(cedula: 'V-12345678', nombre: 'Ana', apellido: 'Garcia');

        // La transacción ejecuta el closure en línea
        $this->model->method('dbTransaction')
            ->willReturnCallback(fn(callable $fn) => $fn());

        $this->personaModel->expects($this->once())
            ->method('insert')
            ->with($cliente);

        $this->model->expects($this->once())
            ->method('dbInsert')
            ->with('cliente', ['cedula' => 'V-12345678']);

        $this->stmt->method('fetch')->willReturn($this->makeRow());
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $result = $this->model->insert($cliente);

        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertSame('V-12345678', $result->cedula);
    }

    public function testInsertPropagatesExceptionFromDbInsert(): void
    {
        $cliente = new Cliente(cedula: 'V-12345678', nombre: 'Ana', apellido: 'Garcia');

        $this->model->method('dbTransaction')
            ->willReturnCallback(fn(callable $fn) => $fn());

        $this->personaModel->method('insert');
        $this->model->method('dbInsert')->willThrowException(new RuntimeException('boom'));

        $this->expectException(RuntimeException::class);
        $this->model->insert($cliente);
    }

    // update

    public function testUpdatePersistsPersonaAndLogs(): void
    {
        $cedula  = 'V-12345678';
        $cliente = new Cliente(cedula: $cedula, nombre: 'Ana Updated');

        $this->personaModel->expects($this->once())
            ->method('update')
            ->with($cedula, $cliente);

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                'Cliente \'{cedula}\' actualizado',
                $this->callback(
                    fn(array $ctx) =>
                    $ctx['modulo'] === 'clientes'
                        && $ctx['accion'] === 'editar'
                        && $ctx['cedula'] === $cedula
                )
            );

        $this->stmt->method('fetch')->willReturn($this->makeRow());
        $this->model->method('dbQuery')->willReturn($this->stmt);

        $result = $this->model->update($cedula, $cliente);

        $this->assertInstanceOf(Cliente::class, $result);
    }

    public function testUpdateUsesClienteCedulaForFinalLookup(): void
    {
        $cedulaParam = 'V-OLD';
        $cliente = new Cliente(cedula: 'V-NEW', nombre: 'Ana');

        $this->personaModel->method('update');
        $this->logger->method('log');

        $this->model->expects($this->once())
            ->method('dbQuery')
            ->with(
                $this->stringContains('WHERE cliente.cedula = ?'),
                ['V-NEW']
            )
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')->willReturn($this->makeRow(['cedula' => 'V-NEW']));
        $this->model->update($cedulaParam, $cliente);
    }

    // delete

    public function testDeleteCallsDbDeleteAndLogs(): void
    {
        $this->model->expects($this->once())
            ->method('dbDelete')
            ->with('cliente', ['cedula' => 'V-12345678']);

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                'Cliente \'{cedula}\' eliminado',
                $this->callback(
                    fn(array $ctx) =>
                    $ctx['modulo'] === 'clientes'
                        && $ctx['accion'] === 'eliminar'
                        && $ctx['cedula'] === 'V-12345678'
                )
            );

        $this->model->delete('V-12345678');
    }
}
