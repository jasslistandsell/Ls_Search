<?php declare(strict_types=1);

namespace Plugin\ls_search;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\Form;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use stdClass;

/**
 * Class TestHelper
 * @package Plugin\ls_search
 */
class TestHelper
{
    /**
     * @var DbInterface
     */
    private $db;

    /**
     * @var PluginInterface
     */
    private $extension;

    /**
     * @var JTLCacheInterface
     */
    private $cache;

    /**
     * TestHelper constructor.
     * @param PluginInterface   $extension
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     */
    public function __construct(PluginInterface $extension, DbInterface $db, JTLCacheInterface $cache)
    {
        $this->extension = $extension;
        $this->db        = $db;
        $this->cache     = $cache;
    }


    /**
     * fetch, render and insert template into DOM
     *
     * @return $this
     */
    public function insertStuff(): self
    {
        $smarty = Shop::Smarty();
        // assign the calculated value of PI for smarty
        $dbRes    = $this->getSomethingFromDB();
        $someText = null;
        $file     = $this->extension->getPaths()->getFrontendPath() . 'template/frontend_test.tpl';
        if (isset($dbRes->text)) {
            $someText = $dbRes->text;
        }
        $config = $this->extension->getConfig();
        $smarty->assign('some_text', $someText)
            ->assign(
                'lang_var_1',
                \vsprintf(
                    $this->extension->getLocalization()->getTranslation('xmlp_lang_var_1'),
                    [$this->calculatePi(), $this->extension->getMeta()->getVersion()]
                )
            )
            ->assign('exampleConfigVars', $this->extension->getConfig()->getOptions());
        // get user options for inserting the template
        $function = $config->getValue('ls_search_pqfunction');
        $selector = $config->getValue('ls_search_pqselector');
        // render template and call pq
        \pq($selector)->$function($smarty->fetch($file));

        // make this method chainable
        return $this;
    }

    /**
     * get a db row via NiceDB instance
     *
     * @return stdClass
     */
    public function getSomethingFromDB(): stdClass
    {
        return $this->db->selectSingleRow('ls_search_foo', 'foo', 22);
    }

    /**
     * insert a new row into our custom DB table
     *
     * @param int $random
     * @return int
     */
    public function insertSomeThingIntoDB(int $random): int
    {
        $myObject       = new stdClass();
        $myObject->foo  = $random;
        $myObject->bar  = 2;
        $myObject->text = 'Hello World!';

        return $this->db->insert('ls_search_foo', $myObject);
    }

    /**
     * modify a string with configured text
     *
     * @param string $text
     * @return string
     */
    public function modify(string $text): string
    {
        $modification = $this->extension->getConfig()->getValue('modification_text');

        return $text . ((\is_string($modification) && \strlen($modification) > 0)
                ? (' ' . $modification)
                : '');
    }

    /**
     * @param array $post
     * @return bool
     */
    public function savePostToDB(array $post): bool
    {
        $validToken = Form::validateToken();
        if (!$validToken || empty($post['jtl-text']) || !isset($post['jtl-number'], $post['jtl-number-two'])) {
            return false;
        }
        $data       = new stdClass();
        $data->foo  = (int)$post['jtl-number'];
        $data->bar  = (int)$post['jtl-number-two'];
        $data->text = $post['jtl-text'];

        return $this->db->insert('ls_search_foo', $data) > 0;
    }
}
