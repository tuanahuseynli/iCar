<?php
require_once "storage.php";
class UserRepository extends Storage
{
    public function __construct()
    {
        $io = new JsonIO('./media/users.json'); // Ensure file path is correct
        parent::__construct($io);
    }
}