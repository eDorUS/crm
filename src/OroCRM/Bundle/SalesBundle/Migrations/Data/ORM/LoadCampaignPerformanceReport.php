<?php

namespace OroCRM\Bundle\SalesBundle\Migrations\Data\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\ReportBundle\Entity\Report;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadCampaignPerformanceReport extends AbstractFixture implements
    ContainerAwareInterface,
    DependentFixtureInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Oro\Bundle\ReportBundle\Migrations\Data\ORM\LoadReportTypes'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load "Campaign Performance" report definition
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $report = new Report();
        $report->setName('Campaign Performance');
        $report->setEntity('OroCRM\Bundle\CampaignBundle\Entity\Campaign');
        $type = $em->getReference('OroReportBundle:ReportType', 'TABLE');
        $report->setType($type);
        // @codingStandardsIgnoreStart
        $definition = [
            'filters'          => [],
            'columns'          => [
                ['name' => 'name', 'label' => 'Name', 'func' => '', 'sorting' => ''],
                ['name' => 'code', 'label' => 'Code', 'func' => '', 'sorting' => ''],
                [
                    'name'    => 'OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::campaign+OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::id',
                    'label'   => 'Leads',
                    'func'    => [
                        'name'       => 'Count',
                        'group_type' => 'aggregates',
                        'group_name' => 'number'
                    ],
                    'sorting' => ''
                ],
                [
                    'name'    => 'OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::campaign+OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::opportunities+OroCRM\\Bundle\\SalesBundle\\Entity\\Opportunity::id',
                    'label'   => 'Opportunities',
                    'func'    => [
                        'name'       => 'Count',
                        'group_type' => 'aggregates',
                        'group_name' => 'number'
                    ],
                    'sorting' => ''
                ],
                [
                    'name'    => 'OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::campaign+OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::opportunities+OroCRM\\Bundle\\SalesBundle\\Entity\\Opportunity::status_label',
                    'label'   => 'Number Won',
                    'func'    => [
                        'name'       => 'WonCount',
                        'group_type' => 'aggregates',
                        'group_name' => 'opportunity_status'
                    ],
                    'sorting' => ''
                ],
                [
                    'name'    => 'OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::campaign+OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::opportunities+OroCRM\\Bundle\\SalesBundle\\Entity\\Opportunity::status_label',
                    'label'   => 'Number Lost',
                    'func'    => [
                        'name'       => 'LostCount',
                        'group_type' => 'aggregates',
                        'group_name' => 'opportunity_status'
                    ],
                    'sorting' => ''
                ],
                [
                    'name'    => 'OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::campaign+OroCRM\\Bundle\\SalesBundle\\Entity\\Lead::opportunities+OroCRM\\Bundle\\SalesBundle\\Entity\\Opportunity::closeRevenue',
                    'label'   => 'Close revenue',
                    'func'    => [
                        'name'       => 'WonRevenueSumFunction',
                        'group_type' => 'aggregates',
                        'group_name' => 'opportunity'
                    ],
                    'sorting' => 'DESC'
                ]
            ],
            'grouping_columns' => [
                [
                    'name'                                   => 'id',
                    'oro_report_form[grouping][columnNames]' => 'id'
                ]
            ]
        ];
        // @codingStandardsIgnoreEnd
        $report->setDefinition(json_encode($definition));
        $em->persist($report);
        $em->flush($report);
    }
}
