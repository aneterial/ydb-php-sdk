<?php

namespace YdbPlatform\Ydb;

use Closure;
use Psr\Log\LoggerInterface;
use YdbPlatform\Ydb\Exceptions\NonRetryableException;
use YdbPlatform\Ydb\Exceptions\RetryableException;
use YdbPlatform\Ydb\Exceptions\Ydb\BadSessionException;
use YdbPlatform\Ydb\Exceptions\Ydb\SessionBusyException;
use YdbPlatform\Ydb\Exceptions\Ydb\SessionExpiredException;
use YdbPlatform\Ydb\Retry\Retry;
use YdbPlatform\Ydb\Retry\RetryParams;
use YdbPlatform\Ydb\Logger\NullLogger;

require "Version.php";

class Ydb
{
    use Traits\LoggerTrait;

    const VERSION = MAJOR.".".MINOR.".".PATCH;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var array
     */
    protected $iam_config;

    /**
     * @var Iam
     */
    protected $iam;

    /**
     * @var Discovery
     */
    protected $discovery;

    /**
     * @var Scheme
     */
    protected $scheme;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Operations
     */
    protected $operations;

    /**
     * @var Scripting
     */
    protected $scripting;

    /**
     * @var Cluster
     */
    protected $cluster;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Retry
     */
    protected $retry;

    /**
     * @param array $config
     * @param LoggerInterface|null $logger
     * @throws Exception
     */
    public function __construct($config = [], LoggerInterface $logger = null)
    {
        $this->endpoint = $config['endpoint'] ?? null;
        $this->database = $config['database'] ?? null;
        $this->iam_config = $config['iam_config'] ?? [];

        if ($logger){
            $this->logger = $logger;
        } else {
            $this->logger = new NullLogger();
        }

        if(isset($config['credentials'])){
            $this->iam_config['credentials'] = $config['credentials'];
            $config['credentials']->setLogger($this->logger());
        }

        if (!empty($config['discovery']))
        {
            $this->discover();
        }

        $this->retry = new Retry();

        $this->logger()->info('YDB: Initialized');
    }

    /**
     * @return string|null
     */
    public function endpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return string|null
     */
    public function database()
    {
        return $this->database;
    }

    public function meta(): array
    {
        $meta = [
            'x-ydb-database' => [$this->database],
            'x-ydb-sdk-build-info' => ['ydb-php-sdk/' . static::VERSION],
        ];

        if (!$this->iam()->config('anonymous'))
        {
            $meta['x-ydb-auth-ticket'] = [$this->iam()->token()];
        }

        return $meta;
    }

    /**
     * Discover available endpoints to connect to.
     *
     * @return void
     * @throws Exception
     */
    public function discover()
    {
        $endpoints = $this->discovery()->listEndpoints();
        if (!empty($endpoints))
        {
            $this->cluster()->sync((array)$endpoints);
            while ($connection = $this->cluster()->get())
            {
                if ($this->endpoint === $connection->endpoint())
                {
                    continue;
                }

                $this->logger()->info('YDB: Connecting to [' . $connection->endpoint() . ']...');
                $this->endpoint = $connection->endpoint();
                try
                {
                    $this->discovery()->whoAmI();
                    $this->logger()->info('YDB: Connected to [' . $connection->endpoint() . '] successfully');
                    return;
                }
                catch (\Exception $e)
                {
                    $this->logger()->warning('YDB: Failed to connect to [' . $connection->endpoint() . ']. Trying another...');
                    $connection->degradePriority();
                }
                $this->logger()->error('YDB: Failed to connect to any endpoints.');
                throw new Exception('YDB: Failed to connect to any endpoints.');
            }
        }
    }

    /**
     * @return Cluster
     */
    public function cluster()
    {
        if (!isset($this->cluster))
        {
            $this->cluster = new Cluster($this);
        }

        return $this->cluster;
    }

    /**
     * @return string
     */
    public function token()
    {
        return $this->iam()->token();
    }

    /**
     * @return Iam
     */
    public function iam()
    {
        if (!isset($this->iam))
        {
            $this->iam = new Iam($this->iam_config, $this->logger);
        }

        return $this->iam;
    }

    /**
     * @return Discovery
     */
    public function discovery()
    {
        if (!isset($this->discovery))
        {
            $this->discovery = new Discovery($this, $this->logger);
        }

        return $this->discovery;
    }

    /**
     * @return Table
     */
    public function table()
    {
        if (!isset($this->table))
        {
            $this->table = new Table($this, $this->logger, $this->retry);
        }

        return $this->table;
    }

    /**
     * @return Scheme
     */
    public function scheme()
    {
        if (!isset($this->scheme))
        {
            $this->scheme = new Scheme($this, $this->logger);
        }

        return $this->scheme;
    }

    /**
     * @return Operations
     */
    public function operations()
    {
        if (!isset($this->operations))
        {
            $this->operations = new Operations($this, $this->logger);
        }

        return $this->operations;
    }

    /**
     * @return Scripting
     */
    public function scripting()
    {
        if (!isset($this->scripting))
        {
            $this->scripting = new Scripting($this, $this->logger);
        }

        return $this->scripting;
    }

    /**
     * @param RetryParams $params
     */
    public function setRetryParams(RetryParams $params): void
    {
        $this->retry = $this->retry->withParams($params);
    }

    /**
     * @param Closure $userFunc
     * @param bool $idempotent
     * @param RetryParams|null $params
     * @return mixed
     * @throws NonRetryableException
     * @throws RetryableException
     */
    public function retry(Closure $userFunc, bool $idempotent = false, RetryParams $params = null){
        return $this->retry->withParams($params)->retry(function () use ($userFunc){
            try{
                $result = $userFunc($this);
                return $result;
            } catch (Exception $exception) {
                throw $exception;
            }
        }, $idempotent);
    }
}
