<?php

namespace Curve\Module\Service\Repos;

use Curve\Config;
use Curve\Module\Service\Response\Error;
use Curve\Module\Service\Response\Response;
use Curve\Module\Service\Response\Success;
use Curve\Module\Service\AbstractService;
use Curve\Module\Service\Exception\Exception;

class Get extends AbstractService
{
    protected $requiredParams = array(
        'id' => self::FORMAT_INT,
    );

    /**
     * @return Response
     * @throws Exception
     * @throws \Curve\Exception\ConfigNotFound
     */
    public function execute(): Response
    {
        $contribs = Config::getConfigParam('mockup.contribs');
        $repos = Config::getConfigParam('mockup.repos');
        $repoId = $this->getValidatedParam('id');

        // check errors
        if (!array_key_exists($repoId, $repos)) {
            return new Error('No such repo');
        }
        if (empty($repos[$repoId])) {
            return new Error('Noone has contributed to this repo');
        }

        // build the return
        $return = array();
        foreach ($contribs as $userName => $reposForUser) {
            if (in_array($repoId, $reposForUser)) {
                // if we found our repo in the list for the user
                $return[] = $userName;
            }
        }

        return new Success($return);
    }
}