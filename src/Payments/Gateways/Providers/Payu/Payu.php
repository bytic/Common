<?php

namespace ByTIC\Common\Payments\Gateways\Payu;

use ByTIC\Common\Payments\Gateways\Payu\Request\LiveUpdate;

class Payu extends LiveUpdate
{


    public function generateForm()
    {
        $return = '';
        $return .= '<form name="frmPaymentRedirect" method="post" action="' . $this->liveUpdateURL . '" id="form-gateway" target="_self">';
        $return .= $this->getLiveUpdateHTML();

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to Payu.ro gateway</p>
            <p><img src="' . IMAGES_URL . ').submit()"></p>
            <p><button id="form-gateway-btn" type="submit" class="btn btn-success btn-large">Go Now!</button></p>
        </form>';

        return $return;
    }
}
