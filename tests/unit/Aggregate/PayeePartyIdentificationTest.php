<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\PayeeIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeePartyIdentification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PayeePartyIdentificationTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
  </cac:PartyIdentification>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID>FR932874294</cbc:ID>
  </cac:PartyIdentification>
</Invoice>
XML;

    protected const XML_INVALID_BAD_SCHEME_ID = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="1234">FR932874294</cbc:ID>
  </cac:PartyIdentification>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
    <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
  </cac:PartyIdentification>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
  </cac:PartyIdentification>
  <cac:PartyIdentification>
    <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
  </cac:PartyIdentification>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = PayeePartyIdentification::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeIdentifier::class, $ublObject);
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = PayeePartyIdentification::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeIdentifier::class, $ublObject->getBuyerIdentifier());
    }

    public function testCannotBeCreatedFromBadSchemeId(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_BAD_SCHEME_ID);
        PayeePartyIdentification::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        PayeePartyIdentification::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        PayeePartyIdentification::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = PayeePartyIdentification::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
