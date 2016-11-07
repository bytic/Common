<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits;

use ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Helper;

/**
 * Class CompletePurchaseRequestTrait
 * @package ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits
 */
trait CompletePurchaseRequestTrait
{
    public function initData()
    {
        parent::initData();

        $this->validate('modelManager');

        $this->pushData('valid', false);
        if ($this->validateModel() && $this->validateHash()) {
            $this->pushData('valid', true);
            $this->pushData('message', $this->getHttpRequest()->request->get('message'));
        }
    }

    /**
     * @return string
     */
    public function generateHashString()
    {
        $return = "";
        $fields = [
            'amount',
            'curr',
            'invoice_id',
            'ep_id',
            'merch_id',
            'action',
            'message',
            'approval',
            'timestamp',
            'nonce',
        ];
        foreach ($fields as $f) {
            $d = addslashes(trim($this->getHttpRequest()->request->get($f)));
            $return .= Helper::generateHashFromString($d);
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function getModelIdFromRequest()
    {
        return $this->getHttpRequest()->request->get('invoice_id');
    }

    /**
     * @return boolean
     */
    protected function validateHash()
    {
        $hash = $this->getHttpRequest()->request->get('fp_hash');
        $hmac = $this->generateHmac($this->generateHashString());

        if ($hmac == $hash) {
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
        $key = $this->getKey();

        return Helper::generateHmac($data, $key);
    }

    /**
     * @return mixed
     */
    protected function isProviderRequest()
    {
        return $this->hasPOST('amount', 'invoice_id', 'merch_id', 'ep_id', 'fp_hash');
    }
}
