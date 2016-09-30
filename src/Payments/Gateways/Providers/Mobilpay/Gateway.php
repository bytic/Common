<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay;

class Gateway extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway
{

    public function isActive()
    {
        if ($this->options['signature'] && $this->getCertificate()) {
            return true;
        }
        return false;
    }

    public function getCertificate()
    {
        $files = $this->getPaymentMethodModel()->findFiles();
        $certificate = $files['public.cer'];
        if (is_object($certificate)) {
            return $certificate->getPath();
        }
        return false;
    }

    public function generatePaymentForm($donation)
    {
        $pClass = $this->getProviderClass();

        $objPmReqCard = $pClass->getCardRequest();
        $pClass->getCardRequest()->orderId = $donation->id;
        $pClass->getCardRequest()->confirmUrl = $donation->getIpnURL();
        $pClass->getCardRequest()->returnUrl = $donation->getConfirmURL();

        $objPmReqCard->invoice = new Mobilpay_Payment_Invoice();
        $objPmReqCard->invoice->currency = 'RON';
        $objPmReqCard->invoice->amount = $donation->amount;
        $objPmReqCard->invoice->installments = '2,3';
        $objPmReqCard->invoice->details = $donation->getCCName();

        $billingAddress = new Mobilpay_Payment_Address();
        $billingAddress->type = 'person'; //company
        $billingAddress->firstName = $donation->getOrgDonor()->first_name;
        $billingAddress->lastName = $donation->getOrgDonor()->last_name;
        $billingAddress->fiscalNumber = '';
        $billingAddress->identityNumber = '';
        $billingAddress->country = '';
        $billingAddress->county = '';
        $billingAddress->city = 'na';
        $billingAddress->zipCode = '';
        $billingAddress->address = 'Romania';
        $billingAddress->email = $donation->getOrgDonor()->email;
        $billingAddress->mobilePhone = '0741000000';
        $billingAddress->bank = '';
        $billingAddress->iban = '';
        $objPmReqCard->invoice->setBillingAddress($billingAddress);

        $shippingAddress = new Mobilpay_Payment_Address();
        $shippingAddress->type = 'person'; //company
        $shippingAddress->firstName = $donation->getOrgDonor()->first_name;
        $shippingAddress->lastName = $donation->getOrgDonor()->last_name;
        $shippingAddress->fiscalNumber = '';
        $shippingAddress->identityNumber = '';
        $shippingAddress->country = '';
        $shippingAddress->county = '';
        $shippingAddress->city = 'na';
        $shippingAddress->zipCode = '';
        $shippingAddress->address = 'Romania';
        $shippingAddress->email = $donation->getOrgDonor()->email;
        $shippingAddress->mobilePhone = '0741000000';
        $shippingAddress->bank = '';
        $shippingAddress->iban = '';
        $objPmReqCard->invoice->setShippingAddress($shippingAddress);

        $objPmReqCard->encrypt($this->getCertificate());

        return $pClass->generateForm();
    }

    public function detectConfirmResponse()
    {
        return $this->detectRequestFields($_GET, array('orderId'));
    }

    public function detectIPNResponse()
    {
        return $this->detectRequestFields($_POST, array('env_key', 'data'));
    }

    public function parseConfirmResponse()
    {
        $donation = Donations::instance()->findOne($_GET['orderId']);
        if ($donation) {
            $gateway = $donation->getPayment_Method()->getType()->getGateway();
        }
        return $donation;
    }

