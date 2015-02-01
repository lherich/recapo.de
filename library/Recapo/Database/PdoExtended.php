<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Database;

/**
 * Extended PDO class, which simplifies some procedure within the work with PDO in webapplications.
 */
class PdoExtended extends \PDO
{
    /**
     * Offers a simplification for pagination
     *
     * Executes a given prepared PDO statement, which have at least to
     * placeholders: ":START" and ":OFFSET". Furthermore this functions accepts
     * a associative array $params. Every value is binded by its key to the
     * corresponding named placeholder in the statement. If the index "START"
     * or "OFFSET" exists, the value is casted to integer and bind to the
     * "START" respectively "OFFSET" placeholder of the statement. If one of
     * them did not exists in the array, it is set to the parameter
     * $defaultStart (default 0) respectively $defaultOffset (default 10, maximum 101).
     *
     * @param  PDOStatement $sth
     * @param  array        $params
     * @param  int          $defaultStart
     * @param  int          $defaultOffset
     * @return array
     */
    public function executePagination(
        &$sth,
        $params = array(),
        $defaultStart = 0,
        $defaultOffset = 10
    ) {
        // check if isset and 0 <= START && 0 < OFFSET < 101, else use default
        if (isset($params['START'], $params['OFFSET'])
            && $params['START'] >= 0
            && $params['OFFSET'] > 0
            && $params['OFFSET'] < 101
        ) {
            $sth->bindValue(':START', (int) $params['START'], \PDO::PARAM_INT);
            $sth->bindValue(':OFFSET', (int) $params['OFFSET'], \PDO::PARAM_INT);
        } else {
            $sth->bindValue(':START', (int) $defaultStart, \PDO::PARAM_INT);
            $sth->bindValue(':OFFSET', (int) $defaultOffset, \PDO::PARAM_INT);
        }
        unset($params['START']);
        unset($params['OFFSET']);

        // are there elements left in the array, which should be bind to the statement?
        if (count($params) > 0) {
            foreach ($params as $index => $value) {
                $sth->bindValue($index, $value);
            }
        }
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Offers a simplification for selecting one data set by an ID. Cast the
     * value of key "ID" or "pID" from the $params array to integer and binds it
     * to the corresponding named placeholder ":pID" in the statement. It
     * returns only one data set.
     *
     * @param  PDOStatement                             $sth
     * @param  array                                    $params
     * @return array
     * @throws \Slim\Exception\FailedParametersTransfer
     */
    public function executeByID(&$sth, $params)
    {
        // if pID did not exists, but ID do
        if (isset($params['ID']) && !isset($params['pID'])) {
            $params['pID'] = $params['ID'];
        }

        // check if pID exists and has a numeric value
        if (isset($params['pID']) && is_numeric($params['pID']) === true) {
            $sth->bindParam(':pID', $params['pID'], \PDO::PARAM_INT);
            $sth->execute();

            return $sth->fetch(\PDO::FETCH_ASSOC);
        } else {
            throw new \Slim\Exception\FailedParametersTransfer();
        }
    }

    /**
     * Offers a simplification to execute a sql query with parameters
     *
     * Prepares a given SQL query and bind all parameters from the given array to this prepared statement.
     * Afterwards the query is executed and the result is returned.
     *
     * @param  string       $sql
     * @param  array        $params
     * @return array
     */
    public function executeStatement($sql, $params = array())
    {
        $_sth = $this->prepare($sql);
        $_sth->execute($params);
        return $_sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Offers a simplification to execute a sql query with parameters
     *
     * Prepares a given SQL query and bind all parameters from the given array to this prepared statement.
     * Afterwards the query is executed and the result is returned.
     *
     * @param  string       $sql
     * @param  array        $params
     * @return array
     */

    public function createInsertAndExecute($table, $params = array())
    {
        if (is_array($params) === false or count($params) < 1) {
            return false;
        }

        $bind = ':'.implode(',:', array_keys($params));
        $sql  = 'insert into '.$table.'('.implode(',', array_keys($params)).') '.
                'values ('.$bind.')';
        $stmt = $this->prepare($sql);
        return $stmt->execute(
            array_combine(
                explode(
                    ',',
                    $bind
                ),
                array_values($params)
            )
        );
    }
}
