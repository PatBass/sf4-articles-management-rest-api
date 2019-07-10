<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=100)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="biography", type="text")
     */
    private $biography;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author", cascade={"persist"})
     */
    private $articles;

    /**
     * Author constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return Author
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set biography
     *
     * @param string $biography
     *
     * @return Author
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Add article
     *
     * @param Article $article
     *
     * @return Author
     */
    public function addArticle(Article $article)
    {
        if (!$this->getArticles()->contains($article)) {
            $this->getArticles()->add($article);
        }

        return $this;
    }

    /**
     * Remove article
     *
     * @param Article $article
     *
     * @return Author
     */
    public function removeArticle(Article $article)
    {
        if ($this->getArticles()->contains($article)) {
            $this->getArticles()->removeElement($article);
        }

        return $this;
    }

    /**
     * Get articles
     *
     * @return Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
