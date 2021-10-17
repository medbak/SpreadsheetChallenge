# SpreadsheetChallenge


## Prepare Environment

1) install dependencies
   ```
   composer install
   ```

## Application
1) Push data of XML file to a Google Spreadsheet
   ```
   php bin/console push:xml-data-to-google-spreadsheet path_to_xml_file path_to_key.json name_of_spreadsheet
   
   Param 1 : link or path to xml file
   
   Param 2 : path to google credentials .json file (if not provided, it will look for service_key.json in the project's root
   
   Param 3 : name of spreadsheet (optional)
   ```
   
2) Help
   ```
   php bin/console push:xml-data-to-google-spreadsheet --help
   ```
   
3) Run Tests
   ```
   vendor/bin/phpunit tests/
   
   or
   
   php bin/phpunit tests/
   ```