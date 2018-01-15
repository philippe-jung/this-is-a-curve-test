<?php

namespace Curve\Module\Service\Distance\Tree;

use Curve\Module\Service\Exception\Exception;

class Helper
{
    /**
     * Max number of levels allowed when building the tree of connections
     */
    const MAX_DISTANCE = 10;

    /**
     * @var string
     */
    protected $user1;

    /**
     * @var string
     */
    protected $user2;

    /**
     * Tree of all connections between users
     *
     * @var array
     */
    protected $connectionTree = array();

    /**
     * @var GithubHelper
     */
    protected $githubHelper;

    public function __construct(string $user1, string $user2)
    {
        $this->user1 = $user1;
        $this->user2 = $user2;
        $this->githubHelper = new GithubHelper();
    }

    /**
     * @return Element
     * @throws Exception
     * @throws \Curve\Exception\ConfigNotFound
     */
    public function searchConnection(): Element
    {
        // no need to lookup anything if both users are the same
        if ($this->user1 === $this->user2) {
            throw new Exception('You must provide 2 different users');
        }

        // get the repos for both users
        $repos1 = $this->githubHelper->getReposForUser($this->user1);
        $repos2 = $this->githubHelper->getReposForUser($this->user2);

        // build the root elements of the connection tree
        $this->connectionTree = array();
        foreach ($repos1 as $repoId => $repoName) {
            $rootElement = new Element($this->user1, $repoName, $repoId);
            $this->connectionTree[0][$repoName] = $rootElement;

            // check for direct match (which allows not to perform any extra query)
            if (array_key_exists($repoId, $repos2)) {
                // return an element with the details of the connection
                $element = new Element($this->user2, $repoName, $repoId, $rootElement);
                return $element;
            }
        }

        // build the tree of direct connections (add users connected via the root repos)
        foreach ($repos1 as $repoId => $repoName) {
            $users = $this->githubHelper->getContributorsForRepo($repoId);
            foreach ($users as $oneUser) {
                if ($oneUser != $this->user1) {
                    $this->connectionTree[1][] = new Element($oneUser, $repoName, $repoId, $this->connectionTree[0][$repoName]);
                }
            }
        }

        // as level 1 has been constructed above, we start our loop from 2
        $level = 2;
        do {
            $found = $this->buildLevel($level);
            if (empty($this->connectionTree[$level])) {
                // no new level was added to the tree: we have reached all the leaves, and the tree can not grow
                // it means the 2 users have no connection
                throw new Exception('Users have no connection');
            }
            if ($found) {
                // an Element was returned, which means we found user2
                return $found;
            }
            $level++;
        } while($level <= self::MAX_DISTANCE);

        throw new Exception('Aborting: distance between 2 users is greater than max allowed (' . self::MAX_DISTANCE . ')');
    }

    /**
     * Build the connections for given level
     *
     * @param $level
     * @return bool|Element
     * @throws Exception
     * @throws \Curve\Exception\ConfigNotFound
     */
    protected function buildLevel($level)
    {
        // check that the previous level exists
        if (empty($this->connectionTree[$level - 1])) {
            return false;
        }
        $parentElements = $this->connectionTree[$level - 1];

        // foreach user of the previous level, retrieve their repo
        foreach ($parentElements as $oneParentElement) {
            $repos = $this->githubHelper->getReposForUser($oneParentElement->getUserName());
            $ignoreUsers = $oneParentElement->getAllParentUsers();

            foreach ($repos as $repoId => $repoName) {
                if (empty($details['reposUsed'][$repoId])) {
                    // foreach repo that has not been used yet in that branch, we get users
                    $users = $this->githubHelper->getContributorsForRepo($repoId);

                    foreach ($users as $oneUser) {
                        if (!in_array($oneUser, $ignoreUsers)) { // we ignore all users that are already part of the tree
                            $element = new Element($oneUser, $repoName, $repoId, $oneParentElement);
                            $this->connectionTree[$level][] = $element;

                            if ($oneUser == $this->user2) {
                                // no need to continue: we find our user!
                                return $element;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }
}