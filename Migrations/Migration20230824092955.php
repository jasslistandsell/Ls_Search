<?php declare(strict_types=1);
namespace Plugin\ls_search\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

/**
 * Class Migration20230824092955
 * @package Plugin\ls_search\Migrations
 */
class Migration20230824092955 extends Migration implements IMigration 
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `ls_search_ddproducts` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `kArtikel` int(10) NOT NULL,
                  `cName` varchar(255) NOT NULL,
                  `dName` varchar(255),
                  `cSeo` varchar(255),
                  `ls_oem` varchar(255),
                  `cIds` varchar(255),
                  `cArtNr` varchar(255),
                  PRIMARY KEY (`id`)
            )  ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        if ($this->doDeleteData()) {
            $this->execute('DROP TABLE IF EXISTS `ls_search_ddproducts`');
        }
    }
}
