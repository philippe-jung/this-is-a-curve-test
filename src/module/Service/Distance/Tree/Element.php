<?php

namespace Curve\Module\Service\Distance\Tree;

/**
 * Represent a connection (user and repo)
 * Point to a parent Element to build a full tree of connections
 */
class Element
{
    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $repoName;

    /**
     * @var int
     */
    protected $repoId;

    /**
     * @var Element
     */
    protected $parent;

    /**
     * Element constructor.
     * @param string $userName
     * @param string $repoName
     * @param int $repoId
     * @param Element|null $parent
     */
    public function __construct(string $userName, string $repoName, int $repoId, Element $parent = null)
    {
        $this->userName = $userName;
        $this->repoName = $repoName;
        $this->repoId = $repoId;
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getRepoName()
    {
        return $this->repoName;
    }

    /**
     * @return mixed
     */
    public function getRepoId()
    {
        return $this->repoId;
    }

    /**
     * @return Element|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string[]
     */
    public function getAllParentUsers(): array
    {
        // add the userName of current element
        $return = array($this->getUserName());

        // and add the userName of all parent elements
        $parent = $this->getParent();
        while (!empty($parent)) {
            $return[] = $parent->getUserName();
            $parent = $parent->getParent();
        }

        return $return;
    }
}