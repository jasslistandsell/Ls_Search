<?php declare(strict_types=1);


namespace Plugin\ls_search;

use ArtikelHelper;
use Exception;
use JTL\Cron\JobQueue;
use JTL\DB\DbInterface;
use JTL\DB\ReturnType;
use JTL\Filter\Items\Sort;
use JTL\Filter\Option;
use JTL\Filter\SearchResults;
use JTL\Helpers\Request;
use JTL\Plugin\PluginInterface;
use JTL\Session\Frontend;
use JTL\Shop;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Class Hooks
 * @package Plugin\ls_search
 */
class Hooks
{
    /**
     * @var DbInterface
     */
    private $db;

    /**
     * @var PluginInterface 
     */
    private $plugin;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Hooks constructor.
     * @param PluginInterface $plugin
     * @param DbInterface     $db
     * @param LoggerInterface $logger
     */
    public function __construct(PluginInterface $plugin, DbInterface $db, LoggerInterface $logger)
    {
        $this->db     = $db;
        $this->plugin = $plugin;
        $this->logger = $logger;
        require_once $plugin->getPaths()->getBasePath() . 'includes/defines_inc.php';
    }

    /**
     * @return bool
     */
    private function validateServerData(): bool
    {
        // commented for test
        return \strlen($this->plugin->getConfig()->getValue('cProjectId')) > 0
            && \strlen($this->plugin->getConfig()->getValue('cAuthHash')) > 0
            && \strlen($this->plugin->getConfig()->getValue('cServerUrl')) > 0;

        return true;
    }

    /**
     * HOOK_SMARTY_OUTPUTFILTER
     *
     * @param array $args
     * 
     */
    public function lsexec140(array $args): void
    {
        // header search
        // if(pq("body .input[name='qs'].tt-input")){
        //     $lssugTpl = Shop::Smarty()->assign('data',  "")
        //     ->fetch($this->plugin->getPaths()->getFrontendPath() . 'template/layout/ls_suggestions_container.tpl');
        //     pq('body #jtl-nav-wrapper')->append($tplData); 
        // }
    }

    

}
