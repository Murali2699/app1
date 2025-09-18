<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once('/opt/lampp/htdocs/payment_automation/api/payment/helper/read_database.php');
require '/opt/lampp/htdocs/payment_automation/api/payment/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


try {
    // Connect to MySQL database
    
    $read_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query data from MySQL table
    $stmt = $read_db->query("SELECT * FROM fn_payment_getbankdetails();");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // print_r($data);exit;
    
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Add data to the Excel file
    
    
    // Set headers for the Excel file
    $headers = array_keys($data[0]);
    $sheet->fromArray($headers, null, 'A1');
    $sheet->fromArray($data, null, 'A2');
    
    // Save the Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save('/opt/lampp/htdocs/payment_automation/api/payment/api/v1/Export_Bank_IFSC_Details.xlsx');
    
    echo "Excel file created successfully!";
} catch (PDOException $e) {
    // Handle database connection error
    echo "Connection failed: " . $e->getMessage();
} catch (Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}