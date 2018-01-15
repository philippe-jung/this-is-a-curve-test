<?php

namespace Curve\Module\Service\Distance;

use Curve\Module\Service\Distance\Tree\Element;
use Curve\Module\Service\Distance\Tree\Helper;
use Curve\Module\Service\Response\Response;
use Curve\Module\Service\AbstractService;
use Curve\Module\Service\Exception\Exception;
use Curve\Module\Service\Response\Success;

class Get extends AbstractService
{
    protected $requiredParams = array(
        'user1' => self::FORMAT_STRING,
        'user2' => self::FORMAT_STRING,
    );

    /**
     * @return Response
     * @throws Exception
     * @throws \Curve\Exception\ConfigNotFound
     */
    public function execute(): Response
    {
        $user1 = $this->getValidatedParam('user1');
        $user2 = $this->getValidatedParam('user2');

        $treeHelper = new Helper($user1, $user2);
        $element = $treeHelper->searchConnection();

        return $this->getSuccessResponse($user1, $element);
    }

    /**
     * Get the details of connection for given Element
     * Return all users (with the connecting repo) from the lowest level to the highest one
     *
     * @param Element $element
     * @return array
     */
    protected function getPathDetails(Element $element)
    {
        $return = array();

        do {
            $return[] = $element->getUserName() . ' (via ' . $element->getRepoName() . ')';
        } while (!empty(($element = $element->getParent())));

        return array_reverse($return);
    }

    /**
     * Create a Success Response, with distance and path details
     *
     * @param Element $element
     * @return Success
     */
    protected function getSuccessResponse($user1, Element $element)
    {
        $details = $this->getPathDetails($element);
        array_shift($details);  // we remove the level 0, as we include it manually without the repo name
        $path = $user1 . ' -> ' . implode(' -> ', $details);

        return new Success(array(
            'distance' => count($details),
            'path'     => $path
        ));
    }
}