<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic;

use Tiime\EN16931\Codelist\PaymentMeansCodeUNTDID4461 as PaymentMeansCode;

class PaymentMeansNamedCode
{
    protected const XML_NODE = 'cbc:PaymentMeansCode';

    /**
     * BT-81.
     */
    private PaymentMeansCode $paymentMeansCode;

    /**
     * BT-82.
     */
    private ?string $name;

    public function __construct(PaymentMeansCode $paymentMeansCode)
    {
        $this->paymentMeansCode = $paymentMeansCode;
        $this->name             = null;
    }

    public function getPaymentMeansCode(): PaymentMeansCode
    {
        return $this->paymentMeansCode;
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

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->paymentMeansCode->value);

        if (\is_string($this->name)) {
            $currentNode->setAttribute('name', $this->name);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $paymentMeansCodeElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $paymentMeansCodeElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $paymentMeansCodeElement */
        $paymentMeansCodeElement = $paymentMeansCodeElements->item(0);

        $paymentMeansCode = PaymentMeansCode::tryFrom((string) $paymentMeansCodeElement->nodeValue);

        if (null === $paymentMeansCode) {
            throw new \Exception('Wrong payment means code');
        }

        $paymentMeansNamedCode = new self($paymentMeansCode);

        if ($paymentMeansCodeElement->hasAttribute('name')) {
            $paymentMeansNamedCode->setName($paymentMeansCodeElement->getAttribute('name'));
        }

        return $paymentMeansNamedCode;
    }
}
