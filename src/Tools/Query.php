<?php

namespace App\Tools;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

class Query
{
    /** @var int Constant used by insert() method */
    const INSERT = 1;

    /** @var int Constant used by insert() method */
    const INSERT_IGNORE = 2;

    /** @var int Constant used by insert() method */
    const REPLACE = 3;
    /**
     * @var EntityManagerInterface
     */
    private $_em;

    public function __construct(EntityManagerInterface $em){
     $this->_em=$em;

   }

    /**
     * @param $sql
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
   public function getAll($sql){
       $connection=$this->_em->getConnection();
       $stmt = $connection->prepare($sql);
       return $stmt->executeQuery()->fetchAllAssociative();
   }
    /**
     * @param $sql
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue($sql){
        $connection=$this->_em->getConnection();
        $stmt = $connection->prepare($sql);
        return $stmt->executeQuery()->fetchOne();
    }
    /**
     * @param $sql
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRow($sql){
        $connection=$this->_em->getConnection();
        $stmt = $connection->prepare($sql);
        return $stmt->executeQuery()->fetchAssociative();
    }

    /**
     * @param $table
     * @param $data
     * @param int $type
     * @return int
     * @throws Exception
     */
    public function insert($table, $data, $type=self::INSERT): int
    {
        if ($type == self::INSERT) {
            $insert_keyword = 'INSERT';
        } elseif ($type == self::INSERT_IGNORE) {
            $insert_keyword = 'INSERT IGNORE';
        } elseif ($type == self::REPLACE) {
            $insert_keyword = 'REPLACE';
        } elseif ($type == self::ON_DUPLICATE_KEY) {
            $insert_keyword = 'INSERT';
        } else {
            throw new \Exception('Bad keyword, must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE');
        }

        return $this->executeStatement($table, $data, $insert_keyword);
    }

    /**
     * Inserts a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $table Table name
     * @param array<string, mixed> $data Column-value pairs
     * @param $type
     * @return int The number of affected rows.
     *
     * @throws Exception
     */
    public function executeStatement($table, $data, $insert_keyword)
    {
        $connection=$this->_em->getConnection();
        if (empty($data)) {
            return $connection->executeStatement($insert_keyword.' INTO ' . $table . ' () VALUES ()');
        }

        $columns = [];
        $values  = [];
        $set     = [];

        foreach ($data as $columnName => $value) {
            $columns[] = $columnName;
            $values[]  = $value;
            $set[]     = '?';
        }

        return $connection->executeStatement(
            $insert_keyword.' INTO ' . $table . ' (' . implode(', ', $columns) . ')' .
            ' VALUES (' . implode(', ', $set) . ')',
            $values
        );
    }

    /**
     * @param $table
     * @param array $data
     * @param array $criteria
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function update($table, array $data, array $criteria){
        $connection=$this->_em->getConnection();
        return $connection->update($table, $data, $criteria);
    }

    /**
     * @param $table
     * @param array $criteria
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete($table, array $criteria){
        $connection=$this->_em->getConnection();
        return $connection->delete($table, $criteria);
    }
}