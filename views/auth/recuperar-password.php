<h1 class="nombre-pagina">Reestablecer Contraseña</h1>
<p class="descripcion-pagina">Ingresa tu nueva contraseña en el siguiente formulario.</p>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<?php if($error) return; ?>

<form method="post" class="formulario">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Ingresa tu nueva contraseña.">
    </div>
    <input type="submit" value="Cambiar Contraseña" class="boton">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Inicia sesión</a>
    <a href="/crear-cuenta"> ¿Aún no tienes una cuenta? Crear una</a>
</div>