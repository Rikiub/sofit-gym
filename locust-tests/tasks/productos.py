import random
import string

from locust import task

from tasks.base import SofitGymUser


class ProductosUser(SofitGymUser):
    """Productos module tasks. Inherits host, wait_time, and login."""

    @task(4)
    def view_products_page(self):
        # Simulates loading the main index view of products via GET request
        self.client.get(
            "/?page=productos",
            name="/productos (Index)",
        )

    @task(3)
    def search_products_ajax(self):
        # Simulates dynamic product searches with randomized search terms
        search_terms = ["creatina", "proteina", "agua", "barra", "snack"]
        term = random.choice(search_terms)
        self.client.get(
            f"/?page=productos&action=buscarAjax&ajax=buscar_productos&termino={term}",
            name="/productos (Buscar AJAX)",
        )

    @task(1)
    def create_product(self):
        # Random code to avoid duplicate key errors during concurrent testing
        random_suffix = "".join(
            random.choices(string.ascii_uppercase + string.digits, k=5)
        )
        codigo = f"TST{random_suffix}"

        payload = {
            "codigo_producto": codigo,
            "nombre": f"Articulo Test {random_suffix}",
            "id_categoria": str(random.randint(1, 5)),
            "precio_venta": str(round(random.uniform(10.0, 100.0), 2)),
            "stock_minimo": "5",
            "stock_actual": "25",
            "id_unidad": "1",
        }

        self.client.post(
            "/?page=productos&action=crear",
            data=payload,
            name="/productos (Crear)",
        )

    @task(2)
    def update_stock(self):
        # Sample product codes with randomized stock increments/decrements
        sample_codes = ["ZAR-0012", "55125", "AGDAYU232"]
        codigo = random.choice(sample_codes)
        cantidad = random.choice([5, -2, 10, -5])

        payload = {"codigo_producto": codigo, "cantidad": str(cantidad)}

        self.client.post(
            "/?page=productos&action=actualizarStock",
            data=payload,
            name="/productos (Actualizar Stock)",
        )

    @task(1)
    def increase_supplement_prices(self):
        # Trigger bulk price update for category 1 (supplements)
        self.client.post(
            "/?page=productos&action=aumentarPreciosSuplementos",
            name="/productos (Aumentar Precios Suplementos)",
        )

    @task(1)
    def edit_product(self):
        # Simulates editing an existing product's details
        sample_codes = ["ZAR-0012", "55125", "AGDAYU232"]
        codigo = random.choice(sample_codes)

        payload = {
            "codigo_producto": codigo,
            "nombre": f"Articulo Editado {random.randint(10, 99)}",
            "precio_venta": str(round(random.uniform(15.0, 150.0), 2)),
            "stock_minimo": "5",
        }

        self.client.post(
            "/?page=productos&action=editar",
            data=payload,
            name="/productos (Editar)",
        )

    @task(1)
    def delete_product(self):
        # Logical deletion/deactivation of a product
        sample_codes = ["ZAR-0012", "55125", "AGDAYU232"]
        codigo = random.choice(sample_codes)

        payload = {"codigo_producto": codigo, "fisico": "false"}

        self.client.post(
            "/?page=productos&action=eliminar",
            data=payload,
            name="/productos (Eliminar Lógico)",
        )
