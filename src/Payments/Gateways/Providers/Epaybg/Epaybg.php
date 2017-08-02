<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Epaybg;

/**
 * Class Epaybg
 * @package ByTIC\Common\Payments\Gateways\Providers\Epaybg
 */
class Epaybg
{
    protected $_paymentUrl = 'https://www.epay.bg/';
    protected $_paymentUrlTest = 'https://demo.epay.bg/';
    /**
     * Test Mode
     * Set to true or or 1 for testing mode.
     * @var boolean
     */
    protected $_sandboxMode = false;
    private $_min = "";
    private $_key = "";
    //protected $_paymentUrlTest = 'https://devep2.datamax.bg/ep2/epay2_demo/';
    private $_data = [];

    public function setSecretKey($value)
    {
        $this->_key = $value;

        return $this;
    }

    public function getMIN()
    {
        return $this->_min;
    }

    public function setMIN($value)
    {
        $this->_min = $value;

        return $this;
    }

    public function setData($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function generateForm()
    {
        $lang = translator()->getLanguage() == 'bg' ? 'bg' : 'en';
        $exp_date = date('d.m.Y', strtotime('+360 days'));

        $data = "MIN={$this->_min}
        INVOICE={$this->_data['invoice']}
        AMOUNT={$this->_data['amount']}
        EXP_TIME={$exp_date}
        DESCR={$this->_data['description']}
		ENCODING=utf-8";

        $ENCODED = base64_encode($data);
        $CHECKSUM = $this->_hmac('sha1', $ENCODED, $this->_key);

        $return = '';
        $paymentUrl = $this->_sandboxMode === true ? $this->_paymentUrlTest : $this->_paymentUrl;
        $paymentUrl .= $lang == 'bg' ? '' : 'en/';
        $return .= '<form ACTION="' . $paymentUrl . '" METHOD="POST" id="form-gateway" name="gateway" target="_self">';

        $pageType = $this->paymentpage == 'credit_paydirect' ? 'credit_paydirect' : 'paylogin';
        $return .= '<input type=hidden name=PAGE value="' . $pageType . '">
					<input type=hidden name=LANG value="' . $lang . '">
                    <input type=hidden name=ENCODED value="' . $ENCODED . '">
                    <input type=hidden name=CHECKSUM value="' . $CHECKSUM . '">
                    <input type=hidden name=URL_OK value="' . $this->returnUrl . '">
                    <input type=hidden name=URL_CANCEL value="' . $this->cancelUrl . '">';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to ePay.bg gateway</p>
            <p><img src="' . IMAGES_URL . '/payment-gateways/epay.bg.png" width="90" alt="" title="" onload="//javascript:document.getElementById(\'form-gateway\').submit()"></p>
            <p><button class="btn btn-success btn-lg">Go Now!</button></p>
        </form>';

        return $return;
    }

    public function _hmac($algo, $data, $passwd = null)
    {
        $passwd = $passwd ? $passwd : $this->getSecretKey();

        /* md5 and sha1 only */
        $algo = strtolower($algo);
        $p = array('md5' => 'H32', 'sha1' => 'H40');
        if (strlen($passwd) > 64) {
            $passwd = pack($p[$algo], $algo($passwd));
        }
        if (strlen($passwd) < 64) {
            $passwd = str_pad($passwd, 64, chr(0));
        }

        $ipad = substr($passwd, 0, 64) ^ str_repeat(chr(0x36), 64);
        $opad = substr($passwd, 0, 64) ^ str_repeat(chr(0x5C), 64);

        return ($algo($opad . pack($p[$algo], $algo($ipad . $data))));
    }

    public function getSecretKey()
    {
        return $this->_key;
    }

    function setSandboxMode($sandboxMode = false)
    {
        switch ($sandboxMode) {
            case true:
            case 1:
                $this->_sandboxMode = true;
                break;

            case false:
            case 0:
            default:
                $this->_sandboxMode = false;
        }

        return true;
    }

}