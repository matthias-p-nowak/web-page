<?php
/**
 *
 */
namespace WebApp\Db;

use Generator;
use PDO;
use PDOStatement;

/**
 * adding backquotes so the items can be used in an SQL
 */
function AddBackQuotes(array $input)
{
    $ret = [];
    foreach ($input as $v) {
        $ret[] = '`' . $v . '`';
    }
    return $ret;
}

/**
 * as placeholder names
 */
function PrependColon(array $input)
{
    $ret = [];
    foreach ($input as $v) {
        $ret[] = ':' . $v;
    }
    return $ret;
}

/**
 * Creating clause items
 */
function MakeClause(array $input)
{
    $ret = [];
    foreach ($input as $v) {
        $ret[] = '`' . $v . '`=:' . $v;
    }
    return $ret;
}

/**
 *  a database context similar to the one in EF6
 */
class DbCtx
{
    private array $allRowDetails;
    public \PDO $pdo;

    // keeping a single instance
    private static $instance;
    private $prefix;

    /**
     * returns the same instance
     */
    public static function GetInstance(): DbCtx
    {
        return static::$instance ?? new static();
    }

    /**
     * use GetInstance instead
     */
    private function __construct()
    {
        global $config;
        if (!isset($config)) {
            error_log('config does not exist');
            die('no config found');
        }
        // error_log('constructing a DbCtx');
        $dbCfg = $config->database;
        $this->pdo = new \PDO('mysql:host=' . $dbCfg->server . ';dbname=' . $dbCfg->database,
            $dbCfg->user, $dbCfg->password);
        static::$instance = $this;
        $this->prefix = $dbCfg->prefix . '_' ?? '';
    }

    /**
     * This takes the database.sql file in the same directory divides it into chunks.
     * Chunks are separated by lines of the form "-- 2022-01-01 comment" with the appropriate date and comment
     * Chunks are executed in separate queries, hence not all of them will fail
     * @return void
     */
    public function Upgrade(): void
    {
        global $config;
        $content = file_get_contents(__DIR__ . '/database.sql');
        error_log('replacing ${prefix} with ' . $this->prefix);
        $content = str_replace('${prefix}', $this->prefix, $content);
        // splitting the file at each occurance '^-- 2020-01-01 or similar dates'
        $pattern = '/^(-- \d{4}-\d{2}-\d{2}.*)$/m';
        $result = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        // running each part as a batch, it should not fail
        $lastSuccess = 'start of file';
        foreach ($result as $sqlParts) {
            $sqlParts = trim($sqlParts);
            if (preg_match($pattern, $sqlParts) == 1) {
                // this is a line '-- 2020-01-01...'
                error_log("db update up to $sqlParts");
                $lastSuccess = $sqlParts;
            } else {
                if ($sqlParts == '') {
                    // empty queries will fail, hence skipping them
                    continue;
                }
                try {
                    $this->pdo->exec($sqlParts);
                } catch (\PDOException $e) {
                    $msg = $e->getMessage();
                    error_log("got an exception $msg");
                    error_log($sqlParts);
                    break;
                }
            }
        }
        header('web-page: database upgraded');
        error_log('database update complete');
    }

