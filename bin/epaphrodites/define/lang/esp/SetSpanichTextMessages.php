<?php

namespace Epaphrodites\epaphrodites\define\lang\esp;

class SetSpanichTextMessages
{
    private $AllMessages;

    public function SwithAnswers($MessageCode)
    {

        $this->AllMessages[] =
        [
            'language' => 'spanish',
            '403-title' => 'ERROR 403',
            '404-title' => 'ERROR 404',
            '419-title' => 'ERROR 419',
            '500-title' => 'ERROR 500',
            'session_name' => _SESSION_,
            'token_name' => _CRSF_TOKEN_,
            '403' => "¡Acceso restringido!!!",
            '419' => "¡Tu sesión ha expirado!",
            'author' => 'Epaphrodites comunidad',
            'description' => 'epaphrodite agency',
            '500' => 'Error interno del servidor',
            'denie' => "¡Procesamiento no posible!",
            'site-title' => 'INICIO | EPAPHRODITES',
            'mdpnotsame' => "Contraseña incorrecta",
            'error_text' => 'txt error epaphrodites',
            'back' => "Volver a la página de inicio",
            '404' => "¡Oops! No se encontró la página",
            'connexion' => "Por favor, vuelve a conectar",
            'version' => 'EPAPHRODITES V0.01 (PHP 8.4 + PYTHON 3 + C)',
            'succes' => "Procesamiento completado exitosamente",
            'user-exist' => 'Lo siento, este usuario ya existe.',
            'noformat' => "El formato del archivo es incorrecto",
            'done' => "¡Felicidades, tu registro ha sido exitoso!",
            'fileempty' => "¡No se ha seleccionado ningún archivo!",
            'no-identic' => "Lo siento, las contraseñas no coinciden",
            'login-wrong' => "Inicio de sesión o contraseña incorrectos",
            'vide' => "Por favor, complete todos los campos correctamente!!!",
            'file-header' => 'Compruebe la cabecera de su archivo, por favor.',
            'send' => "¡Enhorabuena, tu mensaje ha sido enviado exitosamente!",
            'false-mail' => 'Lo siento, este correo electrónico es incorrecto.',
            'error' => "Lo siento, se produjo un error durante el procesamiento.",
            'rightexist' => "Lo siento, este derecho ya existe para este usuario",
            'erreur' => "Lo siento, ocurrió un problema durante el procesamiento",
            'no-data' => "Lo siento, no hay información que coincida con tu solicitud",
            'errorsending' => "Lo siento, se produjo un problema al enviar tu mensaje",
            'tailleauto' => "El tamaño del archivo supera el límite permitido de 500 KB",
            'denie_action' => "¡Procesamiento no posible! No tienes autorización para realizar esta acción",
            'keywords' => "Epaphrodites framework, Creación; sitio web; digital; community manager; logo; identidad visual; marketing; comunicación;",
        ];

        return isset($this->AllMessages[0][$MessageCode]) ? $this->AllMessages[0][$MessageCode] : "";
    }
}
