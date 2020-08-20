<?php
namespace GenArticleOrder\Bootstrap;

use Doctrine\DBAL\Connection;

class Installer
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->createTables();
        
        $sql = "SELECT COUNT(1) IndexIsThere FROM INFORMATION_SCHEMA.STATISTICS
				WHERE table_schema=DATABASE() AND table_name='gen_articles_categories' AND index_name='order';";
        $check = Shopware ()->Db ()->fetchOne ( $sql );
        if (empty ( $check )) {
        	$sql = "ALTER TABLE `gen_articles_categories` ADD INDEX `order` (`order`)";
        	Shopware ()->Db ()->query ( $sql );
        }
        
        $sql="SELECT COUNT(1)
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'gen_articles_categories'
			AND table_schema = DATABASE()
			AND column_name = 'dropped'";
        $check = Shopware ()->Db ()->fetchOne ( $sql );
        if (empty ( $check )) {
        	$sql = "ALTER TABLE gen_articles_categories ADD COLUMN `dropped` int(11) NULL";
        	Shopware()->Db()->query($sql);
        }

        return true;
    }

    /**
     * Creates all tables by loading the SQL files from /Assets/Installation/*.sql
     */
    private function createTables()
    {
        $file = __DIR__ . '/Assets/create_tables.sql';

        $fileContent = file_get_contents($file);
        $this->connection->executeQuery($fileContent);
    }
}
