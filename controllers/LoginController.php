<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $router->render('auth/login');
    }

    public static function logout()
    {
        echo "Desde Logout";
    }

    public static function olvide(Router $router)
    {
        $router->render('auth/olvide-password');
    }

    public static function recuperar()
    {
        echo "Desde Recuperar";
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario;
        // Alertas Vacias
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas este vacío
            if (empty($alertas)) {
                // Verificar si el usuario no esta registrado
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear Password
                    $usuario->hashPassword();

                    // Generar token unico
                    $usuario->crearToken();

                    // Enviar un email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    // Crear el usuario

                    $resultado = $usuario->guardar();

                    // debuguear($usuario);

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valído.');
        } else {
            // Confirmar cuenta
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente.');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
