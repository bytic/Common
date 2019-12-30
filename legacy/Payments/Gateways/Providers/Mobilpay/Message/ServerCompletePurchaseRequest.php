<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseRequest as AbstractRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\AbstractRequest as ApiAbstractRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Notify;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\Traits\ParamSettersRequestTrait;
use Exception;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseRequest extends AbstractRequest
{
    use ParamSettersRequestTrait;

    public function initData()
    {
        parent::initData();

        $this->validate('modelManager');

        $this->pushData('valid', false);
        if ($this->validateRequestData()) {
            $this->pushData('valid', true);
            if ($this->validateModel()) {
                $this->updatePrivateKeyFromModel();
                $this->decodeRequestData();
            } else {
                $this->setInvalidPostParameters();
            }
        }
    }

    /**
     * @return bool
     */
    protected function validateRequestData()
    {
        $this->setErrorParameters(0, ApiAbstractRequest::CONFIRM_ERROR_TYPE_NONE, '');

        if ($this->hasPOST('env_key') && $this->hasPOST('data')) {
            return true;
        } else {
            $this->setInvalidPostParameters();
        }

        return false;
    }

    /**
     * @param $code
     * @param $type
     * @param $message
     */
    protected function setErrorParameters($code, $type, $message)
    {
        $this->pushData('code', $code);
        $this->pushData('codeType', $type);
        $this->pushData('message', $message);
    }

    protected function setInvalidPostParameters()
    {
        $this->setErrorParameters(
            ApiAbstractRequest::CONFIRM_ERROR_TYPE_PERMANENT,
            ApiAbstractRequest::ERROR_CONFIRM_INVALID_POST_PARAMETERS,
            'mobilpay.ro posted invalid parameters'
        );
    }

    /**
     * @return mixed
     */
    public function updatePrivateKeyFromModel()
    {
        $model = $this->getModel();
        $this->setParameter('privateKey', $model->getPaymentMethod()->getType()->getGateway()->getPrivateKey());
    }

    /**
     * @return bool
     */
    protected function decodeRequestData()
    {
        try {
            $requestData = ApiAbstractRequest::factoryFromEncrypted(
                $this->httpRequest->request->get('env_key'),
                $this->httpRequest->request->get('data'),
                $this->getPrivateKey()
            );

            $getModel = $this->getModel();

            $this->setModelFromId($requestData->orderId);
            $model = $this->getModel();
            if ($model && $model->id == $getModel->id) {
                $this->decodeDataFromMobilpayRequest($requestData);

                $_POST['data_decripted'] = print_r($requestData, true);
                $this->httpRequest->request->set('data_decripted', print_r($requestData, true));
            } else {
                $this->setInvalidPostParameters();
            }
        } catch (Exception $e) {
            $this->setErrorParameters(
                ApiAbstractRequest::CONFIRM_ERROR_TYPE_TEMPORARY,
                $e->getCode(),
                $e->getMessage()
            );
        }
    }

    /**
     * @param Request $requestData
     */
    protected function decodeDataFromMobilpayRequest($requestData)
    {
        /** @var Notify $notification */
        $notification = $requestData->objPmNotify;

        $this->pushData('requestData', $requestData);
        $this->pushData('code', $notification->errorCode);
        $this->pushData('message', $notification->errorMessage);
        $this->pushData('action', $notification->action);
        $this->pushData('notificationCrc', $notification->getCrc());
        $this->decodeRequestAction($notification->action);
    }

    /**
     * @param $action
     */
    protected function decodeRequestAction($action)
    {
        $errorType = $this->data['codeType'];
        $errorCode = $this->data['code'];
        $errorMessage = $this->data['message'];

        switch ($action) {
            case 'confirmed':
            case 'confirmed_pending':
            case 'paid_pending':
            case 'paid':
            case 'canceled':
            case 'credit':
                break;

            default:
                $errorType = ApiAbstractRequest::CONFIRM_ERROR_TYPE_PERMANENT;
                $errorCode = ApiAbstractRequest::ERROR_CONFIRM_INVALID_ACTION;
                $errorMessage = 'mobilpay_refference_action paramaters is invalid';
                break;
        }

        $this->setErrorParameters($errorCode, $errorType, $errorMessage);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return mixed
     */
    protected function isProviderRequest()
    {
        return $this->hasPOST('env_key', 'data');
    }

    /**
     * @return mixed
     */
    public function isValidNotification()
    {
        return $this->hasPOST('env_key', 'data');
    }
}
