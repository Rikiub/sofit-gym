from locust import HttpUser, between


class SofitGymUser(HttpUser):
    """Configuracion principal de las tareas.

    NO debe usarse directamente, en cambio, tiene que ser heredado por otras clases.
    Esto usualmente iria en `locustfile.py` pero se separa para mayor extensibilidad.
    """

    host = "http://localhost/sofit-gym"
    wait_time = between(1, 5)

    def on_start(self):
        NOMRE_USUARIO = "admin"
        CONTRSASENA = "admin"

        # Iniciamos sesion para interactuar con el sistema
        self.client.post(
            "/?page=login&action=login",
            json={
                "nombre_usuario": NOMRE_USUARIO,
                "contrasena": CONTRSASENA,
            },
        )
