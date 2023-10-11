<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BG-6. or BG-9.
 */
class Contact
{
    protected const XML_NODE = 'cac:Contact';

    /**
     * BT-41. or BT-56.
     */
    private ?string $name;

    /**
     * BT-42. or BT-57.
     */
    private ?string $telephone;

    /**
     * BT-43. or BT-58.
     */
    private ?string $electronicMail;

    public function __construct()
    {
        $this->name           = null;
        $this->telephone      = null;
        $this->electronicMail = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getElectronicMail(): ?string
    {
        return $this->electronicMail;
    }

    public function setElectronicMail(?string $electronicMail): static
    {
        $this->electronicMail = $electronicMail;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if (\is_string($this->name)) {
            $currentNode->appendChild($document->createElement('cbc:Name', $this->name));
        }

        if (\is_string($this->telephone)) {
            $currentNode->appendChild($document->createElement('cbc:Telephone', $this->telephone));
        }

        if (\is_string($this->electronicMail)) {
            $currentNode->appendChild($document->createElement('cbc:ElectronicMail', $this->electronicMail));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $contactElements = $xpath->query(sprintf('//%s', self::XML_NODE));

        if (0 === $contactElements->count()) {
            return null;
        }

        if ($contactElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $contactElement */
        $contactElement = $contactElements->item(0);

        $nameElements = $xpath->query('./cbc:Name', $contactElement);

        if ($nameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $telephoneElements = $xpath->query('./cbc:Telephone', $contactElement);

        if ($telephoneElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $electronicMailElements = $xpath->query('./cbc:ElectronicMail', $contactElement);

        if ($electronicMailElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $contact = new self();

        if (1 === $nameElements->count()) {
            $contact->setName((string) $nameElements->item(0)->nodeValue);
        }

        if (1 === $telephoneElements->count()) {
            $contact->setTelephone((string) $telephoneElements->item(0)->nodeValue);
        }

        if (1 === $electronicMailElements->count()) {
            $contact->setElectronicMail((string) $electronicMailElements->item(0)->nodeValue);
        }

        return $contact;
    }
}
