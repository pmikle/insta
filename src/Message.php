<?php

namespace App;

class Message
{
    const FILE_ID_LAST_MESSAGE = 'idLastMessage.txt';

    private $messages = [];
    private $idLastMessage;
    private $fileLastMessage;

    public function __construct()
    {
        $this->fileLastMessage = __DIR__ . '/../' . Config::$cacheFolder . '/' . self::FILE_ID_LAST_MESSAGE;
        $this->messages = include_once __DIR__ . "/../static/messages.php";
        $this->idLastMessage = $this->getIdLastMessage();
    }

    public function getMessage(): string
    {
        $msg = $this->messages[$this->idLastMessage + 1] ?? '';
        return mb_convert_encoding($msg, 'utf-8', mb_detect_encoding($msg));
    }

    private function getIdLastMessage()
    {
        if (!is_readable($this->fileLastMessage)) {
            return -1;
        }
        return (int)file_get_contents($this->fileLastMessage);
    }

    public function updateIdLastMessage()
    {
        return file_put_contents($this->fileLastMessage, ++$this->idLastMessage);
    }
}