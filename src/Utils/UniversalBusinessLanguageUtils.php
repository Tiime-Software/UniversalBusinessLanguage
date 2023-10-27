<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Utils;

class UniversalBusinessLanguageUtils
{
    public const UBL_DATE_FORMAT = 'Y-m-d';

    public const XSD_PATH = __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'xsd' . \DIRECTORY_SEPARATOR . 'maindoc' . \DIRECTORY_SEPARATOR . 'UBL-Invoice-2.1.xsd';

    public static function validateXSD(\DOMDocument $xml): bool|array
    {
        libxml_use_internal_errors(true);

        if (!$xml->schemaValidate(self::XSD_PATH)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();

            return $errors;
        }

        return true;
    }
}
