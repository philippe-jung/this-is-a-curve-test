<?php

namespace Curve\Module\Service;


use Curve\Module\Service\Exception;
use Curve\Module\Service\Response\Response;
use Curve\Request;

abstract class AbstractService
{
    const FORMAT_STRING = 'string';
    const FORMAT_INT = 'integer';
    const FORMAT_FLOAT = 'float';
    const FORMAT_EMAIL = 'email';
    const FORMAT_PHONE = 'phone number';
    const FORMAT_COORDINATE = 'coordinate';

    abstract public function execute(): Response;

    /**
     * "Name => Value" array of validated parameters that were submitted
     *
     * @var array
     */
    private $params;

    /**
     * Description of the required parameters and their type
     * Format: array(
     *      'paramName' => format (one of the FORMAT_* constant)
     *
     * @var array
     */
    protected $requiredParams = array();

    /**
     * AbstractService constructor.
     * @throws Exception\InvalidType
     * @throws Exception\MissingParam
     * @throws \Exception
     */
    public function __construct()
    {
        $this->validateParams();
    }

    /**
     * Validate the parameters submitted
     *
     * @throws Exception\InvalidType
     * @throws Exception\MissingParam
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = Request::getParams();

        foreach ($this->requiredParams as $paramName => $paramType) {
            // check that the param has been submitted
            if (!array_key_exists($paramName, $params) || "" === $params[$paramName]) {
                throw new Exception\MissingParam($paramName);
            }
            $submittedParam = $params[$paramName];

            $goodType = true;
            // check that the param type is valid
            switch ($paramType) {
                case self::FORMAT_STRING:
                    if (!is_string($submittedParam)) {
                        $goodType = false;
                    }
                    break;

                case self::FORMAT_PHONE:
                    if (!preg_match('/^\+[0-9]*$/i', $submittedParam)) {
                        $goodType = false;
                    }
                    break;

                case self::FORMAT_EMAIL:
                    if (!filter_var($submittedParam, FILTER_VALIDATE_EMAIL)) {
                        $goodType = false;
                    }
                    break;

                case self::FORMAT_INT:
                    if (!filter_var($submittedParam, FILTER_VALIDATE_INT)) {
                        $goodType = false;
                    }
                    break;

                case self::FORMAT_COORDINATE:
                case self::FORMAT_FLOAT:
                    if (!filter_var($submittedParam, FILTER_VALIDATE_FLOAT)) {
                        $goodType = false;
                    }
                    break;

                default:
                    throw new \Exception('Unknown type ' . $paramType);
            }

            if (!$goodType) {
                throw new Exception\InvalidType($paramName . ' should be a valid ' . $paramType);
            }

            // save the param
            $this->params[$paramName] = $submittedParam;
        }
    }

    /**
     * Get the value of given validated param
     *
     * @param $name
     * @return string
     * @throws Exception\Exception
     */
    protected function getValidatedParam($name): string
    {
        if (!array_key_exists($name, $this->params)) {
            throw new Exception\Exception('Unknown param ' . $name);
        }
        return $this->params[$name];
    }
}