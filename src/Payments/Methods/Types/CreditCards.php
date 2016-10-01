<?php

namespace ByTIC\Common\Payments\Methods\Types;

use ByTIC\Common\Payments\Gateways\Traits\HasGatewaysTrait;
use Nip\Helpers\View\Messages as MessagesHelper;

/**
 * Class Payment_Method_Type_Credit_Cards
 */
class CreditCards extends AbstractType
{
    public $name = 'credit-cards';

    use HasGatewaysTrait;

    public function getEntryDescription()
    {
        if (!$this->getGateway()) {
            return MessagesHelper::error(
                $this->getGatewaysManager()->getMessage('entry-payment.invalid'));
        } elseif (!$this->getGateway()->isActive()) {
            return MessagesHelper::error(
                $this->getGatewaysManager()->getMessage('entry-payment.inactive'));
        }
        return false;
    }


    /**
     * @return bool
     */
    public function checkConfirmRedirect()
    {
        if ($this->getGateway()) {
            return $this->getGateway()->isActive();
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getGatewayOptions()
    {
        $name = $this->getGatewayName();

        return $this->getItem()->getOption($name);
    }

    /**
     * @return string
     */
    public function getGatewayName()
    {
        return $this->getItem()->getOption('payment_gateway');
    }
}
