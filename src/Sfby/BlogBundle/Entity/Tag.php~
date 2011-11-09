<?php

namespace Sfby\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Sfby\BlogBundle\Repository\TagRepository")
 * @ORM\Table(name="sfby_tag")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag 
{
    /**
     * @var int $id
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Gedmo\Sluggable(slugField="slug")
     */
    protected $name;
    
    /**
     * @Gedmo\Slug
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;
    
    /**
     * @ORM\ManyToMany(targetEntity="Blog", inversedBy="tags")
     * @ORM\JoinTable(name="sfby_tag2blog")
     */
    protected $blogs;
    
    
    public function __construct()
    {
        $this->blogs = new ArrayCollection();
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add blogs
     *
     * @param Sfby\BlogBundle\Entity\Blog $blogs
     */
    public function addBlog(\Sfby\BlogBundle\Entity\Blog $blogs)
    {
        $this->blogs[] = $blogs;
    }

    /**
     * Get blogs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBlogs()
    {
        return $this->blogs;
    }
}