<?php

namespace Model;

class Usuario extends ActiveRecord
{
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta()
    {
        $regexEmail = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $regexTelefono = '/^(?:\d{7}|(?:3[0-5]\d{8})|(?:60[1-9]\d{7}))$/';


        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio.';
        }
        if (!$this->apellido) {
            self::$alertas['error'][] = 'El apellido es obligatorio.';
        }
        if (!$this->telefono) {
            self::$alertas['error'][] = 'El número de teléfono es obligatorio.';
        }
        if (!preg_match($regexTelefono, $this->telefono)) {
            self::$alertas['error'][] = 'Debe introducir un número de teléfono válido.';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio.';
        }

        if (!preg_match($regexEmail, $this->email)) {
            self::$alertas['error'][] = 'Debe introducir una dirección de email válida.';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio.';
        }

        if (strlen($this->password) < 8) {
            self::$alertas['error'][] = 'El password debe contener al menos 8 caracteres.';
        }


        return self::$alertas;
    }

    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = '¡El email es obligatorio!';
        }
        if (!$this->password) {
            self::$alertas['error'][] = '¡El password es obligatorio!';
        }

        return self::$alertas;
    }

    public function validarEmail()
    {
        $regexEmail = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio.';
        }

        if (!preg_match($regexEmail, $this->email)) {
            self::$alertas['error'][] = 'Debe introducir una dirección de email válida.';
        }

        return self::$alertas;
    }

    public function valdiarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = "El password es obligatorio.";
        }
        if (strlen($this->password)) {
            self::$alertas['error'][] = "El password debe tener al menos 8 caracteres.";
        }

        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario()
    {
        $sql = "SELECT * FROM " . self::$tabla . " WHERE email =  '" . $this->email . "' LIMIT 1";

        $resultado  = self::$db->query($sql);

        if ($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya está registrado.';
        }

        return $resultado;
    }

    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken()
    {
        $this->token = uniqid();
    }

    public function comprobarPasswordVerificado($password)
    {
        $resultado = password_verify($password, $this->password);
        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password incorrecto o tu cuenta no ha sido confirmada.';
        } else {
            return true;
        }
    }
}
