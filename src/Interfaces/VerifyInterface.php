<?php

namespace App\Interfaces;

interface VerifyInterface
{
    public function verifyFileType($urlOrPath, $type, &$message = null);

    public function verifyFileExistence($urlOrPath, &$message = null);
}
