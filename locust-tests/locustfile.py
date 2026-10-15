# ruff: noqa: F401

"""
Punto de entrada donde se eligen que tareas ejecutar.
SOLO importen las pruebas a realizar en el modulo tasks.
"""

from tasks.facturacion import FacturacionUser
from tasks.productos import ProductosUser
from tasks.trabajadores import TrabajadoresUser
