<?php

namespace Hyper\AdsBundle\Payment\BitPay;

use Hyper\AdsBundle\Payment\OrderInterface;
use Hyper\AdsBundle\Payment\ParamsProviderInterface;
use Hyper\AdsBundle\Payment\Util\OrderHashGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BitPayParamsProvider implements ParamsProviderInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Hyper\AdsBundle\Payment\Util\OrderHashGeneratorInterface */
    private $hashGenerator;

    private $transactionSpeed;
    private $fullNotifications;
    private $notificationEmail;
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(
        RouterInterface $router,
        OrderHashGeneratorInterface $hashGenerator,
        TranslatorInterface $translator,
        $transactionSpeed,
        $fullNotifications,
        $notificationEmail
    ) {
        $this->router = $router;
        $this->transactionSpeed = $transactionSpeed;
        $this->fullNotifications = !!$fullNotifications;
        $this->notificationEmail = $notificationEmail;
        $this->hashGenerator = $hashGenerator;
        $this->translator = $translator;
    }

    public function getParametersFromOrder(OrderInterface $order)
    {
        $params = array(
            'amount' => $order->getPaymentInstruction()->getAmount(),
            'currency' => $order->getPaymentInstruction()->getCurrency(),
            'redirectURL' => $this->router->generate(
                    'payment_successful',
                    array('order' => $order->getId()),
                    RouterInterface::ABSOLUTE_URL
                ),
            'notificationURL' => $this->router->generate(
                    "hyper_ads.omnipay.ipn.bitpay",
                    array(),
                    RouterInterface::ABSOLUTE_URL
                ),
            'transactionSpeed' => $this->transactionSpeed,
            'fullNotifications' => $this->fullNotifications,
            'orderID' => $order->getOrderNumber(),
            'description' => $this->translator->trans(
                    'payment.info',
                    array('%orderNumber%' => $order->getOrderNumber()),
                    'HyperAdsBundle'
                ),
        );

        if (!empty($this->notificationEmail)) {
            $params['notificationEmail'] = $this->notificationEmail;
        }

        $params['posData'] = array(
            'hash' => $this->hashGenerator->hashOrder($order),
            'posData' => $this->getPosData($order),
        );

        return $params;
    }

    private function getPosData(OrderInterface $order)
    {
        return array(
            'order' => $order->getId(),
        );
    }
}