# Sofit Gym

## Instrucciones XAMPP

Colocar la carpeta `sofit-gym` dentro de `htdocs/`. Luego ingresar a traves de `http://localhost/sofit-gym`.

## Estructura de archivos

- `app/`
    - `Controllers/`
        - Gestionan las peticiones HTTP.
    - `Models/`
        - Gestionan el almacenamiento y persistencia en la base de datos.
    - `views/`
        - Colección de plantillas HTML cargadas por los controladores.
    - `Services/`
        - Clases especializadas y reutilizables.
    - `Core/`
        - Colección de helpers y abstracciones principales de la aplicación.

---

- `config/`
    - Scripts que devuelven un array para configurar el estado de la aplicación.
- `bootstrap/`
    - `app.php`
        - Script encargado de preparar la configuración inicial del sistema.
        - Pensado para usarse tanto en el `FrontController` como en scripts de PHP.
- `scripts/`
    - Colección de scripts a ejecutar periodicamente en el servidor.

---

- `assets/`
    - Recursos web cargados por las vistas.
- `uploads/`
    - Recursos web subidos por los usuarios para cargar en la vistas.
- `database/`
    - Almacenamiento de respaldos de la base datos.