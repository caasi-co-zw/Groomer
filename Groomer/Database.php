<?php

namespace Caasi;

/**
 * Connects you to the database server easily.
 *
 * Uses a PHP Data Object to connect you to mysql or sqlite database
 * @link https://github.com/caasi-co-zw/Groomer
 * @author Isaac <isaac@caasi.co.zw>
 * @version 1.1
 * @license MIT Opensource
 */
class Database
{
    /**
     * Stores the current pdo session
     * @var \PDO
     */
    private $database;

    /**
     * Stores the current sql statement request
     * @var \PDO_Statement
     */
    private $statement;

    /**
     * Returns a list of errors that occured
     * @var array
     */
    private $log = [];

    /**
     * Return true if the connection was successful
     */
    public $connected;

    /**
     * Connects to your database and returns true if successful.
     * This class uses a PDO (PHP Data Object) extension to connect to your database.
     * @param string $dbname The name of the database to connect to.
     * @param string $dbusername The username to be used to connect to your database.
     * @param string $dbpassword The password to connect to your database.
     * @param string $dbhost Default is set to localhost
     * @param string $engine [optional] Can only be mysql / sqlite
     * @param string $charset [optional] The charset to be used eg. utf8
     * @return bool
     */
    public function __construct(string $dbname = '', string $dbusername = '', string $dbpassword = '', string $dbhost = 'localhost',  string $engine = "mysql", string $charset = "utf8")
    {
        if (empty($dbusername) && defined('DB_USERNAME')) {
            $dbusername = DB_USERNAME;
        }
        if (empty($dbpassword) && defined('DB_PASSWORD')) {
            $dbpassword = DB_PASSWORD;
        }
        if (empty($dbhost) && defined('DB_HOST')) {
            $dbhost = DB_HOST;
        }
        if (empty($dbname) && defined('DB_NAME')) {
            $dbhost = DB_NAME;
        }
        if (empty($engine)) {
            $engine = "mysql";
        }
        if (empty($charset)) {
            $charset = "utf8";
        }

        try {

            // instantiate database
            $this->database = new \PDO(
                "$engine:host=$dbhost;dbname=$dbname;charset=$charset",
                $dbusername,
                $dbpassword,
                array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            // prevent emulation of prepared statements
            $this->database->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            // store state of the database
            $this->connected = $this->database !== null;
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
        return $this->connected;
    }

    /**
     * Sends query to the database engine
     * @param string $query The SQL query to be executed.
     */
    public function query($query)
    {
        $this->statement = $this->database->prepare($query);
        $this->execute();
        return $this;
    }

    /**
     * Perfoms a single query returning only the first resultif any, in a select query
     * @return array
     */
    public function querySingle($query)
    {
        $query .= " LIMIT 0,1";
        $this->query($query);
        return $this->getRow();
    }

    /**
     * Prepares an SQL Query
     */
    public function prepare($query)
    {
        $this->statement = $this->database->prepare($query);
        return $this;
    }

    /**
     * Bind prepared values one by one
     */
    public function bindParam($key, $value)
    {
        $this->statement->bindParam($key, $value);
        return $this;
    }

    /**
     * Bind all numeric prepared values at once
     */
    public function bindParams(...$values)
    {
        if (is_array($values[0])) {
            for ($i = 0; $i < count($values[0]); $i++) {
                $this->statement->bindParam($i + 1, $values[0][$i]);
            }
        } else {

            for ($i = 0; $i < count($values); $i++) {
                $this->statement->bindParam($i + 1, $values[$i]);
            }
        }
        return $this;
    }

    /**
     * Bind prepared values for any type
     * @param array $params A single array with keys and their respective values
     */
    public function bindValues($params = [])
    {
        foreach ($params as $key => $value) {
            if (is_string($value))
                $type = \PDO::PARAM_STR;
            elseif (is_int($value))
                $type = \PDO::PARAM_INT;
            elseif (is_bool($value))
                $type = \PDO::PARAM_BOOL;
            else
                $type = \PDO::PARAM_NULL;
            $this->statement->bindValue(':' . $key, $value, $type);
        }
        return $this;
    }

    /**
     * Executes an sql transaction
     */
    public function execute()
    {
        $this->statement->execute();
        return $this;
    }

    /**
     * Executes a transaction
     */
    public function exec()
    {
        $this->statement->execute();
    }

    /**
     * Returns the row count of
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * Returns the current database connection handle
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->database;
    }

    /**
     * Can be used to change the database handle
     */
    public function setConnection($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Returns true when connected to the database.
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Returns an associative results array
     * @return array
     */
    public function getRow()
    {
        return $this->fetch();
    }

    /**
     * Returns an array of associative results array
     * @return array
     */
    public function getRows()
    {
        return $this->fetchAll();
    }

    /**
     * Returns the numbers of rows found.
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount();
    }

    /**
     * Returns all error messages logged
     * @return array
     */
    public function getErrorMsg()
    {
        return $this->log;
    }

    /**
     * Initializes the transaction
     * @return bool
     */
    public function startTransaction()
    {
        return $this->database->beginTransaction();
    }

    /**
     * Returns true if in an SQL transaction
     * @return bool
     */
    public function inTransaction()
    {
        return $this->database->inTransaction();
    }

    /**
     * Rolls back an SQL transaction and returns true when successfull
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->database->rollback();
    }

    /**
     * Commits an SQL transaction and returns true when successfull
     * @return bool
     */
    public function endTransaction()
    {
        $this->has_executed_transaction = true;
        return $this->database->commit();
    }
    /*
     * Last Insert Id
     * To Get Last Insert Id After Use Insert Model
     */

    public function lastInsertId()
    {
        return $this->database->lastInsertId();
    }
    /*
     * Close Databse Connect
     * Use It When you Want Close Connection
     */
    public function __destruct()
    {
        // Setting the handler to NULL closes the connection propperly
        $this->database = NULL;
    }

    /**
     * Fetchs and returns data from the database server
     * @return array
     */
    private function fetch()
    {
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * You Can Use it To get All Rows As an array
     * @return array
     */
    private function fetchAll()
    {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Used to log errors
     */
    private function log($log)
    {
        $this->log[] =  $log;
    }
}
