<?php

namespace Sfby\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Sfby\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Sfby\BlogBundle\Repository\BlogRepository")
 * @ORM\Table(name="sfby_blog")
 * @ORM\HasLifecycleCallbacks()
 */
class Blog
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
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sfby\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="blog.error.name_is_blank")
     * @Assert\MinLength(limit=2, message="blog.error.name_short")
     * @Assert\MaxLength(limit=255, message="blog.error.name_long")
     * @Gedmo\Sluggable(slugField="slug")
     */
    protected $title;
    
    /**
     * @var string $keywords
     *
     * @ORM\Column(name="keywords", type="text")
     * @Assert\MaxLength(limit=255, message="blog.error.keywords_long")
     */
    protected $keywords;
    
    /**
     * @var string $short_text
     *
     * @ORM\Column(name="short_text", type="text")
     * @Assert\NotBlank(message="blog.error.text_is_blank")
     */
    protected $short_text;
    
    /**
     * @var string $text
     *
     * @ORM\Column(name="text", type="text")
     * @Assert\NotBlank(message="blog.error.text_is_blank")
     */
    protected $text;

    /**
     * @Gedmo\Slug
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * 
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="blogs")
     * @ORM\JoinTable(name="sfby_tag2blog")
     */
    protected $tags;
    
    /**
     * @var string $tags_text
     *
     */
    protected $tags_text;

    

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    function __toString()
    {
      return $this->getTitle();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set short_text
     *
     * @param text $shortText
     */
    public function setShortText($shortText)
    {
        $this->short_text = $shortText;
    }

    /**
     * Get short_text
     *
     * @return text 
     */
    public function getShortText()
    {
        return $this->short_text;
    }

    /**
     * Set text
     *
     * @param text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return text 
     */
    public function getText()
    {
        return $this->text;
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
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set category
     *
     * @param Sfby\BlogBundle\Entity\Category $category
     */
    public function setCategory(\Sfby\BlogBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Sfby\BlogBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param Sfby\UserBundle\Entity\User $user
     */
    public function setUser(\Sfby\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Sfby\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add tags
     *
     * @param Sfby\BlogBundle\Entity\Tag $tags
     */
    public function addTag(\Sfby\BlogBundle\Entity\Tag $tags)
    {
        if (!$this->getTags()->contains($tags)) {
            $this->getTags()->add($tags);
            $tags->addBlog($this);
        }
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
     * Set related tags
     *
     * @param aarrat $groups
     */
    public function setTags($tags)
    {
        foreach ($tags as $tag){
            $this->addTag($tag);
        }
    }

    /**
     * Get tags in text
     *
     * @return  text
     */
    public function getTagsText()
    {
        if (!$this->tags_text)
        {
            $arr = array();
            foreach ($this->tags as $tag)
            {
                $arr[] = $tag->getName();
                $this->tags_text = join(', ', $arr);
            }
        }
        return $this->tags_text;
    }
    
    /**
     * Set tags text
     *
     * @param $text
     */
    public function setTagsText($text)
    {
        $this->tags_text = $text;
    }

    /**
     * Set keywords
     *
     * @param text $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords
     *
     * @return text 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}