import datetime
import random

from locust import task

from tasks.base import SofitGymUser


def make_payload():
    cedula = f"V-{random.randint(1_000_000, 99_999_999)}"
    return {
        "cedula": cedula,
        "nombre": "Juan",
        "apellido": "Perez",
        "correo": f"{cedula.replace('-', '').lower()}@example.com",
        "telefono": "0414-1234567",
        "direccion": "Av. Principal, Caracas",
        "fecha_nacimiento": "1990-05-15",
        "fecha_contratacion": datetime.date.today().isoformat(),
        "id_rol": 2,
        "salario": 1500.00,
    }


class TrabajadoresUser(SofitGymUser):
    page = "trabajadores"

    def on_start(self):
        self.login()

        data = self.client.get(
            "/",
            params={"page": self.page, "action": "query", "format": "json"},
        ).json()

        self.ids = [item["cedula"] for item in data]

    @task(10)
    def view(self):
        self.client.get(
            "/",
            params={"page": self.page, "action": "query"},
        )

    @task(2)
    def insert(self):
        payload = make_payload()

        self.client.post(
            "/?page=trabajadores&action=insert",
            data=payload,
        )

    @task(1)
    def update(self):
        if not self.ids:
            return

        cedula = random.choice(self.ids)
        payload = make_payload()
        payload["cedula"] = cedula

        self.client.post(
            "/",
            params={
                "page": self.page,
                "action": "update",
                "id": cedula,
            },
            data=payload,
        )

    @task(1)
    def delete(self):
        if not self.ids:
            return

        cedula = self.ids.pop()
        self.client.post(
            "/",
            params={
                "page": self.page,
                "action": "delete",
                "id": cedula,
            },
        )
