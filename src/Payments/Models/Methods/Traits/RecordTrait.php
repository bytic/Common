<?php

namespace ByTIC\Common\Payments\Models\Methods\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Files\MobilpayFile;
use ByTIC\Common\Payments\Models\Methods\Types\AbstractType;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Records\Traits\HasTypes\RecordTrait as HasTypesRecordTrait;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Models\Methods\Traits
 *
 * @property string $name
 * @property string $internal_name
 * @property string $descriptio
 *
 * @method AbstractType|CreditCards getType
 * @method RecordsTrait getManager()
 */
trait RecordTrait
{
    use HasTypesRecordTrait;

    use \ByTIC\Common\Records\Traits\HasSerializedOptions\RecordTrait;

    use \ByTIC\Common\Records\Traits\Media\Generic\RecordTrait;
    use \ByTIC\Common\Records\Traits\Media\Files\RecordTrait {
        getFileModelName as getFileModelNameAbstract;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function writeData($data = [])
    {
        if (!$data['type']) {
            $data['type'] = 'bank-transfer';
        }

        return parent::writeData($data);
    }

    /**
     * @param bool $type
     * @return string
     */
    public function getName($type = false)
    {
        if ($type == 'internal') {
            return $this->internal_name;
        }
        return $this->name;
    }

    /**
     * @return bool
     */
    public function checkConfirmRedirect()
    {
        if ($this->getType()->checkConfirmRedirect()) {
            return true;
        }

        return false;
    }

    public function getEntryDescription()
    {
        $return = $this->getType()->getEntryDescription();
        $return .= $this->description;
        $return .= $this->__notes;

        return $return;
    }

    /**
     * @return bool|Gateway|null
     */
    public function getGateway()
    {
        if ($this->getType()->getName() == 'credit-cards') {
            return $this->getType()->getGateway();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPaymentGatewayOptions()
    {
        $gatewayName = $this->getOption('payment_gateway');

        return $this->getOption($gatewayName);
    }


    /**
     * @param null $type
     * @return string
     */
    public function getFileModelName($type = null)
    {
        if ($type == 'Mobilpay') {
            return MobilpayFile::class;
        }
        return $this->getFileModelNameAbstract($type);
    }

    /**
     * @return int
     */
    abstract public function getPurchasesCount();
}
