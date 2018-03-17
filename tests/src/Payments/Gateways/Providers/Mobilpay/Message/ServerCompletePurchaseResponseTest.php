<?php

namespace ByTIC\Common\Tests\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Card;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Notify;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\ServerCompletePurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Tests\AbstractTest;
use Guzzle\Http\Client;
use Nip\Request;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponseTest extends AbstractTest
{

    /**
     * @var ServerCompletePurchaseResponse
     */
    protected $message;

    /**
     * @var ServerCompletePurchaseRequest
     */
    protected $request;

    /**
     * @var []
     */
    protected $data;

    public function testIsError()
    {
        $this->data['requestData']->objPmNotify->action = 'paid';
        $this->data['code'] = '20';

        $this->message = new ServerCompletePurchaseResponse($this->request, $this->data);

        self::assertSame('paid', $this->message->getAction());
        self::assertSame('20', $this->message->getCode());
        self::assertFalse($this->message->isSuccessful());
        self::assertFalse($this->message->isPending());
        self::assertSame('error', $this->message->getModelResponseStatus());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->request = new ServerCompletePurchaseRequest(new Client(), new Request());

        $requestData = new Card();
        $requestData->objPmNotify = new Notify();
        $this->data = [
            'requestData' => $requestData,
        ];
    }
}
