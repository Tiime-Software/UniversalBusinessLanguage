<?php

use \PHPUnit\Framework\TestCase;
use \Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;

class DueDateTest extends TestCase
{
    protected const XML_NODE = "cbc:DueDate";

    protected const XML_ROOT = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
</ns2:CreditNote>
XMLCONTENT;

    protected const XML_REFERENCE = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
  <cbc:DueDate>2023-01-02</cbc:DueDate>
</ns2:CreditNote>
XMLCONTENT;

    protected const XML_VALID_DATE = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
  <cbc:DueDate>2023-01-02</cbc:DueDate>
</ns2:CreditNote>
XMLCONTENT;
    protected const XML_INVALID_DATE = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
  <cbc:DueDate>201</cbc:DueDate>
</ns2:CreditNote>
XMLCONTENT;

    protected const XML_EMPTY_DATE = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
  <cbc:DueDate></cbc:DueDate>
</ns2:CreditNote>
XMLCONTENT;

    protected const XML_OMITTED_DATE = <<<XMLCONTENT
<ns2:CreditNote xmlns:ns2="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2" xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
</ns2:CreditNote>
XMLCONTENT;

    public function testCanBeCreatedFromValid(): void
    {
        $document = $this->loadXMLDocument(self::XML_VALID_DATE);

        $xpath = new \DOMXPath($document);
        $currentElements = $xpath->query(sprintf('//%s', "ns2:CreditNote"));
        if ($currentElements->count() > 0) {
            /** @var \DOMElement $currentElement */
            $currentElement = $currentElements->item(0);
            $ublObject = DueDate::fromXML($xpath, $currentElement);
            $this->assertEquals($ublObject->getDateTimeString(), new \DateTime('2023-01-02'));
        } else {
            $this->fail("Test source is not valid");
        }
    }

    public function testCanBeCreatedFromOmitted(): void
    {
        $document = $this->loadXMLDocument(self::XML_OMITTED_DATE);

        $xpath = new \DOMXPath($document);
        $currentElements = $xpath->query(sprintf('//%s', "ns2:CreditNote"));
        if ($currentElements->count() > 0) {
            /** @var \DOMElement $currentElement */
            $currentElement = $currentElements->item(0);
            $ublObject = DueDate::fromXML($xpath, $currentElement);
            $this->assertNull($ublObject);
        } else {
            $this->fail("Test source is not valid");
        }
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $document = $this->loadXMLDocument(self::XML_INVALID_DATE);

        $xpath = new \DOMXPath($document);
        $currentElements = $xpath->query(sprintf('//%s', "ns2:CreditNote"));
        if ($currentElements->count() > 0) {
            /** @var \DOMElement $currentElement */
            $currentElement = $currentElements->item(0);
            $this->expectException(\Exception::class);
            $ublObject = DueDate::fromXML($xpath, $currentElement);
        } else {
            $this->fail("Test source is not valid");
        }
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $document = $this->loadXMLDocument(self::XML_EMPTY_DATE);

        $xpath = new \DOMXPath($document);
        $currentElements = $xpath->query(sprintf('//%s', "ns2:CreditNote"));
        if ($currentElements->count() > 0) {
            /** @var \DOMElement $currentElement */
            $currentElement = $currentElements->item(0);
            $this->expectException(\Exception::class);
            $ublObject = DueDate::fromXML($xpath, $currentElement);
        } else {
            $this->fail("Test source is not valid");
        }
    }

    public function testGenerateXml(): void
    {
        $document = $this->loadXMLDocument(self::XML_VALID_DATE);

        $xpath = new \DOMXPath($document);
        $currentElements = $xpath->query(sprintf('//%s', "ns2:CreditNote"));
        if ($currentElements->count() > 0) {
            /** @var \DOMElement $currentElement */
            $currentElement = $currentElements->item(0);
            $ublObject = DueDate::fromXML($xpath, $currentElement);
            $outputElement = $this->loadXMLDocument(self::XML_ROOT);
            $this->assertEquals(self::XML_REFERENCE, $document->saveXml($document->documentElement, LIBXML_NOEMPTYTAG));
        } else {
            $this->fail("Test source is not valid");
        }
    }

    protected function loadXMLDocument($xmlSource)
    {
        $xmlDocument = new \DOMDocument();
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $loadResult = $xmlDocument->loadXML($xmlSource);
        $this->assertTrue($loadResult);

        return $xmlDocument;
    }
}