<?php

namespace NewEcomAI\ShopSmart\Helper;

use Exception;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class Area
{
    /**
     * @var State
     */
    private State $state;
    /**
     * @param State $state
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }
    /**
     * @return string|null
     */
    public function getAreaCode(): ?string
    {
        try
        {
            return $this->state->getAreaCode();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    /**
     * @param string $code
     *
     * AREA_GLOBAL = 'global';
     * AREA_FRONTEND = 'frontend';
     * AREA_ADMINHTML = 'adminhtml';
     * AREA_DOC = 'doc';
     * AREA_CRONTAB = 'crontab';
     * AREA_WEBAPI_REST = 'webapi_rest';
     * AREA_WEBAPI_SOAP = 'webapi_soap';
     * AREA_GRAPHQL = 'graphql';
     *
     * @return void
     * @throws LocalizedException
     */
    public function setAreaCode(string $code = "global")
    {
        $areaCode = $this->getAreaCode();
        if (!$areaCode) {
            $this->state->setAreaCode($code);
        }
    }
}
