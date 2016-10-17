<?phpnamespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc;class Gateway extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway{    public function isActive()    {        if ($this->options['key'] && $this->options['mid']) {            return true;        }        return false;    }    public function generatePaymentForm($donation)    {        $pClass = $this->getProviderClass();        $pClass->setData('order', [            'amount' => $donation->amount,            'invoice_id' => $donation->id,            'order_desc' => $donation->getCCName(),        ]);        $donor = $donation->getOrgDonor();        $pClass->setData('bill', [            'fname' => $donor->first_name,   // nume            'lname' => $donor->last_name,   // prenume            'country' => 'Romania',   // tara            'email' => $donor->email,   // email        ]);        return $pClass->generateForm();    }    public function detectConfirmResponse()    {        return $this->detectRequestFields($_POST, ['amount', 'invoice_id', 'merch_id', 'ep_id', 'fp_hash']);    }    public function detectIPNResponse()    {        return $this->detectRequestFields($_POST, ['amount', 'invoice_id', 'merch_id', 'ep_id', 'fp_hash']);    }    public function parseIPNResponse()    {        return $this->parseConfirmResponse();    }    public function parseConfirmResponse()    {        $donation = Donations::instance()->findOne($_POST['invoice_id']);        if ($donation) {            $gateway = $donation->getPayment_Method()->getType()->getGateway();            if ($_POST['action'] != 0) {                $donation->gateway_error = $_POST['message'];                $donation->updateStatus('error');            } else {                if ($donation->amount != $_POST['amount'] || $_POST['merch_id'] != $gateway->getProcesingClass()->getMID()) {                    $donation->gateway_error = 'Eroare autorizare plata';                }            }            if (!$donation->gateway_error && $donation->status == 'pending') {                $donation->received = date(DATE_DB);                $donation->updateStatus('active');            }        }        return $donation;    }    /**     * @return Euplatesc     */    public function generateProviderClass()    {        $class = new Euplatesc();        $class->setKEY($this->getOption('key'))            ->setMID($this->getOption('mid'));        return $class;    }}