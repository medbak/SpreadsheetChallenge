<?php

namespace App\Command;

use App\Interfaces\GoogleConnectionInterface;
use App\Interfaces\GoogleSheetInterface;
use App\Interfaces\XmlInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushXmlDataToGoogleSpreadsheetCommand extends Command
{
    protected static $defaultName = 'push:xml-data-to-google-spreadsheet';
    protected static $defaultDescription = 'Extract data from xml file and push it to google sheet';

    /**
     * @var SymfonyStyle
     */
    private $io;
    private $xml;
    private $googleConnect;
    private $googleSheet;
    private $logger;

    public function __construct(GoogleConnectionInterface $googleConnect, GoogleSheetInterface $googleSheet, XmlInterface $xml, LoggerInterface $logger)
    {
        parent::__construct();
        $this->googleSheet = $googleSheet;
        $this->xml = $xml;
        $this->logger = $logger;
        $this->googleConnect = $googleConnect;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Extract data from xml file and push it to google sheet')
            ->setHelp($this->getCommandHelp())
            ->addArgument('arg1', InputArgument::REQUIRED, 'Path of Xml file')
            ->addArgument('arg2', InputArgument::OPTIONAL, "Path of credentials, by default service_key.json in project's root")
            ->addArgument('arg3', InputArgument::OPTIONAL, 'Name of spreadsheet')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('arg1')
            && null !== $input->getArgument('arg2')
            && null !== $input->getArgument('arg3')
        ) {
            return;
        }

        $questions = [];
        $this->io->title('Extract data from xml file and push it to google sheet');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console push:xml-data-to-google-spreadsheet path_to_xml_file path_to_key.json name_of_spreadsheet',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        if (!$input->getArgument('arg1')) {
            $question = new Question("\n Please give path/link to xml file: ");
            $question->setValidator(function ($pathXml) {
                if (empty($pathXml)) {
                    throw new \Exception('Path/link can not be empty');
                }

                return $pathXml;
            });
            $questions['arg1'] = $question;
        }

        if (!$input->getArgument('arg2')) {
            $question = new Question("\n Please give path to key, else we will use look for service_key.json in project's root: ");

            $questions['arg2'] = $question;
        }

        if (!$input->getArgument('arg3')) {
            $question = new Question("\n Please give name for spreadsheet, else the default (new spreadsheet) will be used: ");

            $questions['arg3'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            //get path of xml file
            $path = $input->getArgument('arg1');

            //get path of credentials json file
            $pathSec = $input->getArgument('arg2');
            $pathSecWithoutSpace = str_replace(' ', '', $pathSec);

            if ('' === $pathSecWithoutSpace) {
                $pathSec = __DIR__.'/../../service_key.json';
            }

            //get the name of spreadsheet
            $name = $input->getArgument('arg3');
            $nameWithoutSpace = str_replace(' ', '', $name);
            if ('' === $nameWithoutSpace) {
                $name = 'new spreadsheet';
            }

            $googleClient = $this->googleConnect->connect($pathSec);

            $data = $this->xml->extractDataFromXml($path);

            $id = $this->googleSheet->createSpreadsheet($name, $googleClient);

            $this->googleSheet->insertDataToSpreadsheet($id, $data, $googleClient);

            $url = $this->googleSheet->getUrlSpreadsheet($id, $googleClient);

            $this->io->success(sprintf('Spreadsheet has been created, url : "%s" ', $url));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logger->info($e);
            $this->io->error(sprintf('"%s" ', 'An error occurred, please check the log'));

            return Command::FAILURE;
        }
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command gets data from given xml and then creates google spreadsheet 
and pushes the data to it:
  <info>php %command.full_name%</info> <comment>path_to_xml_file path_to_key.json name_of_spreadsheet</comment>

First, the command will look for the xml file and verify its existence and type, to extract data from it.
Then, it will look for path_to_key.json for google credentials, if no path provided it will look for service_key.json in the project's root.
Finally, it will ask for a name to the new spreadsheet that gonna be created, if no name provided it is gonna use new spreadsheet.

If you omit any of the three required arguments, the command will ask you to
provide the missing values:

  # command will ask you for the name
  <info>php %command.full_name%</info> <comment>path_to_xml_file path_to_key.json</comment>
  
  # command will ask you for the path_to_key.json (even if it's in the project's root) and name_of_spreadsheet
  <info>php %command.full_name%</info> <comment>path_to_xml_file</comment>
  
  # command will ask you for all arguments
  <info>php %command.full_name%</info>

HELP;
    }
}