    public function parseIPNResponse()
    {
        $errorCode = 0;
        $errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_NONE;
        $errorMessage = '';

        if (isset($_POST['env_key']) && isset($_POST['data'])) {
            $getDonation = Donations::instance()->findOne($_GET['id']);
            if ($getDonation) {
                $this->setPaymentMethodModel($getDonation->getPayment_Method());
                $privateKeyFilePath = $this->getPrivateKey();

                try {
                    $objPmReq = Mobilpay_Payment_Request_Abstract::factoryFromEncrypted($_POST['env_key'],
                        $_POST['data'], $privateKeyFilePath);

                    $donation = Donations::instance()->findOne($objPmReq->orderId);
                    $getDonation->payee_name = $objPmReq->objPmNotify->customer->firstName . ' ' . $objPmReq->objPmNotify->customer->lastName;
                    $_POST['data_decripted'] = print_r($objPmReq, true);

                    if ($donation->id == $getDonation->id) {
                        $errorCode = $objPmReq->objPmNotify->errorCode;
                        $errorMessage = $objPmReq->objPmNotify->errorMessage;

                        if ($errorCode == 0) {
                            switch ($objPmReq->objPmNotify->action) {
                                #orice action este insotit de un cod de eroare si de un mesaj de eroare. Acestea pot fi citite folosind $cod_eroare = $objPmReq->objPmNotify->errorCode; respectiv $mesaj_eroare = $objPmReq->objPmNotify->errorMessage;
                                #pentru a identifica ID-ul comenzii pentru care primim rezultatul platii folosim $id_comanda = $objPmReq->orderId;
                                case 'confirmed':
                                    #cand action este confirmed avem certitudinea ca banii au plecat din contul posesorului de card si facem update al starii comenzii si livrarea produsului
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'active';
                                    $getDonation->received = date(DATE_DB);
                                    break;
                                case 'confirmed_pending':
                                    #cand action este confirmed_pending inseamna ca tranzactia este in curs de verificare antifrauda. Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'pending';
                                    break;
                                case 'paid_pending':
                                    #cand action este paid_pending inseamna ca tranzactia este in curs de verificare. Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'pending';
                                    break;
                                case 'paid':
                                    #cand action este paid inseamna ca tranzactia este in curs de procesare. Nu facem livrare/expediere. In urma trecerii de aceasta procesare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'pending';
                                    break;
                                case 'canceled':
                                    #cand action este canceled inseamna ca tranzactia este anulata. Nu facem livrare/expediere.
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'canceled';
                                    break;
                                case 'credit':
                                    #cand action este credit inseamna ca banii sunt returnati posesorului de card. Daca s-a facut deja livrare, aceasta trebuie oprita sau facut un reverse.
                                    $errorMessage = $objPmReq->objPmNotify->getCrc();
                                    $newStatus = 'canceled';
                                    break;
                                default:
                                    $errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
                                    $errorCode = Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_ACTION;
                                    $errorMessage = 'mobilpay_refference_action paramaters is invalid';
                                    if ($getDonation->status == 'active') {
                                    } else {
                                        $newStatus = 'error';
                                    }
                                    break;
                            }
                        } else {
                            if ($getDonation->status == 'active') {
                            } else {
                                $newStatus = 'error';
                            }
                        }
                    }
                } catch (Exception $e) {
                    $errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_TEMPORARY;
                    $errorCode = $e->getCode();
                    $errorMessage = $e->getMessage();
                }

                $getDonation->status_notes = '#' . $errorCode . ' ' . $errorMessage;
                $getDonation->setStatus($newStatus);
            } else {
                $errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
                $errorCode = Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
                $errorMessage = 'mobilpay.ro posted invalid parameters';
            }
        } else {
            $errorType = Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
            $errorCode = Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
            $errorMessage = 'mobilpay.ro posted invalid parameters';
        }

        header('Content-type: application/xml');
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";

        if ($errorCode == 0) {
            echo "<crc>{$errorMessage}</crc>";
        } else {
            echo "<crc error_type=\"{$errorType}\" error_code=\"{$errorCode}\">{$errorMessage}</crc>";
        }
        return $getDonation;
    }

    public function getPrivateKey()
    {
        $files = $this->getPaymentMethodModel()->findFiles();
        $certificate = $files['private.key'];
        if (is_object($certificate)) {
            return $certificate->getPath();
        }
        return false;
    }

    /**
     * @return Mobilpay
     */
    public function generateProviderClass()
    {
        $class = new Mobilpay();
        $class->setSignature($this->getOption('signature'));
        $class->setCertificate($this->getCertificate());
        $class->setSandboxMode($this->getOption('sandbox') == 'yes');
        return $class;
    }
}
