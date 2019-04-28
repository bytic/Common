<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Romcard;

/**
 * Class Romcard
 * @package ByTIC\Common\Payments\Gateways\Providers\Romcard
 *
 * @deprecated use \ByTIC\Payments
 */
class Romcard
{
    protected $paymentUrl = 'https://www.secure11gw.ro/portal/cgi-bin/';
    protected $paymentUrlTest = 'https://www.activare3dsecure.ro/teste3d/cgi-bin/';
    /**
     *
     * Test Mode
     * Set to true or or 1 for testing mode.
     * @var boolean
     */
    protected $sandboxMode = false;

    private $merch_name = "";
    private $terminal = "";

    private $key = "";
    private $data = array(
        'request' => array(),
        'response' => array(),
    );


    /**
     * Romcard constructor.
     */
    public function __construct()
    {
        $this->data['request'] = array(
            'AMOUNT' => 0,
            'CURRENCY' => 'RON',
            'ORDER' => 0,
            'DESC' => '',
            'MERCH_NAME' => '',
            'MERCH_URL' => BASE_URL,
            'MERCHANT' => '',
            'TERMINAL' => '',
            'EMAIL' => 'info@galantom.ro',
            'TRTYPE' => '0',
            'COUNTRY' => '',
            'MERCH_GMT' => '',
            'TIMESTAMP' => gmdate("YmdHis"),
            'NONCE' => md5(microtime() . mt_rand()),
            'BACKREF' => '',   // prenume
        );

        $this->data['response'] = array(
            'TERMINAL' => '',
            'TRTYPE' => '',
            'ORDER' => '',
            'AMOUNT' => '',
            'CURRENCY' => '',
            'DESC' => '',
            'ACTION' => '',
            'RC' => '',
            'MESSAGE' => '',
            'RRN' => '',
            'INT_REF' => '',
            'APPROVAL' => '',
            'TIMESTAMP' => '',
            'NONCE' => '',
        );

        $this->data['sale'] = array(
            'ORDER' => 0,
            'AMOUNT' => 0,
            'CURRENCY' => 'RON',
            'RRN' => '',
            'INT_REF' => '',
            'TRTYPE' => '21',
            'TERMINAL' => '',
            'TIMESTAMP' => gmdate("YmdHis"),
            'NONCE' => md5(microtime() . mt_rand()),
            'BACKREF' => '',
        );

        $this->data['responseSale'] = array(
            'ACTION' => '',
            'RC' => '',
            'MESSAGE' => '',
            'TRTYPE' => '',
            'AMOUNT' => '',
            'CURRENCY' => '',
            'ORDER' => '',
            'RRN' => '',
            'INT_REF' => '',
            'TIMESTAMP' => '',
            'NONCE' => '',
        );

        $this->data['canceled'] = array(
            'ORDER' => 0,
            'AMOUNT' => 0,
            'CURRENCY' => 'RON',
            'RRN' => '',
            'INT_REF' => '',
            'TRTYPE' => '24',
            'TERMINAL' => '',
            'TIMESTAMP' => gmdate("YmdHis"),
            'NONCE' => md5(microtime() . mt_rand()),
            'BACKREF' => '',
        );
    }

    public function setData($data, $type = 'request')
    {
        foreach ($data as $label => $value) {
            if (isset($this->data[$type][$label])) {
                $this->data[$type][$label] = $value;
            }
        }
    }

    public function getDataField($field, $type = 'request')
    {
        return $this->data[$type][$field];
    }

    public function getTerminal()
    {
        return $this->terminal;
    }

    public function setTerminal($value)
    {
        $this->terminal = $value;
        $this->data['request']['TERMINAL'] = $this->terminal;
        $this->data['request']['MERCHANT'] = '0000000' . $this->terminal;

        $this->data['sale']['TERMINAL'] = $this->terminal;
        $this->data['canceled']['TERMINAL'] = $this->terminal;
        return $this;
    }

    public function setMerchName($value)
    {
        $this->data['request']['MERCH_NAME'] = $this->merch_name = $value;
        return $this;
    }

    public function setKey($value)
    {
        $this->key = $value;
        return $this;
    }

    public function calculatePSIGN()
    {
        $this->data['request']['P_SIGN'] = $this->getPSIGN();
    }

