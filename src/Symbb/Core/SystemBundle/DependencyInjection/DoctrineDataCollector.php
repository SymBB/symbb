<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;

class DoctrineDataCollector extends DataCollector
{

    /**
     * @var \Doctrine\DBAL\Logging\DebugStack
     */
    protected $logger;

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
        $this->logger = new \Doctrine\DBAL\Logging\DebugStack();
        $loggerChain = $em
            ->getConnection()
            ->getConfiguration()
            ->getSQLLogger();
        $loggerChain->addLogger($this->logger);
    }

    public function getCount()
    {
        return $this->data["count"];
    }

    public function getQueries()
    {
        return $this->data["queries"];
    }


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $problematicQueries = array();
        $queries = $this->logger->queries;
        $queries = $this->sanitizeQueries($queries);
        foreach ($queries as $queryData) {
            $problematic = false;
            $queryData["problems"] = array();
            if ($queryData["executionMS"] > 10) {
                $problematic = true;
                $queryData["problems"][] = "Query is slow.";
            }
            if (
                $queryData["explainable"] &&
                strpos($queryData["sql"], "TRANSACTION") === false &&
                strpos($queryData["sql"], "COMMIT") === false &&
                strpos($queryData["sql"], "ROLLBACK") === false
            ) {
                try {
                    $explainData = $this->explainQuery($queryData["sql"], $queryData["params"]);
                    foreach ($explainData as $explain) {
                        if (
                            $explain["type"] == "ALL"
                        ) {
                            $problematic = true;
                            $queryData["problems"][] = "type is ALL";
                        }
                    }
                } catch (\Exception $e) {

                }
            }
            if ($problematic) {
                $problematicQueries[] = $queryData;
            }
        }

        $this->data = array(
            "count" => count($problematicQueries),
            "queries" => $problematicQueries
        );
    }

    public function explainQuery($sql, $params)
    {
        $rows = $this->em->getConnection()->fetchAll("EXPLAIN " . $sql, $params);
        return $rows;
    }

    public function getName()
    {
        return 'symbb_doctrine';
    }

    private function sanitizeQueries($queries)
    {
        foreach ($queries as $i => $query) {
            $queries[$i] = $this->sanitizeQuery($query);
        }
        return $queries;
    }

    private function sanitizeQuery($query)
    {
        $query['explainable'] = true;
        $query['params'] = (array)$query['params'];
        foreach ($query['params'] as $j => &$param) {
            if (isset($query['types'][$j])) {
                // Transform the param according to the type
                $type = $query['types'][$j];
                if (is_string($type)) {
                    $type = Type::getType($type);
                }
                if ($type instanceof Type) {
                    $query['types'][$j] = $type->getBindingType();
                }
            }
            list($param, $explainable) = $this->sanitizeParam($param);
            if (!$explainable) {
                $query['explainable'] = false;
            }
        }
        return $query;
    }

    /**
     * Sanitizes a param.
     *
     * The return value is an array with the sanitized value and a boolean
     * indicating if the original value was kept (allowing to use the sanitized
     * value to explain the query).
     *
     * @param mixed $var
     *
     * @return array
     */
    private function sanitizeParam($var)
    {
        if (is_object($var)) {
            return array(sprintf('Object(%s)', get_class($var)), false);
        }
        if (is_array($var)) {
            $a = array();
            $original = true;
            foreach ($var as $k => $v) {
                list($value, $orig) = $this->sanitizeParam($v);
                $original = $original && $orig;
                $a[$k] = $value;
            }
            return array($a, $original);
        }
        if (is_resource($var)) {
            return array(sprintf('Resource(%s)', get_resource_type($var)), false);
        }
        return array($var, true);
    }

}