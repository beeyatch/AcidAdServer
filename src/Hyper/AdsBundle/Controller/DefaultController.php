<?php

namespace Hyper\AdsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/frame/{id}")
     * @Template()
     */
    public function frameAction($id)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var $zone \Hyper\AdsBundle\Entity\Zone */
        $zone = $em->getRepository('HyperAdsBundle:Zone')->find($id);

        if (empty($zone)) {
            throw $this->createNotFoundException('Zone not found.');
        }

        /** @var $banner \Hyper\AdsBundle\Entity\Banner */
        $banner = $em->getRepository('HyperAdsBundle:Banner')->getRandomBannerInZone($zone);

        if (empty($banner)) {
            throw $this->createNotFoundException('No banner found.');
        }

        /** @var $banner \Hyper\AdsBundle\Entity\BannerZoneReference */
        $reference = $banner->getReferenceInZone($id);

        /** @var $statsCollector \Hyper\AdsBundle\Helper\StatsCollector */
        $statsCollector = $this->get('stats_collector');
        $statsCollector->collectView($reference);

        return array(
            'banner' => $banner,
        );
    }

    /**
     * @Route("/click/{zoneId}/{bannerId}")
     * @todo prevention from bots' clicks
     */
    public function clickAction($zoneId, $bannerId)
    {
        /** @var $bannerRepository \Hyper\AdsBundle\Entity\BannerRepository */
        $bannerRepository = $this->get('doctrine.orm.entity_manager')->getRepository('HyperAdsBundle:Banner');

        $reference = $bannerRepository->getBannerReference($bannerId, $zoneId);

        if (empty($reference)) {
            throw $this->createNotFoundException('Banner is not present in given zone');
        }

        /** @var $statsCollector \Hyper\AdsBundle\Helper\StatsCollector */
        $statsCollector = $this->get('stats_collector');
        $statsCollector->collectClick($reference);

        return $this->redirect($reference->getBanner()->getUrl());
    }

    /**
     * @Route("/head", name="default_head")
     */
    public function headAction()
    {
        $resp = new Response();

        $resp->headers->set('Content-type', 'text/javascript');
        $resp->headers->set('Cache-control', 'max-age=172800, public, must-revalidate');

        $resp->setContent(
            $this->renderView(
                'HyperAdsBundle:Default:head.js.twig',
                array(
                    'server' => $this->getRequest()->getHttpHost(),
                )
            )
        );

        return $resp;
    }

    /**
     * @Route("/demo")
     * @Template()
     */
    public function demoAction()
    {
        return array();
    }

}