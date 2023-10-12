<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SellerPartyLegalEntity;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class SellerPartyLegalEntityTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Full Formal Seller Name LTD.</cbc:RegistrationName>
    <cbc:CompanyLegalForm>Share capital</cbc:CompanyLegalForm>
    <cbc:CompanyID schemeID="0002">987654321</cbc:CompanyID>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Full Formal Seller Name LTD.</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_COMPANY_ID = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Full Formal Seller Name LTD.</cbc:RegistrationName>
    <cbc:CompanyID schemeID="0002">987654321</cbc:CompanyID>
    <cbc:CompanyID schemeID="0002">987654321</cbc:CompanyID>
    <cbc:CompanyLegalForm>Share capital</cbc:CompanyLegalForm>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Full Formal Seller Name LTD.</cbc:RegistrationName>
  </cac:PartyLegalEntity>
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Full Formal Seller Name LTD.</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerPartyLegalEntity::class, $ublObject);
        $this->assertInstanceOf(LegalRegistrationIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals("Full Formal Seller Name LTD.", $ublObject->getRegistrationName());
        $this->assertEquals("Share capital", $ublObject->getCompanyLegalForm());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerPartyLegalEntity::class, $ublObject);
        $this->assertNull($ublObject->getIdentifier());
        $this->assertEquals("Full Formal Seller Name LTD.", $ublObject->getRegistrationName());
        $this->assertNull($ublObject->getCompanyLegalForm());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyCompanyId(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_COMPANY_ID);
        SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = SellerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}