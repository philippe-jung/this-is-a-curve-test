<?php

namespace Curve\DB;


use Curve\Config;
use Curve\DB\Exception\DuplicateEntry;
use Curve\DB\Exception\InvalidForeignKey;

/**
 * DB connection and query class
 */
class DB {

    /**
     * @var self
     */
    protected static $instance;

    /**
     * @var \PDO connection handler
     */
    protected $handler;

    /**
     * Retrieve an instance of the DB object
     *
     * @return DB
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new handler
     * @throws \Curve\Exception\ConfigNotFound
     * @throws Exception
     */
    public function __construct()
    {
        // PDO connection using db config params
        $this->handler = new \PDO(
            Config::getConfigParam('db.conn'),
            Config::getConfigParam('db.user'),
            Config::getConfigParam('db.pass')
        );

        if ($this->handler->errorCode()) {
            // there was a connection issue
            $this->handlerError();
        }
    }

    /**
     * Run given query with given binds and return an associative array of results
     *
     * @param string $query
     * @param mixed $binds
     * @return array
     * @throws Exception
     */
    public function select($query, $binds = array()): array
    {
        $stmt = $this->query($query, $binds);

        // get the results as an associative array
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }

    /**
     * Runs an insert query and returns the inserted id
     *
     * @param $query
     * @param array $binds
     * @return string
     * @throws Exception
     */
    public function insert($query, array $binds = array()): string
    {
        $this->query($query, $binds);

        return $this->handler->lastInsertId();
    }

    /**
     * Runs a query
     *
     * @param $query
     * @param array $binds
     * @return \PDOStatement
     * @throws Exception
     */
    public function query($query, $binds = array()): \PDOStatement
    {
        if (!is_array($binds)) {
            $binds = array($binds);
        }

        $stmt = $this->handler->prepare($query);
        if (false === $stmt) {
            // could not prepare the query: there is an issue at the handler level
            $this->handlerError($query, $binds);
        }

        $res = $stmt->execute($binds);
        if (false === $res) {
            // could not execute the query: there is an issue with the query itself
            $this->statementError($stmt, $query, $binds);
        }

        return $stmt;
    }


    /**
     * Deal with statement level errors
     *
     * @note $query and $binds should be somehow displayed in debug mode
     *
     * @param \PDOStatement $statement
     * @param string $query
     * @param array $binds
     * @throws Exception
     */
    protected function statementError(\PDOStatement $statement, string $query = null, array $binds = null)
    {
        $error = $statement->errorInfo();

        switch ($error[1]) {
            case Exception::CODE_DUPLICATE_ENTRY:
                $message = $error[2];
                $pieces = explode("'", $message);
                throw new DuplicateEntry($pieces[3]);
            break;

            case Exception::CODE_INVALID_FOREIGN_KEY:
                preg_match('/FOREIGN KEY \(`([a-zA-Z_]*)`\)/', $error[2], $matches);
                throw new InvalidForeignKey($matches[1]);
            break;

            default:
                throw new Exception($error[2], $error[1]);
        }
    }

    /**
     * Prepare and display an error message for the handler itself
     *
     * @param string $query
     * @param array $binds
     * @throws Exception
     */
    protected function handlerError(string $query = null, array $binds = null)
    {
        $error = $this->handler->errorInfo();
        $this->error($error[2], $query, $binds);
    }

    /**
     * Deal with errors
     *
     * @param $message
     * @param string $query
     * @param array $binds
     * @throws Exception
     */
    protected function error(string $message, string $query = null, array $binds = null)
    {
        $content = '';

        // if needed, prepare and display an error message
        $message .= PHP_EOL;

        if (!empty($query)) {
            $message .= 'Query was ' . $query . PHP_EOL;
        }
        if (!empty($binds)) {
            $message .= 'Binds: ' . PHP_EOL . print_r($binds, true);
        }

        $content .= $message . PHP_EOL;


        throw new Exception($content);
    }
}