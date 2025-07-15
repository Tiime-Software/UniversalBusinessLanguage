<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\Utils;

class UniversalBusinessLanguageUtils
{
    public const UBL_DATE_FORMAT = 'Y-m-d';

    public const XSD_PATH = __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'xsd' . \DIRECTORY_SEPARATOR . 'maindoc' . \DIRECTORY_SEPARATOR . 'UBL-CreditNote-2.1.xsd';

    /**
     * @return array<int, \LibXMLError>
     */
    public static function validateXSD(\DOMDocument $xml): array
    {
        $errors = [];

        libxml_use_internal_errors(true);

        if (!$xml->schemaValidate(self::XSD_PATH)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
        }

        libxml_use_internal_errors(false);

        return $errors;
    }
}
