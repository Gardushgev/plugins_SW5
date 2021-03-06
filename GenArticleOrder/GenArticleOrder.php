<?php
/**
 * test
 */
namespace GenArticleOrder;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use GenArticleOrder\Bootstrap\Installer;
use GenArticleOrder\Bootstrap\Updater;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GenArticleOrder extends Plugin
{
    /**
     * add compiler pass
     *
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
    	$container->setParameter('gen_article_order.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $installer = new Installer(
            $this->container->get('dbal_connection')
        );

        $installer->install();
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $context)
    {
    	$updater = new Updater(
    			$this->container->get('dbal_connection')
    	);
    	
    	$updater->update($context->getCurrentVersion());
    	
        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
    	$this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(ActivateContext $context)
    {
        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->requestClearCache($context);
    }

    /**
     * @param InstallContext | UpdateContext | UninstallContext | ActivateContext | DeactivateContext $context
     */
    private function requestClearCache($context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
}
