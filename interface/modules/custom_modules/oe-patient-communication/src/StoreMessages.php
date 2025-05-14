<?php

namespace Juggernaut\Module\BulkEmail;

class StoreMessages
{
    public $subject;
    public $message;
    public $date;
    public $time;
    public $user;
    public function __construct()
    {
        $this->storeMessages();
    }

    private function storeMessages()
    {
        $sql = "INSERT INTO `module_mass_email` (`id`, `subject`, `message`, `date`, `time`, `user`)
        VALUES (NULL, ?, ?, ?, ?, ?)";
        try {
            sqlStatement($sql, [
                $this->subject,
                $this->message,
                $this->date,
                $this->time,
                $this->user
            ]);
        } catch (\Exception $e) {
            return xlt("Message could not be stored");
        }
        return xlt("Message stored");
    }
}
