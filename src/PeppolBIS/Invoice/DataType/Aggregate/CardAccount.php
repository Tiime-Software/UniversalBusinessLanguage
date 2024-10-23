<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate;

class CardAccount
{
    protected const XML_NODE = 'cac:CardAccount';

    private string $primaryAccountNumberIdentifier;

    private string $networkIdentifier;

    private ?string $holderName;

    public function __construct(string $primaryAccountNumberIdentifier, string $networkIdentifier)
    {
        $this->primaryAccountNumberIdentifier = $primaryAccountNumberIdentifier;
        $this->networkIdentifier              = $networkIdentifier;
        $this->holderName                     = null;
    }

    public function getPrimaryAccountNumberIdentifier(): string
    {
        return $this->primaryAccountNumberIdentifier;
    }

    public function getNetworkIdentifier(): string
    {
        return $this->networkIdentifier;
    }

    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    public function setHolderName(?string $holderName): static
    {
        $this->holderName = $holderName;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:PrimaryAccountNumberID', $this->primaryAccountNumberIdentifier));
        $currentNode->appendChild($document->createElement('cbc:NetworkID', $this->networkIdentifier));

        if (\is_string($this->holderName)) {
            $currentNode->appendChild($document->createElement('cbc:HolderName', $this->holderName));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $cardAccountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $cardAccountElements->count()) {
            return null;
        }

        if (1 !== $cardAccountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $cardAccountElement */
        $cardAccountElement = $cardAccountElements->item(0);

        $primaryAccountNumberIdentifierElements = $xpath->query('./cbc:PrimaryAccountNumberID', $cardAccountElement);

        if (1 !== $primaryAccountNumberIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $primaryAccountNumberIdentifier = (string) $primaryAccountNumberIdentifierElements->item(0)->nodeValue;

        $networkIdentifierElements = $xpath->query('./cbc:NetworkID', $cardAccountElement);

        if (1 !== $networkIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $networkIdentifier = (string) $networkIdentifierElements->item(0)->nodeValue;

        $cardAccount = new self($primaryAccountNumberIdentifier, $networkIdentifier);

        $holderNameElements = $xpath->query('./cbc:HolderName', $cardAccountElement);

        if ($holderNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $holderNameElements->count()) {
            $cardAccount->setHolderName((string) $holderNameElements->item(0)->nodeValue);
        }

        return $cardAccount;
    }
}
