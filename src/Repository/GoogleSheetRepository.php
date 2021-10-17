<?php

namespace App\Repository;

use App\Interfaces\GoogleSheetInterface;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;

class GoogleSheetRepository implements GoogleSheetInterface
{
    public const GOOGLESCOPES = [
        'https://www.googleapis.com/auth/spreadsheets',
    ];

    public function createSpreadsheet($name, $googleClient)
    {
        $this->addGoogleScopes($googleClient);

        $service = new Google_Service_Sheets($googleClient);

        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => $name,
            ],
        ]);
        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId',
        ]);

        return $spreadsheet->spreadsheetId;
    }

    public function insertDataToSpreadsheet($id, $values, $googleClient)
    {
        $this->addGoogleScopes($googleClient);

        $service = new Google_Service_Sheets($googleClient);

        // Object - range of values
        $ValueRange = new Google_Service_Sheets_ValueRange();
        // Specify the direction of insertion - by default ROWS
        // we can choose COLUMNS
        $ValueRange->setMajorDimension('ROWS');
        // Setting our data
        $ValueRange->setValues($values);
        // We specify in the options to process user data
        $options = ['valueInputOption' => 'USER_ENTERED'];
        // We make a request indicating in the second parameter the name of the sheet and the starting cell to fill
        //It is needed to point to next line
        $range = 'Sheet1!A1';
        $service->spreadsheets_values->update($id, $range, $ValueRange, $options);

        return true;
    }

    public function getUrlSpreadsheet($id, $googleClient)
    {
        $this->addGoogleScopes($googleClient);

        $service = new Google_Service_Sheets($googleClient);

        $response = $service->spreadsheets->get($id);

        return $response->spreadsheetUrl;
    }

    public function givePermission($id, $email, $googleClient)
    {
        $this->addGoogleScopes($googleClient);

        /*
         * give access to someone
         */
        // Object - drive
        $Drive = new Google_Service_Drive($googleClient);
        // Object - permission drive
        $DrivePermisson = new Google_Service_Drive_Permission();
        // Type permission
        $DrivePermisson->setType('user');
        // You email
        $DrivePermisson->setEmailAddress($email);
        // Role
        $DrivePermisson->setRole('writer');
        // Send request with you spreadsheetId
        $response = $Drive->permissions->create($id, $DrivePermisson);
    }

    private function addGoogleScopes(&$googleClient)
    {
        // Adding an access area for reading, editing, creating and deleting tables
        $googleClient->addScope(GoogleSheetRepository::GOOGLESCOPES);
    }
}
