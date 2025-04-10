<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use MongoDB\Client;

class PhoneNumberController extends Controller
{
    /**
     * @var Client
     */
    protected Client $mongoClient;

    /**
     * @var PhoneNumberUtil
     */
    protected PhoneNumberUtil $phoneNumberUtil;


    public function __construct()
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Summary of validateAndStore
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function validateAndStore(Request $request)
    {
        $this->init();

        try {
            $phoneNumbers = $request->input('phone_numbers');
            if (!$phoneNumbers || !is_array($phoneNumbers)) {
                return response()->json(['error' => 'A list of phone numbers is required.'], 400);
            }

            $results = [];
            $validCount = 0;
            $collection = $this->mongoClient->phone_numbers->numbers;

            foreach ($phoneNumbers as $phoneNumber) {
                $existingRecord = $collection->findOne(['phone_number' => $phoneNumber]);

                try {
                    $parsedNumber = $this->phoneNumberUtil->parse($phoneNumber, null);
                    $isValid = $this->phoneNumberUtil->isValidNumber($parsedNumber);
                    $countryCode = $parsedNumber->getCountryCode();
                    $numberType = $this->phoneNumberUtil->getNumberType($parsedNumber);

                    if ($existingRecord) {
                        $results[] = [
                            'phone_number' => $existingRecord['phone_number'],
                            'country_code' => $existingRecord['country_code'],
                            'valid' => $existingRecord['valid'],
                            'number_type' => $existingRecord['number_type'],
                            'from_cache' => true,
                        ];

                        if ($existingRecord['valid']) {
                            $validCount++;
                        }

                        continue;
                    }

                    $results[] = [
                        'phone_number' => $phoneNumber,
                        'country_code' => $countryCode,
                        'valid' => $isValid,
                        'number_type' => $numberType,
                        'from_cache' => false,
                    ];

                    if ($isValid) {
                        $validCount++;
                        $collection->insertOne([
                            'phone_number' => $phoneNumber,
                            'country_code' => $countryCode,
                            'valid' => true,
                            'number_type' => $numberType,
                        ]);
                    }
                } catch (NumberParseException $e) {
                    $results[] = ['phone_number' => $phoneNumber, 'error' => 'Invalid phone number format.'];
                }
            }

            $validPercentage = count($phoneNumbers) > 0 ? ($validCount / count($phoneNumbers)) * 100 : 0;

            return response()->json([
                'valid_count' => $validCount,
                'valid_percentage' => $validPercentage,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            \Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @return void
     */
    private function init()
    {
        $mongoHost = env('DB_HOST', '127.0.0.1');
        $mongoPort = env('DB_PORT', 27017);
        $mongoDatabase = env('DB_DATABASE', 'phone_numbers');
        $mongoUsername = env('DB_USERNAME');
        $mongoPassword = env('DB_PASSWORD');

        $uri = "mongodb://";

        if ($mongoUsername && $mongoPassword) {
            $uri .= "$mongoUsername:$mongoPassword@";
        }

        $uri .= "$mongoHost:$mongoPort";

        $this->mongoClient = new Client($uri, ['db' => $mongoDatabase, 'ssl' => false]);
    }
}