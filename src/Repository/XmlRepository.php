<?php

namespace App\Repository;

use App\Interfaces\VerifyInterface;
use App\Interfaces\XmlInterface;

class XmlRepository implements XmlInterface
{
    private $verify;

    public function __construct(VerifyInterface $verify)
    {
        $this->verify = $verify;
    }

    public function extractDataFromXml($pathOrLink)
    {
        $message = '';

        if (!$this->verify->verifyFileExistence($pathOrLink, $message) or !$this->verify->verifyFileType($pathOrLink, 'xml', $message)) {
            throw new \Exception($message);
        }

        $fileContents = file_get_contents($pathOrLink);

        if (false === $fileContents or empty($fileContents)) {
            throw new \Exception('It is not possible to extract data from file '.$pathOrLink);
        }

        $fileContents = str_replace(["\n", "\r", "\t"], '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $xml = simplexml_load_string($fileContents, null, LIBXML_NOCDATA);

        //get the keys
        $keys = array_keys(get_object_vars($xml->children()[0]));

        $values = [
                $keys,
            ];

        foreach ($xml->children() as $books) {
            $a = get_object_vars($books);
            $c = array_values($a);
            $c = str_replace(["\n", "\r", "\t"], '', $c);
            //replace empty object
            $c = array_map(function ($value) {
                return (empty($value) and 'object' == gettype($value)) ? '' : $value;
            }, $c);
            $values[] = $c;
        }

        return $values;
    }
}
