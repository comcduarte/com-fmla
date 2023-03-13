<?php

use Fmla\Controller\FmlaConfigController;
use Fmla\Controller\FmlaController;
use Fmla\Controller\Factory\FmlaConfigControllerFactory;
use Fmla\Controller\Factory\FmlaControllerFactory;
use Fmla\Form\FmlaForm;
use Fmla\Form\Factory\FmlaFormFactory;
use Fmla\Service\Factory\FmlaModelAdapterFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

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
                        'controller' => FmlaController::class,
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
                                'controller' => FmlaConfigController::class,
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
                                'controller' => FmlaController::class,
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
            'fmla' => FmlaController::class,
        ],
        'factories' => [
            FmlaController::class => FmlaControllerFactory::class,
            FmlaConfigController::class => FmlaConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            FmlaForm::class => FmlaFormFactory::class,
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
            'fmla-model-adapter' => FmlaModelAdapterFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'fmla/config' => __DIR__ . '/../view/fmla/config/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];