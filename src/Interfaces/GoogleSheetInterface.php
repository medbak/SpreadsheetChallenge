<?php

namespace App\Interfaces;

interface GoogleSheetInterface
{
    public function createSpreadsheet($name, $googleClient);

    public function insertDataToSpreadsheet($id, $values, $googleClient);

    public function getUrlSpreadsheet($id, $googleClient);

    public function givePermission($id, $email, $googleClient);
}
