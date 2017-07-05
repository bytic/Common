<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseRequest as AbstractRequest;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseRequest extends AbstractRequest
{
    public function initData()
    {
        parent::initData();

        $this->validate('modelManager');
        $this->prepareServer();

        $this->pushData('valid', false);
        if ($this->validateModel() && $this->validateHash()) {
            $this->pushData('valid', true);
        }
    }

    protected function prepareServer()
    {
        ini_set("mbstring.func_overload", 0);

        /* check if mbstring.func_overload is still set to overload strings(2)*/
        if (ini_get("mbstring.func_overload") > 2) {
            echo "WARNING: mbstring.func_overload is set to overload strings and might cause problems\n";
        }
    }

    /**
     * @return boolean
     */
    protected function validateHash()
    {
        $hash = $this->httpRequest->request->get('HASH');
        $this->pushData('hash', $hash);

        $hmac = $this->generateHmac($this->generateHashString());
        $this->pushData('hmac', $hmac);
        if ($hmac == $hash) {
            $this->generateReturnHashString();

            return true;
        }

        return false;
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateHmac($data)
    {
        $key = $this->getSecretKey();

        return Helper::generateHmac($data, $key);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        /** @var IsPurchasableModelTrait $model */
        $model = $this->getDataItem('model');

        return $model->getPaymentMethod()->getType()->getGateway()->getSecretKey();
    }

    /**
     * @return string
     */
    protected function generateHashString()
    {
        $post = $this->httpRequest->request->all();

        $result = '';
        foreach ($post as $key => $val) {
            /* get values */
            if ($key != "HASH") {
                if (is_array($val)) {
                    $result .= Helper::generateHashFromArray($val);
                } else {
                    $result .= Helper::generateHashFromString($val);
                }
            }
        }

        return $result;
    }

    protected function generateReturnHashString()
    {
        $post = $this->httpRequest->request->all();
        $dateReturn = date("YmdGis");
        $return = Helper::generateHashFromString($post["IPN_PID"][0]);
        $return .= Helper::generateHashFromString($post["IPN_PNAME"][0]);
        $return .= Helper::generateHashFromString($post["IPN_DATE"][0]);
        $return .= Helper::generateHashFromString($dateReturn);

        $this->pushData('dateReturn', $dateReturn);
        $this->pushData('hashReturn', $this->generateHmac($return));
    }

    /**
     * @return int
     */
    public function getModelIdFromRequest()
    {
        return $this->getHttpRequest()->request->get('REFNOEXT');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function isProviderRequest()
    {
        return $this->hasPOST('HASH', 'REFNOEXT');
    }
}
