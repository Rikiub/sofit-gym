<?php
// Formulario compartido para clientes y trabajadores. 
?>

<fieldset class="row">
    <label class="col form-label">Cédula
        <input
            class="form-control"
            required
            name="cedula"
            type="text"
            pattern="^[V]-\d{8}$"
            x-mask="V-99999999"
            @input.debounce.500ms="validateCedula($el)">
        <small class="form-text" x-text="errors.cedula"></small>
    </label>

    <label class="col form-label">Nombre
        <input class="form-control" required name="nombre" type="text" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.nombre"></small>
    </label>

    <label class="col form-label">Apellido
        <input class="form-control" required name="apellido" type="text" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.apellido"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col form-label">Teléfono
        <input
            class="form-control"
            required
            name="telefono"
            type="tel"
            x-mask="9999-9999999"
            pattern="04(12|14|16|24|26)-\d{7}"
            @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.telefono"></small>
    </label>

    <label class="col form-label">Correo
        <input class="form-control" required name="correo" type="email" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.correo"></small>
    </label>

    <label class="col form-label">Dirección
        <input class="form-control" name="direccion" type="text" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.direccion"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col form-label">Fecha de nacimiento
        <input class="form-control" required name="fecha_nacimiento" type="date" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors.fecha_nacimiento"></small>
    </label>
</fieldset>