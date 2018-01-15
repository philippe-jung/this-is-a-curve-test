<?php

namespace Curve\Module\Service\Users;

use Curve\Config;
use Curve\Module\Service\Response\Error;
use Curve\Module\Service\Response\Response;
use Curve\Module\Service\Response\Success;
use Curve\Module\Service\AbstractService;
use Curve\Module\Service\Exception\Exception;

class Get extends AbstractService
{
    protected $requiredParams = array(
        'username' => self::FORMAT_STRING,
    );

    /**
     * @return Response
     * @throws Exception
     * @throws \Curve\Exception\ConfigNotFound
     */
    public function execute(): Response
    {
        $contribs = Config::getConfigParam('mockup.contribs');
        $userName = $this->getValidatedParam('username');

        // check errors
        if (!array_key_exists($userName, $contribs)) {
            return new Error('No such user');
        }
        if (empty($contribs[$userName])) {
            return new Error('User has no contribution');
        }

        // build the return
        $repos = Config::getConfigParam('mockup.repos');
        $return = array();
        foreach ($contribs[$userName] as $repoId) {
            if (empty($repos[$repoId])) {
                throw new Exception('Unknown repo ' . $repoId);
            }
            $return[] = array(
                'id'   => $repoId,
                'name' => $repos[$repoId],
            );
        }

        return new Success($return);
    }
}