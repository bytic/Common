<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc;

class Euplatesc
{
    private $_mid = "";
    private $_key = "";
    private $_data = array();


    public function __construct()
    {
        $this->_data = array(
            'order' => array(
                'amount' => 0,
                //suma de plata
                'curr' => 'RON',
                // moneda de plata
                'invoice_id' => 0,
                // numarul comenzii este generat aleator. inlocuiti cuu seria dumneavoastra
                'order_desc' => '',
                //descrierea comenzii
                'merch_id' => $this->_mid,
                // nu modificati
                'timestamp' => gmdate("YmdHis"),
                // nu modificati
                'nonce' => md5(microtime() . mt_rand()),
                //nu modificati
            ),
            'bill' => array(
                'fname' => '',   // nume
                'lname' => '',   // prenume
                'country' => '',   // tara
                'company' => '',   // firma
                'city' => '',   // oras
                'add' => '',   // adresa
                'email' => '',   // email
                'phone' => '',   // telefon
                'fax' => '',   // fax
            ),
        );
    }

    public function setData($type, $data)
    {
        foreach ($data as $label => $value) {
            $this->_data[$type][$label] = trim(iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $value));
        }
    }

    public function setKEY($value)
    {
        $this->_key = $value;
        return $this;
    }

    public function getMID()
    {
        return $this->_mid;
    }

    public function setMID($value)
    {
        $this->_data['order']['merch_id'] = $this->_mid = $value;
        return $this;
    }

    public function generateForm()
    {
        $return = '';
        $return .= '<form ACTION="https://secure.euplatesc.ro/tdsprocess/tranzactd.php" METHOD="POST" id="form-gateway" name="gateway" target="_self">';

        $return .= '<!-- begin billing details -->';
        foreach ($this->_data['bill'] as $field => $value) {
            $return .= '<input name="' . $field . '" type="hidden" value="' . $value . '" />';
        }
        $return .= '<!-- snd billing details -->';


        foreach ($this->_data['order'] as $field => $value) {
            $return .= '<input name="' . $field . '" type="hidden" value="' . $value . '" />';
        }
        $return .= '<input TYPE="hidden" NAME="fp_hash" SIZE="40" VALUE="' . strtoupper($this->_mac($this->_data['order'])) . '" />';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to EuPlatesc.ro gateway</p>
            <p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title="" onload="document.getElementById(\'form-gateway\').submit()"></p>
            <p><button id="form-gateway-btn" type="submit" class="btn btn-success">Go Now!</button></p>
        </form>';

        return $return;
    }

    private function _mac($data)
    {
        $str = null;

        foreach ($data as $d) {
            if ($d === null || strlen($d) == 0) {
                $str .= '-'; // valorile nule sunt inlocuite cu -
            } else {
                $str .= strlen($d) . $d;
            }
        }

        $key = pack('H*', $this->_key);

        return $this->_hash($key, $str);
    }

    private function _hash($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'md5';

        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }

        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);

        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        return bin2hex($hmac);
    }
}
