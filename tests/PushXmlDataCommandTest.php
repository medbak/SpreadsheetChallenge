<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PushXmlDataCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('push:xml-data-to-google-spreadsheet');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
            'arg1' => __DIR__.'/data/coffee_feed_mini.xml',
            'arg2' => 'path_to_key.json',
            'arg3' => 'test command',

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        //this will return error because the json credentials not provided
        $this->assertStringContainsString('An error occurred, please check the log', $output);

    }
}