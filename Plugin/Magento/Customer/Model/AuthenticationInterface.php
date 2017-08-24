<?php

namespace Powerbuy\Authentication\Plugin\Magento\Customer\Model;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Store\Model\ScopeInterface;

class AuthenticationInterface
{
    /**
     * Enable/disable Powerbuy authentication
     */
    const XML_POWERBUY_AUTH_ENABLE = 'powerbuyauth/general/enable';
    /**
     * Default hash string delimiter
     */
    const XML_POWERBUY_AUTH_DELIMITER = 'powerbuyauth/general/hash_delimiter';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;
    /**
     * @var Encryptor
     */
    protected $encryptor;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRegistry $customerRegistry
     * @param Encryptor $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CustomerRegistry $customerRegistry,
        Encryptor $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
    }

    function aroundAuthenticate($subject, \Closure $proceed, $customerId, $password)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $enable = $this->scopeConfig->getValue(self::XML_POWERBUY_AUTH_ENABLE, $storeScope);

        if ($enable) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
            $passwordHash = $customerSecure->getPasswordHash();
            $delimiter = $this->scopeConfig->getValue(self::XML_POWERBUY_AUTH_DELIMITER, $storeScope);
            if (substr_count($passwordHash, $delimiter) == 1) {
                list($expectHashValue, $salt) = explode($delimiter, $passwordHash, 2);
                $hashValue = $this->encryptor->hash($salt . $password, Encryptor::HASH_VERSION_MD5);
                $comparePasswordHash = Security::compareStrings($hashValue, $expectHashValue);
                if ($comparePasswordHash)
                {
                    return true;
                }
            }
        }
        return $proceed($customerId, $password);
    }

}
