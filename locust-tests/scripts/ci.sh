locust -f locustfile.py \
    --headless \                            # Ejecutar sin interfaz grafica
    -u 500 \                                # Usuarios concurrentes     
    -r 50 \                                 # Usuarios por segundo  
    -t 5m \                                 # Duracion
    --csv=resultados \                      # Exportar a CSV
    --html=reporte.html \                   # Exportar HTML
    --exit-code-on-error 1                  # Abortar pruebas en errores