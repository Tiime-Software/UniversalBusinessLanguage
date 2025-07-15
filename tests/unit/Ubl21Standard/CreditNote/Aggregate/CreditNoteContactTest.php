<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\Contact;

class CreditNoteContactTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Contact>
    <cbc:Name>Contact Fournisseur</cbc:Name>
    <cbc:Name>Contact Fournisseur 2</cbc:Name>
    <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
    <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
  </cac:Contact>
</CreditNote>
XML;
    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
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
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
