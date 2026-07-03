<?php
// Props
$codigo ??= null;
?>

<main style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
    <header style='text-align: center; margin-bottom: 20px;'>
        <h1 style='color: #d11a2a; font-family: Arial, sans-serif; font-size: 32px; margin: 0; text-transform: uppercase; letter-spacing: 2px;'>
            SOFIT GYM
        </h1>
    </header>

    <section>
        <hgroup>
            <h2 style='color: #333;'>Recuperación de contraseña</h2>
            <p>Hola, hemos recibido una solicitud para recuperar el acceso a tu cuenta en <strong>Sofit Gym</strong>.</p>
        </hgroup>
    </section>

    <section style='background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
        <p style='margin: 0; font-size: 16px;'>Tu código de verificación es:</p>
        <h1 style='color: #e67e22; font-size: 32px; margin: 10px 0;'><?= $codigo ?></h1>
    </section>

    <section>
        <p style='font-size: 12px; color: #777;'>Este código expirará en 15 minutos. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
    </section>

    <hr style='border: 0; border-top: 1px solid #eee;'>

    <footer>
        <p style='font-size: 12px; color: #999; text-align: center;'>Sofit Gym - Sistema de Gestión Interna</p>
    </footer>
</main>