from locust import HttpUser, between, events


@events.request.add_listener
def _(response=None, exception=None, **kw):
    """Mostrar errores en la consola"""
    if exception:
        print(f"[ERR] {exception}")
    elif response and response.status_code >= 400:
        print(f"[{response.status_code}] {response.text[:200]}")


class SofitGymUser(HttpUser):
    """Configuracion principal de las tareas.

    NO debe usarse directamente, en cambio, tiene que ser heredado por otras clases.
    Esto usualmente iria en `locustfile.py` pero se separa para mayor extensibilidad.
    """

    host = "http://localhost/sofit-gym"
    wait_time = between(1, 5)

    def on_start(self):
        self.login()

    def login(self):
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
