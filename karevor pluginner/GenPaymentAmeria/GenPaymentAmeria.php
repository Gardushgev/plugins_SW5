<?php

namespace GenPaymentAmeria;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Payment\Payment;

class GenPaymentAmeria extends Plugin
{
    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        /** @var \Shopware\Components\Plugin\PaymentInstaller $installer */
        $installer = $this->container->get('shopware.plugin_payment_installer');

        $options = [
            'name' => 'ameria_payment_cc',
            'description' => 'Credit Card',
            'action' => 'PaymentAmeria',
            'active' => 0,
            'position' => 0,
            'additionalDescription' =>
                '<img src="custom/plugins/GenPaymentAmeria/Images/ameria-logo.jpg" alt="ameria-logo"/>'
                . '<div id="payment_desc">'
                . '  Pay save and secured by credit card with our Ameria bank payment provider.'
                . '</div>'
        ];

        $installer->createOrUpdate($context->getPlugin(), $options);
        
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_order_attributes', 'ameria_payment_id', 'string');
                
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_order_attributes']);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), true);
    }

    /**
     * @param Payment[] $payments
     * @param $active bool
     */
    private function setActiveFlag($payments, $active)
    {
        $em = $this->container->get('models');

        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        $em->flush();
    }
}