    /**
     * For storing, we need to know what can be stored in the database
     * @param string $tableName name of table to get details of
     * @return <mixed>,object>
     */
    public function GetRowDetails(string $tableName): mixed
    {
        if (isset($this->allRowDetails[$tableName])) {
            return $this->allRowDetails[$tableName];
        }
        // retrieve the columns to store, if available
        $sql = 'Select * from `' . $this->prefix . $tableName . '` limit 0';
        $stmt = $this->pdo->query($sql);
        $columnCount = $stmt->columnCount();
        $rowDetails = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $ci = $stmt->getColumnMeta($i);
            $name = $ci['name'];
            $rowDetails[$name] = (object) $ci;
        }
        $this->allRowDetails[$tableName] = $rowDetails;
        return $rowDetails;
    }

    /**
     * Requires the row to be a class instance of the same name as the table.
     * Executes a replace in the database.
     * @return void
     * @param mixed $row
     */
    public function StoreRow($row): void
    {
        $tableName = basename(str_replace('\\', '/', get_class($row)));
        // find out what to store
        $rowDetails = $this->GetRowDetails($tableName);
        $columns2store = array_keys($rowDetails);
        foreach ($columns2store as $pos => $propName) {
            if (!property_exists($row, $propName)) {
                unset($columns2store[$pos]);
            }
        }
        // constructing the SQL
        $sql = 'Replace into `' . $this->prefix . $tableName . '`( ' .
        implode(', ', AddBackQuotes($columns2store)) . ' ) ' .
        ' values ( ' . implode(', ', PrependColon($columns2store)) . ' )';
        $stmt = $this->pdo->prepare($sql);
        foreach ($columns2store as $name) {
            if (!$stmt->bindValue(':' . $name, $row->$name, $rowDetails[$name]->pdo_type)) {
                error_log('error in parameter binding in DbCtx StoreRow name=' . $name);
                return;
            }
        }
        error_log("storing into $tableName using $sql");
        $r = $stmt->execute();
    }

    /**
     * deletes the rows that match the details
     * @return void
     * @param mixed $row
     */
    public function DeleteRow($row): void
    {
        $tableName = basename(str_replace('\\', '/', get_class($row)));
        $rowDetails = $this->GetRowDetails($tableName);
        $columns2store = array_keys($rowDetails);
        foreach ($columns2store as $pos => $propName) {
            if (!property_exists($row, $propName)) {
                unset($columns2store[$pos]);
            } else if (\is_null($row->$propName)) {
                unset($columns2store[$pos]);
            }
        }
        // constructing the SQL
        $sql = 'DELETE FROM `' . $this->prefix . $tableName . '` where ' . implode(' and ', MakeClause($columns2store));
        $stmt = $this->pdo->prepare($sql);
        foreach ($columns2store as $col) {
            $stmt->bindValue(':' . $col, $row->$col);
        }
        if (!$stmt->execute()) {
            error_log(__FILE__ . ':' . __LINE__ . ' deleting row failed ' . $sql . ' row=' . print_r($row, true));
        }
    }

    /**
     * Executes select and returns statement to read from
     * @return PDOStatement|bool
     * @param array<int,mixed> $criteria
     */
    private function FetchStmt(string $tableName, array $criteria): PDOStatement | bool
    {

        $sql = 'select * from `' . $this->prefix . $tableName . '`';
        if (count($criteria) > 0) {
            $keys = array_keys($criteria);
            $clause = MakeClause($keys);
            $sql .= ' where ' . implode(' and ', $clause);
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($criteria as $key => $value) {
            if ($stmt->bindValue(':' . $key, $value)) {

            } else {
                error_log(__FILE__ . ':' . __LINE__ . ' binding parameter ' . $key . ' failed');
            };
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Basically a select, returns objects.
     * @param array<int,mixed> $criteria
     * @param string $tableName the table to get the rows from
     * @return Generator<mixed> of Objects with a classname equal to that tableName
     */
    public function FindRows(string $tableName, array $criteria = []): Generator
    {
        $stmt = $this->FetchStmt($tableName, $criteria);
        while ($res = $stmt->fetchObject(__NAMESPACE__ . '\\' . $tableName)) {
            $res->ctx = $this;
            yield $res;
        }
    }

    /**
     * Returns a single matching object - does not check if more match.
     * @param array<int,mixed> $criteria
     * @return mixed an object of the class with the same name
     */
    public function FindRow(string $tableName, array $criteria): mixed
    {
        $stmt = $this->FetchStmt($tableName, $criteria);
        $obj = $stmt->fetchObject(__NAMESPACE__ . '\\' . $tableName);
        return $obj;
    }

    /**
     * Fetches from database, need ${prefix} before table names.
     * @param string $sql sql-statement to use
     * @return Generator<mixed> of objects
     */
    public function FetchRows(string $sql): Generator
    {
        $sql = str_replace('${prefix}', $this->prefix, $sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($res = $stmt->fetchObject()) {
            yield $res;
        }
    }

    /**
     * @param string $sql SQL statement to execute
     * @param array<string,mixed> $criteria
     * @return void
     */
    public function ExecuteSQL(string $sql, array $criteria): void
    {
        $sql = str_replace('${prefix}', $this->prefix, $sql);
        if (count($criteria) > 0) {
            $keys = array_keys($criteria);
            $clause = MakeClause($keys);
            $sql .= ' where ' . implode(' and ', $clause);
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($criteria as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
    }

    /**
     * @param string @sql complete SQL query with ${prefix}
     * @param string $params array<string,mixed> $params
     * @return void
     */
    public function ExecuteSqlWithParams(string $sql, array $params): void
    {
        $sql = str_replace('${prefix}', $this->prefix, $sql);
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
    }
}

// error_log(__FILE__ . ' read');
