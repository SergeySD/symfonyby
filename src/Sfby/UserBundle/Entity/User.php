<?php

namespace Sfby\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sfby\BlogBundle\Entity\Blog;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
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
     * @Assert\NotBlank(groups={"Registration", "Profile"}, message="profile.error.name_is_blank")
     * @Assert\MinLength(limit=2, groups={"Registration", "Profile"}, message="profile.error.name_short")
     * @Assert\MaxLength(limit=255, groups={"Registration", "Profile"}, message="profile.error.name_long")
     */
    protected $name;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    protected $image;
    
    /**
     * @var string $old_image
     *
     */
    protected $old_image;
    
    /**
     * @var UploadedFile $file
     * 
     * @Assert\Image(
     *     groups={"Profile"}, 
     *     maxSize = "100k", 
     *     mimeTypesMessage = "profile.error.image_type",
     *     maxSizeMessage = "profile.error.image_size"
     * )
     */
    protected $file;

    /**
     * @var string $about
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     */
    protected $about;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * 
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Sfby\BlogBundle\Entity\Blog", mappedBy="fos_user", cascade={"all"})
     */
    protected $blogs;

    public function __construct()
    {
        parent::__construct();
        $this->blogs = new ArrayCollection();
        // your own logic
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function preUpdate()
    {
        if (null === $this->file) return;
        $this->file->move($this->getAbsoluteUploadDir(), $this->image);
        unset($this->file);
        if ($file = $this->getAbsolutePath($this->old_image)) 
        {
            @unlink($file);
        }
        unset($this->old_image);
    }
    /**
     * @ORM\PostRemove()
     */
    public function postRemove()
    {
        if ($file = $this->getAbsolutePath()) 
        {
            unlink($file);
        }
    }
    
    public function getAbsolutePath($filename = null)
    {
        if(!$filename) $filename = $this->image;
        return null === $filename ? null : $this->getAbsoluteUploadDir().'/'.$filename;
    }

    public function getWebPath()
    {
        return null === $this->image ? null : $this->getUploadDir().'/'.$this->image;
    }

    protected function getAbsoluteUploadDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/users';
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
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->old_image = $this->image;
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Get file
     *
     * @return UploadedFile 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->setImage(uniqid().'.'.$file->guessExtension());
        $this->file = $file;
    }

    /**
     * Set about
     *
     * @param text $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * Get about
     *
     * @return text 
     */
    public function getAbout()
    {
        return $this->about;
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