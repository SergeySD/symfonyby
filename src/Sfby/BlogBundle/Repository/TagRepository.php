<?php

namespace Sfby\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sfby\BlogBundle\Entity\Tag;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findForCloud()
    {
        $res = $this->getEntityManager()
            ->createQuery('SELECT t, count(b.id) FROM SfbyBlogBundle:Tag t LEFT JOIN t.blogs b GROUP BY t.id')
            ->getResult();
        
        $groups = 3;
        $min = 9999;
        $max = 0;
        foreach ($res as $arr)
        {
            $min = $arr[1] < $min ? $arr[1] : $min;
            $max = $arr[1] > $max ? $arr[1] : $max;
        }
        
        $s = ($max - $min)/$groups;
        foreach ($res as $i => $arr)
        {
            $group = round(($arr[1]-$min)/$s);
            $res[$i][1] = $group;
        }
        
        return $res;
    }
    
    /**
     * Sets tags for blog from tags text
     *
     * @param blog
     */
    public function processTags(\Sfby\BlogBundle\Entity\Blog $blog)
    {
        $tags = $this->loadOrCreateTags($blog->getTagsText());
        $this->replaceTags($tags, $blog);
    }
    
    /**
     * Loads or creates a tag from tag name
     *
     * @param array  $name  Tag name
     * @return Tags
     */
    public function loadOrCreateTags($tags_text)
    {
        if (empty($tags_text)) {
            return array();
        }
      
        if (!is_array($tags_text))
        {
            $tags_text = trim($tags_text);
            $tags_text = explode(',', $tags_text);
        }
        $tags_text = array_unique($tags_text);
        $tags_text = array_filter($tags_text, function ($value) { return !empty($value); });
        $tags = array();
        foreach ($tags_text as $val)
        {
            $criteria = array('name' => trim($val));
            $tag = $this->findOneBy($criteria);
            $em = $this->getEntityManager();
            if (!count($tag))
            {
                $tag = new Tag;
                $tag->setName(trim($val));
                $em->persist($tag);
            }
            $tags[] = $tag;

            $em->flush();
        }
        
        return $tags;
    }

    /**
     * Adds multiple tags on the blog
     *
     * @param $tags
     * @param $blog
     */
    public function addTags($tags, \Sfby\BlogBundle\Entity\Blog $blog)
    {
        foreach ($tags as $tag)
        {
            $this->addTag($tag, $blog);
        }
    }
    
    
    /**
     * Adds a tag on the blog
     *
     * @param $tag
     * @param $blog
     */
    public function addTag(\Sfby\BlogBundle\Entity\Tag $tag, \Sfby\BlogBundle\Entity\Blog $blog)
    {
        if (!$blog->getTags()->contains($tag)) {
            $blog->getTags()->add($tag);
            $tag->addBlog($blog);
        }
    }
    
    public function replaceTags($tags, \Sfby\BlogBundle\Entity\Blog $blog)
    {
        foreach ($blog->getTags() as $tag)
            $this->removeTag($tag, $blog);
        
        $this->addTags($tags, $blog);
    }

    /**
     * Removes an existant tag on the given blog
     *
     * @param $tag
     * @param $blog
     */
    public function removeTag(\Sfby\BlogBundle\Entity\Tag $tag, \Sfby\BlogBundle\Entity\Blog $blog)
    {
        if ($tag->getBlogs()->contains($blog)){
            $tag->getBlogs()->removeElement($blog);
            $blog->getTags()->removeElement($tag);
        }
    }
    
    /**
     * Joins tag names into a string
     *
     * @param string    $separator  Tag name separator
     * @return String
     */
    public function getTagNamesInString($separator=', ', $search = null, $limit = 0)
    {
        $q = $this->createQueryBuilder('q');
        $q->select('q.name');
        if ($limit)
            $q->setMaxResults($limit);
        if ($search)
            $q->where("q.name LIKE ?1")->setParameter(1, '%'.$search.'%');
        
        $names = array();
        foreach ($q->getQuery()->getResult() as $tag)
            $names[] = $tag['name'];

        return join($separator, $names);
    }
}