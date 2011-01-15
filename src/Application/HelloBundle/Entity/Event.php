<?php

namespace Application\HelloBundle\Entity;

/**
 * @orm:Entity
 */
class Event
{
    /**
     * @orm:Id
     * @orm:GeneratedValue
     * @orm:Column(type="integer")
     * 
     * @var     integer
     */
    private $id;

    /**
     * @orm:Column(type="string", length="128")
     * 
     * @var     string
     */
    private $name;

    /**
     * @orm:Column(name="created_at", type="date")
     *
     * @var     \DateTime
     */
    private $createdAt;

    public function __construct()
    {
        $this->name = md5(time());
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getName()
    {
        return $this->name;
    }
}
