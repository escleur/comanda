<?php



namespace Components;

class GenericResponse
{
    public $correcto;
    public $message;
    public $content;

    function __construct($correcto, $message = '', $content = '')
    {
        $this->correcto = $correcto;
        $this->message = $message;
        $this->content = $content;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /* Used to return a new Generic respoonse for the client */
    public static function obtain($correcto, $message = '', $content = '')
    {
        $text = json_encode(new GenericResponse($correcto, $message, $content), JSON_PRETTY_PRINT);
        return $text;
    }
}
