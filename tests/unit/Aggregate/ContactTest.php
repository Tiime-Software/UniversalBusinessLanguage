<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Contact;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class ContactTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Name>Contact Fournisseur 2</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
</Invoice>
XML;
    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = Contact::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Contact::class, $ublObject);
        $this->assertEquals('Contact Fournisseur', $ublObject->getName());
        $this->assertEquals('01 02 03 04 05', $ublObject->getTelephone());
        $this->assertEquals('contact@vendeur.com', $ublObject->getElectronicMail());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = Contact::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromOmittedLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        Contact::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleAddresses(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Contact::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = Contact::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
