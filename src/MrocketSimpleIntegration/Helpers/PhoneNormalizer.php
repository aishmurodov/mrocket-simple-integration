<?php

namespace Aishmurodov\MrocketSimpleIntegration\Helpers;

use Aishmurodov\MrocketSimpleIntegration\Interfaces\PhoneNormalizerInterface;

class PhoneNormalizer implements PhoneNormalizerInterface {
    public static function normalizePhone (string $phone): string
    {
        $phoneNumber = preg_replace('/[^0-9]/','', $phone);

        if(strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+'.$countryCode.$areaCode.$nextThree.$lastFour;
        }
        else if(strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = $areaCode.$nextThree.$lastFour;
        }
        else if(strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree.$lastFour;
        }

        if ($phoneNumber[0] !== "+") {
            $phoneNumber = "+" . $phoneNumber;
        }

        if ($phoneNumber[1] == "8") {
            $phoneNumber[1] = "7";
        } else if ($phoneNumber[1] != '7'){
            $phoneNumber[0] = "7";
            $phoneNumber = "+" . $phoneNumber;
        }

        return $phoneNumber;
    }
}