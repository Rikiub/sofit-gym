locust -f locustfile.py `
    --headless `
    -u 50 `
    -r 2 `
    -t 20s `
    --csv=resultados `
    --html=reporte.html `
    --exit-code-on-error 1