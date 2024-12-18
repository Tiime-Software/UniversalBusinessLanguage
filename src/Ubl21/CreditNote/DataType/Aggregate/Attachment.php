<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate;

use Tiime\EN16931\Codelist\MimeCode;
use Tiime\EN16931\DataType\BinaryObject;

class Attachment
{
    protected const XML_NODE = 'cac:Attachment';

    /**
     * BT-125.
     */
    private ?BinaryObject $embeddedDocumentBinaryObject;

    private ?ExternalReference $externalReference;

    public function __construct()
    {
        $this->embeddedDocumentBinaryObject = null;
        $this->externalReference            = null;
    }

    public function getEmbeddedDocumentBinaryObject(): ?BinaryObject
    {
        return $this->embeddedDocumentBinaryObject;
    }

    public function setEmbeddedDocumentBinaryObject(?BinaryObject $embeddedDocumentBinaryObject): static
    {
        $this->embeddedDocumentBinaryObject = $embeddedDocumentBinaryObject;

        return $this;
    }

    public function getExternalReference(): ?ExternalReference
    {
        return $this->externalReference;
    }

    public function setExternalReference(?ExternalReference $externalReference): static
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->embeddedDocumentBinaryObject instanceof BinaryObject) {
            $binaryObjectElement = $document->createElement('cbc:EmbeddedDocumentBinaryObject', $this->embeddedDocumentBinaryObject->content);
            $binaryObjectElement->setAttribute('mimeCode', $this->embeddedDocumentBinaryObject->mimeCode->value);
            $binaryObjectElement->setAttribute('filename', $this->embeddedDocumentBinaryObject->filename);

            $currentNode->appendChild($binaryObjectElement);
        }

        if ($this->externalReference instanceof ExternalReference) {
            $currentNode->appendChild($this->externalReference->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $attachmentElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $attachmentElements->count()) {
            return null;
        }

        if ($attachmentElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $attachmentElement */
        $attachmentElement = $attachmentElements->item(0);

        $embeddedDocumentBinaryObjectElements = $xpath->query('./cbc:EmbeddedDocumentBinaryObject', $attachmentElement);
        $externalReference                    = ExternalReference::fromXML($xpath, $attachmentElement);

        if ($embeddedDocumentBinaryObjectElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $attachment = new self();

        if (1 === $embeddedDocumentBinaryObjectElements->count()) {
            /** @var \DOMElement $embeddedDocumentBinaryObjectElement */
            $embeddedDocumentBinaryObjectElement = $embeddedDocumentBinaryObjectElements->item(0);

            $content = $embeddedDocumentBinaryObjectElement->nodeValue;

            $mimeCode = MimeCode::tryFrom($embeddedDocumentBinaryObjectElement->getAttribute('mimeCode'));

            if (!$mimeCode instanceof MimeCode) {
                throw new \Exception('Wrong mimeCode');
            }

            $filename = $embeddedDocumentBinaryObjectElement->getAttribute('filename');

            $attachment->setEmbeddedDocumentBinaryObject(new BinaryObject($content, $mimeCode, $filename));
        }

        if ($externalReference instanceof ExternalReference) {
            $attachment->setExternalReference($externalReference);
        }

        return $attachment;
    }
}
