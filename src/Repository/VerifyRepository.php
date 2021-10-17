<?php

namespace App\Repository;

use App\Interfaces\VerifyInterface;

class VerifyRepository implements VerifyInterface
{
    /**
     * @param $urlOrPath
     * @param $type
     * @param null $message
     *
     * @return bool
     */
    public function verifyFileType($urlOrPath, $type, &$message = null)
    {
        $ext = pathinfo($urlOrPath, PATHINFO_EXTENSION);
        if ($result = ($ext !== $type)) {
            $message = 'File is not of type '.$type;
        }

        return !$result;
    }

    public function verifyFileExistence($urlOrPath, &$message = null)
    {
        $check = @fopen($urlOrPath, 'r');
        if (!$check) {
            $message = 'File does not exist in the location '.$urlOrPath;
        }

        return $check;
    }
}
