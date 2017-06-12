<?php
/**
 * Created by PhpStorm.
 * User: PerigeeSoftouaire
 * Date: 09/06/2017
 * Time: 14:26
 */

namespace Fot\Bundle\ElvisConnectorBundle;

use Monolog\Logger;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Pim\Bundle\CatalogBundle\Entity\Channel;
use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Elvis
{

    private $baseUrl;
    private $username;
    private $accessToken;
    private $router;
    private $serializer;
    private $kernel;
    private $logger;


    public function __construct(
        ConfigManager $oroGlobal,
        RouterInterface $router,
        SerializerInterface $serializer,
        Kernel $kernel,
        Logger $logger
    ) {
        var_dump($oroGlobal);
        $this->baseUrl = $oroGlobal->get('fot_elvisconnector.base_url');
        $this->username = $oroGlobal->get('fot_elvisconnector.username');
        $this->accessToken = $oroGlobal->get('fot_elvisconnector.pwd');
        $this->router = $router;
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->logger = $logger;
    }


    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->testConnection() < 400;
    }

    protected function testConnection()
    {
        $ch = curl_init($this->baseUrl);

        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }

}