<?php
namespace Fmla;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'fmla' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/fmla',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => Controller\FmlaController::class,
                    ]
                ],
                'may_terminate' => FALSE,
                'child_routes' => [
                    'config' => [
                        'type' => Segment::class,
                        'priority' => 100,
                        'options' => [
                            'route' => '/config[/:action]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => Controller\FmlaConfigController::class,
                            ],
                        ],
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => Controller\FmlaController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'acl' => [
        'guest' => [
            'fmla/config' => ['create','index'],
        ],
        'member' => [
            'fmla/default' => ['index','create','update','delete','find','menu'],
            'fmla/config' => ['index','clear','create'],
        ],
    ],
    'controllers' => [
        'aliases' => [
            'fmla' => Controller\FmlaController::class,
        ],
        'factories' => [
            Controller\FmlaController::class => Controller\Factory\FmlaControllerFactory::class,
            Controller\FmlaConfigController::class => Controller\Factory\FmlaConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\FmlaForm::class => Form\Factory\FmlaFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            'fmla' => [
                'label' => 'FMLA',
                'route' => 'fmla/default',
                'class' => 'dropdown',
                'resource' => 'fmla/default',
                'privilege' => 'index',
                'pages' => [
                    [
                        'label' => 'Add FMLA',
                        'route' => 'fmla/default',
                        'action' => 'create',
                        'controller' => 'fmla',
                        'resource' => 'fmla/default',
                        'privilege' => 'create',
                    ],
                    [
                        'label' => 'List FMLA',
                        'route' => 'fmla/default',
                        'action' => 'index',
                        'controller' => 'fmla',
                        'resource' => 'fmla/default',
                        'privilege' => 'index',
                    ],
                ],
            ],
            'settings' => [
                'pages' => [
                    'fmla' => [
                        'label' => 'FMLA Settings',
                        'route' => 'fmla/config',
                        'action' => 'index',
                        'resource' => 'fmla/config',
                        'privilege' => 'index',
                    ],
                ],
            ],
        ],
        
    ],
    'service_manager' => [
        'aliases' => [
            'fmla-model-adapter-config' => 'model-adapter-config',
        ],
        'factories' => [
            'fmla-model-adapter' => Service\Factory\FmlaModelAdapterFactory::class,
            Listener\FmlaListener::class => Listener\Factory\FmlaListenerFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\FmlaRecords::class => InvokableFactory::class,
        ],
        'aliases' => [
            'fmlarecords' => View\Helper\FmlaRecords::class,
            'fmla_records' => View\Helper\FmlaRecords::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'fmla/config' => __DIR__ . '/../view/fmla/config/index.phtml',
            'fmla/update' => __DIR__ . '/../view/fmla/fmla/update.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];