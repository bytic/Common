<?php
/**
 * @var Nip\View $this
 * @var ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse $response
 */
$response = $this->get('response');
$model = $response->getModel();
$messageType = $response->getMessageType();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Redirecting...</title>
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Open+Sans:400,300'>
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding: 0;
            background-color: #fffffe;
            color: #1a1a1a;
            text-align: center;
        }

        .header {
            margin-top: 100px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>
        <?php echo translator()->translate('payment-gateways.messages.confirm.'.$messageType.'.title') ?>
    </h1>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h4>
                <?php echo $model->getPurchaseName(); ?>
            </h4>
            <hr/>

            <p>
                <?php
                echo $this->Messages()->$messageType($model->getManager()->getMessage('confirm.'.$model->status));
                ?>
            </p>

            <?php if (!$response->isSuccessful()) { ?>
                <p>
                    <?php
                    echo $this->Messages()->error(
                        '<strong>'
                        .translator()->translate('payment-gateways.messages.confirm.error.message')
                        .'</strong>:<br />'
                        .$response->getMessage()
                    );
                    ?>
                </p>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>