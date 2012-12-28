<?php

namespace Hyper\AdsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hyper\AdsBundle\DBAL\AnnouncementPaymentType;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Hyper\AdsBundle\Entity\AnnouncementRepository")
 * @ORM\Table(name="announcement")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn("announcement_type", type="string")
 * @ORM\DiscriminatorMap({"announcement" = "Announcement", "banner" = "Banner"})
 */
class Announcement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="announcement_payment_type", name="announcement_payment_type")
     * @Assert\Choice(callback="getAnnouncementPaymentTypes")
     */
    protected $announcementPaymentType;

    /**
     * @ORM\ManyToOne(targetEntity="Advertiser", inversedBy="announcements")
     * @ORM\JoinColumn(name="advertiser_id", referencedColumnName="id")
     *
     * @var \Hyper\AdsBundle\Entity\Advertiser
     */
    protected $advertiser;

    /**
     * @ORM\Column(type="smallint", name="paid")
     */
    protected $paid = false;

    /**
     * @ORM\Column(type="date", name="paid_to", nullable=true)
     * @var \DateTime
     */
    protected $paidTo;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="announcement")
     */
    protected $orders;

    public function __construct()
    {
        $this->paid = AnnouncementPaymentType::ANNOUNCEMENT_PAYMENT_TYPE_STANDARD != $this->announcementPaymentType;
        $this->orders = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function isActive()
    {
        if (AnnouncementPaymentType::ANNOUNCEMENT_PAYMENT_TYPE_STANDARD == $this->announcementPaymentType) {
            return !$this->isExpired();
        } else {
            return $this->getPaidTo() > new \DateTime() && !$this->isExpired();
        }
    }

    public function setAnnouncementPaymentType($announcementPaymentType)
    {
        if (!in_array($announcementPaymentType, AnnouncementPaymentType::getValidTypes())) {
            throw new \InvalidArgumentException('Given announcement payment type is invalid');
        }

        $this->announcementPaymentType = $announcementPaymentType;
    }

    public function setPaid($paid = true)
    {
        $this->paid = !!$paid;
    }

    public function getPaid()
    {
        return $this->paid;
    }

    public function isPaid()
    {
        return $this->getPaid();
    }

    public static function getAnnouncementPaymentTypes()
    {
        return AnnouncementPaymentType::getValidTypes();
    }

    /**
     * @return Advertiser
     */
    public function getAdvertiser()
    {
        return $this->advertiser;
    }

    public function setAdvertiser(Advertiser $advertiser)
    {
        $this->advertiser = $advertiser;
    }

    /**
     * @return \DateTime
     */
    public function getPaidTo()
    {
        return $this->paidTo;
    }

    public function setPaidTo(\DateTime $paidTo)
    {
        $this->paidTo = $paidTo;
        if ($paidTo >= $this->getExpireDate()
            && $paidTo->diff($this->getExpireDate())->days >= 0
        ) {
            $this->paid = true;
        } else {
            $this->paid = false;
        }
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function __toString()
    {
        return sprintf('%s (ID: %d)', $this->getTitle(), $this->getId());
    }
}
