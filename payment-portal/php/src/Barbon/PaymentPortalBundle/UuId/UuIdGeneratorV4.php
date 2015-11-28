<?php

namespace Barbon\PaymentPortalBundle\UuId;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class UuIdGeneratorV4
 *
 * @package Barbon\PaymentPortalBundle\UuId
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service("barbon.payment_portal_bundle.uu_id.uu_id_generator")
 */
class UuIdGeneratorV4 implements UuIdGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}