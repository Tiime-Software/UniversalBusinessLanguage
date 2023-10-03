<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AddressLine;

class AddressLineTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:AddressLine>
        <cbc:Line>Ruelle du représentant fiscal</cbc:Line>
    </cac:AddressLine>
</Invoice>
XMLCONTENT;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_NO_LINE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:AddressLine>
    </cac:AddressLine>
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_MULTIPLE_ADDRESSLINES = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:AddressLine>
        <cbc:Line>Ruelle du représentant administratif</cbc:Line>
    </cac:AddressLine>
    <cac:AddressLine>
        <cbc:Line>Ruelle du représentant fiscal</cbc:Line>
    </cac:AddressLine>
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_MULTIPLE_LINES = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:AddressLine>
        <cbc:Line>Ruelle du représentant administratif</cbc:Line>
        <cbc:Line>Ruelle du représentant fiscal</cbc:Line>
    </cac:AddressLine>
</Invoice>
XMLCONTENT;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = AddressLine::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(AddressLine::class, $ublObject);
        $this->assertEquals("Ruelle du représentant fiscal", $ublObject->getLine());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = AddressLine::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromOmittedLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        AddressLine::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleAddresses(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_ADDRESSLINES);
        AddressLine::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_LINES);
        AddressLine::fromXML($this->xpath, $currentElement);
    }
}