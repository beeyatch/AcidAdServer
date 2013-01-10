<?php
/**
 * @author fajka <fajka@hyperreal.info>
 * @see    https://github.com/fajka
 */

namespace Hyper\AdsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Hyper\AdsBundle\Entity\BannerZoneReferenceRepository")
 * @ORM\Table(name="banner_zone")
 */
class BannerZoneReference
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Banner", inversedBy="zones")
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id")
     */
    protected $banner;

    /**
     * @ORM\ManyToOne(targetEntity="Zone", inversedBy="banners")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     */
    protected $zone;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="bannerZone")
     * @var Order[]
     */
    protected $orders;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $probability;

    /**
     * @ORM\Column(type="integer")
     */
    protected $clicks = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="pay_model", type="paymodeltype", nullable=false)
     */
    private $payModel;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    protected $active = 1;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBanner($banner)
    {
        $this->banner = $banner;
    }

    /**
     * @return \Hyper\AdsBundle\Entity\Banner
     */
    public function getBanner()
    {
        return $this->banner;
    }

    public function setClicks($clicks)
    {
        $this->clicks = $clicks;
    }

    public function getClicks()
    {
        return $this->clicks;
    }

    public function setProbability($probability)
    {
        $this->probability = $probability;
    }

    public function getProbability()
    {
        return $this->probability;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return \Hyper\AdsBundle\Entity\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getPayModel()
    {
        return $this->payModel;
    }

    public function setPayModel($payModel)
    {
        $this->payModel = $payModel;
    }
}
