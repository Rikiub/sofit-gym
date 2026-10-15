from locust import task

from tasks.base import SofitGymUser


class FacturacionUser(SofitGymUser):
    @task(3)
    def ver_facturacion(self):
        self.client.get("/?page=facturacion")

    @task(2)
    def buscar_pagos(self):
        self.client.get("/?page=facturacion")

    @task(1)
    def registrar_pago(self):
        self.client.post(
            "/?page=facturacion",
            data={
                "cedula_cliente": "28123456",
                "monto": "15.00",
                "metodo_pago": "Efectivo",
                "plan_tipo_id": "1",
            },
        )
