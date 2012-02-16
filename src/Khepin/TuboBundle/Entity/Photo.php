<?php

namespace Khepin\TuboBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Khepin\TuboBundle\Entity\Photo
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Photo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $tubo_id
     *
     * @ORM\Column(name="tubo_id", type="integer")
     */
    private $tubo_id;

    /**
     * @var string $link
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var string $legend
     *
     * @ORM\Column(name="legend", type="string", length=255)
     */
    private $legend;


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
     * Set tubo_id
     *
     * @param integer $tuboId
     */
    public function setTuboId($tuboId)
    {
        $this->tubo_id = $tuboId;
    }

    /**
     * Get tubo_id
     *
     * @return integer 
     */
    public function getTuboId()
    {
        return $this->tubo_id;
    }

    /**
     * Set link
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set legend
     *
     * @param string $legend
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
    }

    /**
     * Get legend
     *
     * @return string 
     */
    public function getLegend()
    {
        return $this->legend;
    }
}