    public function getPSIGN($varName = 'request')
    {
        return strtoupper($this->mac($this->data[$varName]));
    }

    private function mac($data)
    {
        $str = null;

        foreach ($data as $d) {
            if ($d === null || strlen($d) == 0) {
                $str .= '-'; // valorile nule sunt inlocuite cu -
            } else {
                $str .= strlen($d) . $d;
            }
        }

        $key = pack('H*', $this->key);

        return $this->_hash($key, $str);
    }

    private function _hash($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'sha1';

        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }

        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);

        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        return bin2hex($hmac);
    }

    public function generateForm()
    {
        $paymentUrl = $this->getPaymentURL();

        $return = '';
        $return .= '<form ACTION="' . $paymentUrl . '" METHOD="POST" id="form-gateway" name="gateway" target="_self">';

        $return .= '<!-- begin details -->';
        foreach ($this->data['request'] as $field => $value) {
            $return .= '<input name="' . $field . '" type="hidden" value="' . $value . '" />';
        }
        $return .= '<!-- end  details -->';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to ROMCARD gateway</p>
            <p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title="" onload="document.getElementById(\'form-gateway8\').submit()"></p>
            <p><button class="btn btn-success">Go Now!</button></p>
        </form>';

        return $return;
    }

    public function getPaymentURL()
    {
        return $this->sandboxMode === true ? $this->paymentUrlTest : $this->paymentUrl;
    }

    public function sendSaleMessage($donation)
    {
        $paymentUrl = $this->getPaymentURL();

        $this->data['sale']['ORDER'] = str_pad($donation->id, 10, "0", STR_PAD_LEFT);
        $this->data['sale']['AMOUNT'] = $donation->amount;
        $this->data['sale']['RRN'] = $donation->options['RRN'];
        $this->data['sale']['INT_REF'] = $donation->options['INT_REF'];
        $this->data['sale']['BACKREF'] = $donation->getConfirmURL();

        $data = $this->data['sale'];
        $data['P_SIGN'] = $this->getPSIGN('sale');

        $return = '';
        $return .= '<form ACTION="' . $paymentUrl . '" METHOD="POST" id="form-gateway" name="gateway" target="_self">';

        $return .= '<!-- begin details -->';
        foreach ($data as $field => $value) {
            $return .= '<input name="' . $field . '" type="hidden" value="' . $value . '" />';
        }
        $return .= '<!-- end  details -->';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to ROMCARD gateway</p>
            <p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title="" onload="document.getElementById(\'form-gateway\').submit()"></p>
            <p><button class="btn btn-success">Go Now!</button></p>
        </form>';

        echo $return;

        die('');
    }

    public function sendCanceledMessage($donation)
    {
        $paymentUrl = $this->getPaymentURL();
        $messageType = 'canceled';

        $this->data[$messageType]['ORDER'] = str_pad($donation->id, 10, "0", STR_PAD_LEFT);
        $this->data[$messageType]['AMOUNT'] = $donation->amount;
        $this->data[$messageType]['RRN'] = $donation->options['RRN'];
        $this->data[$messageType]['INT_REF'] = $donation->options['INT_REF'];
        $this->data[$messageType]['BACKREF'] = $donation->getConfirmURL();

        $data = $this->data[$messageType];
        $data['P_SIGN'] = $this->getPSIGN($messageType);

        $return = '';
        $return .= '<form ACTION="' . $paymentUrl . '" METHOD="POST" id="form-gateway" name="gateway" target="_self">';

        $return .= '<!-- begin details -->';
        foreach ($data as $field => $value) {
            $return .= '<input name="' . $field . '" type="hidden" value="' . $value . '" />';
        }
        $return .= '<!-- end  details -->';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to ROMCARD gateway</p>
            <p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title="" onload="document.getElementById(\'f+orm-gateway\').submit()"></p>
            <p><button class="btn btn-success">Go Now!</button></p>
        </form>';

        echo $return;

        die('');
    }

    public function setSandboxMode($sandboxMode = false)
    {
        switch ($sandboxMode) {
            case true:
            case 1:
                $this->sandboxMode = true;
                break;

            case false:
            case 0:
            default:
                $this->sandboxMode = false;
        }
        return true;
    }
}
