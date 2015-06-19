<?php

namespace Marichat\ChatBundle\Sms;

use Doctrine\ORM\EntityManager;

class TurboSms
{
    const ENCODING = 'UTF-8';
    const SMS_MAX_LENGTH = 70;

    /** @var EntityManager */
    protected $smsEm;

    protected $smsUser;

    protected $smsSign;

    public function __construct(EntityManager $smsEm, $smsUser, $smsSign)
    {
        $this->smsEm = $smsEm;
        $this->smsUser = $smsUser;
        $this->smsSign = $smsSign;
    }

    /**
     * @param $number
     * @param $message
     * @param $additionalMessage
     * @param $sign
     *
     * @return int
     */
    public function send($number, $message, $additionalMessage, $sign = NULL)
    {
        if (!isset($sign)) {
            $sign = $this->smsSign;
        }

        $additionalMessage = ' ' . $additionalMessage;

        $additionalMessageLength = mb_strlen($additionalMessage, self::ENCODING);
        $messageLength = mb_strlen($message, self::ENCODING);

        if ($messageLength + $additionalMessageLength > self::SMS_MAX_LENGTH) {
            $message = mb_substr($message, 0, self::SMS_MAX_LENGTH - $additionalMessageLength, self::ENCODING);
        }

        $smsText = $message . $additionalMessage;

        $smsConnection = $this->smsEm->getConnection();
        return $smsConnection->insert($this->smsUser, array(
            'number'  => str_replace('+', '', $number),
            'sign'    => $sign,
            'message' => $smsText,
        ));
    }

}
