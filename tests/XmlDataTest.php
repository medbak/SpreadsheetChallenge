<?php

namespace App\Tests;
use App\Repository\VerifyRepository;
use App\Repository\XmlRepository;
use Exception;
use PHPUnit\Framework\TestCase;

class XmlDataTest extends TestCase
{
    public function testGetData()
    {
        $verify = new VerifyRepository();
        $xmlRepo = new XmlRepository($verify);

        $result = $xmlRepo->extractDataFromXml(__DIR__.'/data/coffee_feed_mini.xml');
        $this->assertEquals(3, count($result));
    }

    public function testGetDataFromNotXmlFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File is not of type xml');
        $verify = new VerifyRepository();
        $xmlRepo = new XmlRepository($verify);
        $xmlRepo->extractDataFromXml(__DIR__.'/data/moldb.xml1');
    }

    public function testGetDataFromNotExistingFileInPath()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File does not exist in the location');
        $verify = new VerifyRepository();
        $xmlRepo = new XmlRepository($verify);
        $xmlRepo->extractDataFromXml(__DIR__.'/data/test.xml');
    }

    public function testGetDataFromNotExistingFileInUrl()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File does not exist in the location');
        $verify = new VerifyRepository();
        $xmlRepo = new XmlRepository($verify);
        $xmlRepo->extractDataFromXml('https://www.example.com/files/exemple.xml');
    }

    public function testGetDataFromEmptyFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('It is not possible to extract data from file '.__DIR__.'/data/coffee_feed_empty.xml');
        $verify = new VerifyRepository();
        $xmlRepo = new XmlRepository($verify);
        $xmlRepo->extractDataFromXml(__DIR__.'/data/coffee_feed_empty.xml');
    }
}