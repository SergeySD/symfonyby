<?php
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
class appProdProjectContainer extends Container implements TaggedContainerInterface
{
    public function __construct()
    {
        parent::__construct(new FrozenParameterBag($this->getDefaultParameters()));
    }
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Symfony\Bundle\FrameworkBundle\EventDispatcher();
        $instance->registerKernelListeners(array(0 => array(0 => new \Symfony\Bundle\FrameworkBundle\RequestListener($this, $this->get('router'), NULL), 1 => new \Symfony\Component\HttpKernel\Cache\EsiListener(new \Symfony\Component\HttpKernel\Cache\Esi()), 2 => new \Symfony\Component\HttpKernel\ResponseListener()), -128 => array(0 => new \Symfony\Component\HttpKernel\Debug\ExceptionListener('Symfony\\Bundle\\FrameworkBundle\\Controller\\ExceptionController::exceptionAction', NULL))));
        return $instance;
    }
    protected function getHttpKernelService()
    {
        return $this->services['http_kernel'] = new \Symfony\Component\HttpKernel\HttpKernel($this->get('event_dispatcher'), $this->get('controller_resolver'));
    }
    protected function getRequestService()
    {
        return $this->get('http_kernel')->getRequest();
    }
    protected function getResponseService()
    {
        $instance = new \Symfony\Component\HttpFoundation\Response();
        $instance->setCharset('UTF-8');
        return $instance;
    }
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Bundle\FrameworkBundle\Util\Filesystem();
    }
    protected function getRouting_Loader_RealService()
    {
        $a = new \Symfony\Component\Routing\Loader\LoaderResolver();
        $a->addLoader(new \Symfony\Component\Routing\Loader\XmlFileLoader(array('Application' => '/Users/everzet/Sites/test/symfony2/app/../src/Application', 'Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle', 'Symfony\\Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle')));
        $a->addLoader(new \Symfony\Component\Routing\Loader\YamlFileLoader(array('Application' => '/Users/everzet/Sites/test/symfony2/app/../src/Application', 'Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle', 'Symfony\\Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle')));
        $a->addLoader(new \Symfony\Component\Routing\Loader\PhpFileLoader(array('Application' => '/Users/everzet/Sites/test/symfony2/app/../src/Application', 'Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle', 'Symfony\\Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle')));
        return $this->services['routing.loader.real'] = new \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader($this->get('controller_name_converter'), NULL, $a);
    }
    protected function getRouterService()
    {
        return $this->services['router'] = new \Symfony\Component\Routing\Router(new \Symfony\Bundle\FrameworkBundle\Routing\LazyLoader($this, 'routing.loader.real'), '/Users/everzet/Sites/test/symfony2/app/config/routing.yml', array('cache_dir' => '/Users/everzet/Sites/test/symfony2/app/cache/prod', 'debug' => false, 'generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper', 'generator_cache_class' => 'app_prodUrlGenerator', 'matcher_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher', 'matcher_base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher', 'matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper', 'matcher_cache_class' => 'app_prodUrlMatcher'));
    }
    protected function getValidatorService()
    {
        return $this->services['validator'] = new \Symfony\Component\Validator\Validator(new \Symfony\Component\Validator\Mapping\ClassMetadataFactory(new \Symfony\Component\Validator\Mapping\Loader\LoaderChain(array(0 => new \Symfony\Component\Validator\Mapping\Loader\AnnotationLoader(array('validation' => 'Symfony\\Component\\Validator\\Constraints\\')), 1 => new \Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader('loadValidatorMetadata'), 2 => new \Symfony\Component\Validator\Mapping\Loader\XmlFilesLoader(array(0 => '/Users/everzet/Sites/test/symfony2/src/vendor/symfony/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/../../../Component/Form/Resources/config/validation.xml')), 3 => new \Symfony\Component\Validator\Mapping\Loader\YamlFilesLoader(array())))), new \Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory($this, array()));
    }
    protected function getTemplating_Engine_PhpService()
    {
        $this->services['templating.engine.php'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine($this, $this->get('templating.loader'));
        $instance->setCharset('UTF-8');
        $instance->setHelpers(array('slots' => 'templating.helper.slots', 'assets' => 'templating.helper.assets', 'request' => 'templating.helper.request', 'session' => 'templating.helper.session', 'router' => 'templating.helper.router', 'actions' => 'templating.helper.actions', 'code' => 'templating.helper.code', 'translator' => 'templating.helper.translator', 'security' => 'templating.helper.security', 'form' => 'templating.helper.form'));
        return $instance;
    }
    protected function getTemplating_Helper_SlotsService()
    {
        return $this->services['templating.helper.slots'] = new \Symfony\Component\Templating\Helper\SlotsHelper();
    }
    protected function getTemplating_Helper_AssetsService()
    {
        return $this->services['templating.helper.assets'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper($this->get('http_kernel')->getRequest(), array(), NULL);
    }
    protected function getTemplating_Helper_RequestService()
    {
        return $this->services['templating.helper.request'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper($this->get('http_kernel')->getRequest());
    }
    protected function getTemplating_Helper_SessionService()
    {
        return $this->services['templating.helper.session'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper($this->get('http_kernel')->getRequest());
    }
    protected function getTemplating_Helper_RouterService()
    {
        return $this->services['templating.helper.router'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper($this->get('router'));
    }
    protected function getTemplating_Helper_ActionsService()
    {
        return $this->services['templating.helper.actions'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper($this->get('controller_resolver'));
    }
    protected function getTemplating_Helper_CodeService()
    {
        return $this->services['templating.helper.code'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper(NULL, '/Users/everzet/Sites/test/symfony2/app');
    }
    protected function getTemplating_Helper_TranslatorService()
    {
        return $this->services['templating.helper.translator'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper($this->get('translator'));
    }
    protected function getTemplating_Helper_SecurityService()
    {
        return $this->services['templating.helper.security'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SecurityHelper(NULL);
    }
    protected function getTemplating_Helper_FormService()
    {
        return $this->services['templating.helper.form'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper($this->get('templating'));
    }
    protected function getTemplating_NameParserService()
    {
        return $this->services['templating.name_parser'] = new \Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser();
    }
    protected function getSessionService()
    {
        $this->services['session'] = $instance = new \Symfony\Component\HttpFoundation\Session($this->get('session.storage'), array('default_locale' => 'en'));
        $instance->start();
        return $instance;
    }
    protected function getTranslator_RealService()
    {
        $this->services['translator.real'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, $this->get('translator.selector'), array('cache_dir' => '/Users/everzet/Sites/test/symfony2/app/cache/prod/translations', 'debug' => false), $this->get('session'));
        $instance->setFallbackLocale('en');
        return $instance;
    }
    protected function getTranslatorService()
    {
        $this->services['translator'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, $this->get('translator.selector'), array('cache_dir' => '/Users/everzet/Sites/test/symfony2/app/cache/prod/translations', 'debug' => false), $this->get('session'));
        $instance->setFallbackLocale('en');
        return $instance;
    }
    protected function getTranslation_Loader_PhpService()
    {
        return $this->services['translation.loader.php'] = new \Symfony\Component\Translation\Loader\PhpFileLoader();
    }
    protected function getTranslation_Loader_YmlService()
    {
        return $this->services['translation.loader.yml'] = new \Symfony\Component\Translation\Loader\YamlFileLoader();
    }
    protected function getTranslation_Loader_XliffService()
    {
        return $this->services['translation.loader.xliff'] = new \Symfony\Component\Translation\Loader\XliffFileLoader();
    }
    protected function getTwigService()
    {
        $this->services['twig'] = $instance = new \Twig_Environment($this->get('twig.loader'), array('charset' => 'UTF-8', 'debug' => false, 'cache' => '/Users/everzet/Sites/test/symfony2/app/cache/prod/twig', 'strict_variables' => false));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\TransExtension($this->get('translator')));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\TemplatingExtension($this));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\FormExtension(array(0 => 'TwigBundle::form.twig')));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\SecurityExtension(NULL));
        return $instance;
    }
    protected function getTwig_LoaderService()
    {
        return $this->services['twig.loader'] = new \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader($this->get('templating.name_parser'), array(0 => '/Users/everzet/Sites/test/symfony2/app/views/%bundle%/%controller%/%name%%format%.%renderer%', 1 => '/Users/everzet/Sites/test/symfony2/app/../src/Application/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%', 2 => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%', 3 => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%'), NULL);
    }
    protected function getTemplating_Engine_TwigService()
    {
        return $this->services['templating.engine.twig'] = new \Symfony\Bundle\TwigBundle\TwigEngine($this, $this->get('twig'), $this->get('twig.globals'));
    }
    protected function getTwig_GlobalsService()
    {
        return $this->services['twig.globals'] = new \Symfony\Bundle\TwigBundle\GlobalVariables($this);
    }
    protected function getTwig_Extension_TextService()
    {
        return $this->services['twig.extension.text'] = new \Twig_Extensions_Extension_Text();
    }
    protected function getTwig_Extension_DebugService()
    {
        return $this->services['twig.extension.debug'] = new \Twig_Extensions_Extension_Debug();
    }
    protected function getDoctrine_DataCollectorService()
    {
        return $this->services['doctrine.data_collector'] = new \Symfony\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector($this->get('doctrine.dbal.logger'));
    }
    protected function getDoctrine_Dbal_DefaultConnectionService()
    {
        $a = new \Doctrine\DBAL\Configuration();
        $a->setSqlLogger($this->get('doctrine.dbal.logger'));
        return $this->services['doctrine.dbal.default_connection'] = call_user_func(array('Doctrine\\DBAL\\DriverManager', 'getConnection'), array('driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver', 'driverOptions' => array(), 'dbname' => 'sf2com', 'host' => 'localhost', 'user' => 'root'), $a, $this->get('doctrine.dbal.default_connection.event_manager'));
    }
    protected function getDoctrine_Dbal_DefaultConnection_EventManagerService()
    {
        $this->services['doctrine.dbal.default_connection.event_manager'] = $instance = new \Symfony\Bundle\DoctrineAbstractBundle\Event\EventManager();
        $instance->loadTaggedEventListeners($this);
        $instance->loadTaggedEventListeners($this, 'doctrine.dbal.default_event_listener');
        $instance->loadTaggedEventSubscribers($this);
        $instance->loadTaggedEventSubscribers($this, 'doctrine.dbal.default_event_subscriber');
        return $instance;
    }
    protected function getDoctrine_Orm_DefaultAnnotationMetadataDriverService()
    {
        $a = new \Doctrine\Common\Annotations\AnnotationReader();
        $a->setAnnotationNamespaceAlias('Doctrine\\ORM\\Mapping\\', 'orm');
        return $this->services['doctrine.orm.default_annotation_metadata_driver'] = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($a, array(0 => '/Users/everzet/Sites/test/symfony2/src/Application/HelloBundle/Entity'));
    }
    protected function getDoctrine_Orm_DefaultMetadataDriverService()
    {
        $this->services['doctrine.orm.default_metadata_driver'] = $instance = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $instance->addDriver($this->get('doctrine.orm.default_annotation_metadata_driver'), 'Application\\HelloBundle\\Entity');
        return $instance;
    }
    protected function getDoctrine_Orm_DefaultEntityManagerService()
    {
        $a = new \Doctrine\ORM\Configuration();
        $a->setEntityNamespaces(array('HelloBundle' => 'Application\\HelloBundle\\Entity'));
        $a->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $a->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $a->setResultCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $a->setMetadataDriverImpl($this->get('doctrine.orm.default_metadata_driver'));
        $a->setProxyDir('/Users/everzet/Sites/test/symfony2/app/cache/prod/doctrine/orm/Proxies');
        $a->setProxyNamespace('Proxies');
        $a->setAutoGenerateProxyClasses(false);
        $a->setClassMetadataFactoryName('Doctrine\\ORM\\Mapping\\ClassMetadataFactory');
        return $this->services['doctrine.orm.default_entity_manager'] = call_user_func(array('Doctrine\\ORM\\EntityManager', 'create'), $this->get('doctrine.dbal.default_connection'), $a);
    }
    protected function getTemplating_LoaderService()
    {
        return $this->services['templating.loader'] = new \Symfony\Component\Templating\Loader\FilesystemLoader($this->get('templating.name_parser'), array(0 => '/Users/everzet/Sites/test/symfony2/app/views/%bundle%/%controller%/%name%%format%.%renderer%', 1 => '/Users/everzet/Sites/test/symfony2/app/../src/Application/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%', 2 => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%', 3 => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%'));
    }
    protected function getTemplatingService()
    {
        $this->services['templating'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine($this);
        $instance->setEngineIds(array(0 => 'templating.engine.twig', 1 => 'templating.engine.php'));
        return $instance;
    }
    protected function getSession_StorageService()
    {
        return $this->services['session.storage'] = new \Symfony\Component\HttpFoundation\SessionStorage\NativeSessionStorage(array('lifetime' => 3600));
    }
    protected function getDatabaseConnectionService()
    {
        return $this->get('doctrine.dbal.default_connection');
    }
    protected function getDoctrine_Dbal_EventManagerService()
    {
        return $this->get('doctrine.dbal.default_connection.event_manager');
    }
    protected function getSecurity_User_EntityManagerService()
    {
        return $this->get('doctrine.orm.default_entity_manager');
    }
    protected function getDoctrine_Orm_EntityManagerService()
    {
        return $this->get('doctrine.orm.default_entity_manager');
    }
    protected function getDoctrine_Orm_DefaultEntityManager_EventManagerService()
    {
        return $this->get('doctrine.dbal.default_connection.event_manager');
    }
    protected function getControllerNameConverterService()
    {
        return $this->services['controller_name_converter'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter($this->get('kernel'), NULL);
    }
    protected function getControllerResolverService()
    {
        return $this->services['controller_resolver'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver($this, $this->get('controller_name_converter'), NULL);
    }
    protected function getTranslator_SelectorService()
    {
        return $this->services['translator.selector'] = new \Symfony\Component\Translation\MessageSelector();
    }
    protected function getDoctrine_Dbal_LoggerService()
    {
        return $this->services['doctrine.dbal.logger'] = new \Symfony\Bundle\DoctrineBundle\Logger\DbalLogger(NULL);
    }
    public function findTaggedServiceIds($name)
    {
        static $tags = array(
            'templating.engine' => array(
                'templating.engine.php' => array(
                    0 => array(
                        'priority' => 128,
                    ),
                ),
                'templating.engine.twig' => array(
                    0 => array(
                        'priority' => 255,
                    ),
                ),
            ),
            'templating.helper' => array(
                'templating.helper.slots' => array(
                    0 => array(
                        'alias' => 'slots',
                    ),
                ),
                'templating.helper.assets' => array(
                    0 => array(
                        'alias' => 'assets',
                    ),
                ),
                'templating.helper.request' => array(
                    0 => array(
                        'alias' => 'request',
                    ),
                ),
                'templating.helper.session' => array(
                    0 => array(
                        'alias' => 'session',
                    ),
                ),
                'templating.helper.router' => array(
                    0 => array(
                        'alias' => 'router',
                    ),
                ),
                'templating.helper.actions' => array(
                    0 => array(
                        'alias' => 'actions',
                    ),
                ),
                'templating.helper.code' => array(
                    0 => array(
                        'alias' => 'code',
                    ),
                ),
                'templating.helper.translator' => array(
                    0 => array(
                        'alias' => 'translator',
                    ),
                ),
                'templating.helper.security' => array(
                    0 => array(
                        'alias' => 'security',
                    ),
                ),
                'templating.helper.form' => array(
                    0 => array(
                        'alias' => 'form',
                    ),
                ),
            ),
            'translation.loader' => array(
                'translation.loader.php' => array(
                    0 => array(
                        'alias' => 'php',
                    ),
                ),
                'translation.loader.yml' => array(
                    0 => array(
                        'alias' => 'yml',
                    ),
                ),
                'translation.loader.xliff' => array(
                    0 => array(
                        'alias' => 'xliff',
                    ),
                ),
            ),
            'data_collector' => array(
                'doctrine.data_collector' => array(
                    0 => array(
                        'template' => 'DoctrineBundle:Collector:db',
                    ),
                ),
            ),
            'doctrine.orm.entity_manager' => array(
                'doctrine.orm.default_entity_manager' => array(
                    0 => array(
                    ),
                ),
            ),
        );
        return isset($tags[$name]) ? $tags[$name] : array();
    }
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => '/Users/everzet/Sites/test/symfony2/app',
            'kernel.environment' => 'prod',
            'kernel.debug' => false,
            'kernel.name' => 'app',
            'kernel.cache_dir' => '/Users/everzet/Sites/test/symfony2/app/cache/prod',
            'kernel.logs_dir' => '/Users/everzet/Sites/test/symfony2/app/logs',
            'kernel.bundle_dirs' => array(
                'Application' => '/Users/everzet/Sites/test/symfony2/app/../src/Application',
                'Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle',
                'Symfony\\Bundle' => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle',
            ),
            'kernel.bundles' => array(
                0 => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                1 => 'Symfony\\Bundle\\TwigBundle\\TwigBundle',
                2 => 'Symfony\\Bundle\\ZendBundle\\ZendBundle',
                3 => 'Symfony\\Bundle\\SwiftmailerBundle\\SwiftmailerBundle',
                4 => 'Symfony\\Bundle\\DoctrineBundle\\DoctrineBundle',
                5 => 'Symfony\\Bundle\\DoctrineMigrationsBundle\\DoctrineMigrationsBundle',
                6 => 'Application\\HelloBundle\\HelloBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'request_listener.class' => 'Symfony\\Bundle\\FrameworkBundle\\RequestListener',
            'controller_resolver.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
            'controller_name_converter.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameConverter',
            'response_listener.class' => 'Symfony\\Component\\HttpKernel\\ResponseListener',
            'exception_listener.class' => 'Symfony\\Component\\HttpKernel\\Debug\\ExceptionListener',
            'exception_listener.controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ExceptionController::exceptionAction',
            'esi.class' => 'Symfony\\Component\\HttpKernel\\Cache\\Esi',
            'esi_listener.class' => 'Symfony\\Component\\HttpKernel\\Cache\\EsiListener',
            'csrf_secret' => 'xxxxxxxxxx',
            'event_dispatcher.class' => 'Symfony\\Bundle\\FrameworkBundle\\EventDispatcher',
            'http_kernel.class' => 'Symfony\\Component\\HttpKernel\\HttpKernel',
            'response.class' => 'Symfony\\Component\\HttpFoundation\\Response',
            'error_handler.class' => 'Symfony\\Component\\HttpKernel\\Debug\\ErrorHandler',
            'error_handler.level' => NULL,
            'filesystem.class' => 'Symfony\\Bundle\\FrameworkBundle\\Util\\Filesystem',
            'router.class' => 'Symfony\\Component\\Routing\\Router',
            'routing.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\DelegatingLoader',
            'routing.resolver.class' => 'Symfony\\Component\\Routing\\Loader\\LoaderResolver',
            'routing.loader.xml.class' => 'Symfony\\Component\\Routing\\Loader\\XmlFileLoader',
            'routing.loader.yml.class' => 'Symfony\\Component\\Routing\\Loader\\YamlFileLoader',
            'routing.loader.php.class' => 'Symfony\\Component\\Routing\\Loader\\PhpFileLoader',
            'router.options.generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'router.options.matcher_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'router.options.matcher_base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'router.options.matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'routing.resource' => '/Users/everzet/Sites/test/symfony2/app/config/routing.yml',
            'kernel.compiled_classes' => array(
                0 => 'Symfony\\Component\\Routing\\RouterInterface',
                1 => 'Symfony\\Component\\Routing\\Router',
                2 => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcherInterface',
                3 => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
                4 => 'Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface',
                5 => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
                6 => 'Symfony\\Component\\Routing\\Loader\\LoaderInterface',
                7 => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\LazyLoader',
                8 => 'Symfony\\Component\\Templating\\DelegatingEngine',
                9 => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\EngineInterface',
                10 => 'Symfony\\Component\\HttpFoundation\\Session',
                11 => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\SessionStorageInterface',
                12 => 'Symfony\\Component\\HttpFoundation\\ParameterBag',
                13 => 'Symfony\\Component\\HttpFoundation\\HeaderBag',
                14 => 'Symfony\\Component\\HttpFoundation\\Request',
                15 => 'Symfony\\Component\\HttpFoundation\\Response',
                16 => 'Symfony\\Component\\HttpFoundation\\ResponseHeaderBag',
                17 => 'Symfony\\Component\\HttpKernel\\HttpKernel',
                18 => 'Symfony\\Component\\HttpKernel\\ResponseListener',
                19 => 'Symfony\\Component\\HttpKernel\\Controller\\ControllerResolver',
                20 => 'Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface',
                21 => 'Symfony\\Bundle\\FrameworkBundle\\RequestListener',
                22 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameConverter',
                23 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
                24 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
                25 => 'Symfony\\Component\\EventDispatcher\\Event',
                26 => 'Symfony\\Component\\EventDispatcher\\EventDispatcher',
                27 => 'Symfony\\Bundle\\FrameworkBundle\\EventDispatcher',
                28 => 'Symfony\\Component\\Form\\FormConfiguration',
            ),
            'validator.class' => 'Symfony\\Component\\Validator\\Validator',
            'validator.mapping.class_metadata_factory.class' => 'Symfony\\Component\\Validator\\Mapping\\ClassMetadataFactory',
            'validator.mapping.loader.loader_chain.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\LoaderChain',
            'validator.mapping.loader.static_method_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\StaticMethodLoader',
            'validator.mapping.loader.annotation_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\AnnotationLoader',
            'validator.mapping.loader.xml_file_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\XmlFileLoader',
            'validator.mapping.loader.yaml_file_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\YamlFileLoader',
            'validator.mapping.loader.xml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\XmlFilesLoader',
            'validator.mapping.loader.yaml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\YamlFilesLoader',
            'validator.mapping.loader.static_method_loader.method_name' => 'loadValidatorMetadata',
            'validator.validator_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Validator\\ConstraintValidatorFactory',
            'validator.annotations.namespaces' => array(
                'validation' => 'Symfony\\Component\\Validator\\Constraints\\',
            ),
            'templating.engine.delegating.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\DelegatingEngine',
            'templating.engine.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\PhpEngine',
            'templating.loader.filesystem.class' => 'Symfony\\Component\\Templating\\Loader\\FilesystemLoader',
            'templating.loader.cache.class' => 'Symfony\\Component\\Templating\\Loader\\CacheLoader',
            'templating.loader.chain.class' => 'Symfony\\Component\\Templating\\Loader\\ChainLoader',
            'templating.helper.slots.class' => 'Symfony\\Component\\Templating\\Helper\\SlotsHelper',
            'templating.helper.assets.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\AssetsHelper',
            'templating.helper.actions.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\ActionsHelper',
            'templating.helper.router.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RouterHelper',
            'templating.helper.request.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RequestHelper',
            'templating.helper.session.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SessionHelper',
            'templating.helper.code.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\CodeHelper',
            'templating.helper.translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\TranslatorHelper',
            'templating.helper.security.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SecurityHelper',
            'templating.helper.form.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\FormHelper',
            'templating.assets.version' => NULL,
            'templating.assets.base_urls' => array(
            ),
            'templating.name_parser.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\TemplateNameParser',
            'templating.renderer.php.class' => 'Symfony\\Component\\Templating\\Renderer\\PhpRenderer',
            'debug.file_link_format' => NULL,
            'templating.loader.filesystem.path' => array(
                0 => '/Users/everzet/Sites/test/symfony2/app/views/%bundle%/%controller%/%name%%format%.%renderer%',
                1 => '/Users/everzet/Sites/test/symfony2/app/../src/Application/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%',
                2 => '/Users/everzet/Sites/test/symfony2/app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%',
                3 => '/Users/everzet/Sites/test/symfony2/app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%%format%.%renderer%',
            ),
            'templating.loader.cache.path' => NULL,
            'session.class' => 'Symfony\\Component\\HttpFoundation\\Session',
            'session.default_locale' => 'en',
            'session.storage.native.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\NativeSessionStorage',
            'session.storage.native.options' => array(
                'lifetime' => 3600,
            ),
            'session.storage.pdo.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\PdoSessionStorage',
            'session.storage.pdo.options' => array(
            ),
            'session.storage.array.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\ArraySessionStorage',
            'session.storage.array.options' => array(
            ),
            'translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\Translator',
            'translator.identity.class' => 'Symfony\\Component\\Translation\\IdentityTranslator',
            'translator.selector.class' => 'Symfony\\Component\\Translation\\MessageSelector',
            'translation.loader.php.class' => 'Symfony\\Component\\Translation\\Loader\\PhpFileLoader',
            'translation.loader.yml.class' => 'Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            'translation.loader.xliff.class' => 'Symfony\\Component\\Translation\\Loader\\XliffFileLoader',
            'translator.fallback_locale' => 'en',
            'translation.resources' => array(
                0 => array(
                    0 => 'xliff',
                    1 => '/Users/everzet/Sites/test/symfony2/src/vendor/symfony/src/Symfony/Bundle/FrameworkBundle/Resources/translations/validators.fr.xliff',
                    2 => 'fr',
                    3 => 'validators',
                ),
            ),
            'twig.class' => 'Twig_Environment',
            'twig.options' => array(
                'charset' => 'UTF-8',
                'debug' => false,
                'cache' => '/Users/everzet/Sites/test/symfony2/app/cache/prod/twig',
                'strict_variables' => false,
            ),
            'twig.loader.class' => 'Symfony\\Bundle\\TwigBundle\\Loader\\FilesystemLoader',
            'twig.globals.class' => 'Symfony\\Bundle\\TwigBundle\\GlobalVariables',
            'twig.form.resources' => array(
                0 => 'TwigBundle::form.twig',
            ),
            'templating.engine.twig.class' => 'Symfony\\Bundle\\TwigBundle\\TwigEngine',
            'doctrine.dbal.default_connection' => 'default',
            'doctrine.dbal.connection_class' => 'Doctrine\\DBAL\\Connection',
            'doctrine.dbal.logger.debug_class' => 'Doctrine\\DBAL\\Logging\\DebugStack',
            'doctrine.dbal.logger_class' => 'Symfony\\Bundle\\DoctrineBundle\\Logger\\DbalLogger',
            'doctrine.data_collector.class' => 'Symfony\\Bundle\\DoctrineBundle\\DataCollector\\DoctrineDataCollector',
            'doctrine.dbal.event_manager_class' => 'Symfony\\Bundle\\DoctrineAbstractBundle\\Event\\EventManager',
            'doctrine.orm.default_entity_manager' => 'default',
            'doctrine.orm.metadata_cache_driver' => 'array',
            'doctrine.orm.query_cache_driver' => 'array',
            'doctrine.orm.result_cache_driver' => 'array',
            'doctrine.orm.configuration_class' => 'Doctrine\\ORM\\Configuration',
            'doctrine.orm.entity_manager_class' => 'Doctrine\\ORM\\EntityManager',
            'doctrine.orm.proxy_namespace' => 'Proxies',
            'doctrine.orm.proxy_dir' => '/Users/everzet/Sites/test/symfony2/app/cache/prod/doctrine/orm/Proxies',
            'doctrine.orm.auto_generate_proxy_classes' => false,
            'doctrine.orm.cache.array_class' => 'Doctrine\\Common\\Cache\\ArrayCache',
            'doctrine.orm.cache.apc_class' => 'Doctrine\\Common\\Cache\\ApcCache',
            'doctrine.orm.cache.memcache_class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
            'doctrine.orm.cache.memcache_host' => 'localhost',
            'doctrine.orm.cache.memcache_port' => 11211,
            'doctrine.orm.cache.memcache_instance_class' => 'Memcache',
            'doctrine.orm.cache.xcache_class' => 'Doctrine\\Common\\Cache\\XcacheCache',
            'doctrine.orm.metadata.driver_chain_class' => 'Doctrine\\ORM\\Mapping\\Driver\\DriverChain',
            'doctrine.orm.metadata.annotation_class' => 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver',
            'doctrine.orm.metadata.annotation_reader_class' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
            'doctrine.orm.metadata.annotation_default_namespace' => 'Doctrine\\ORM\\Mapping\\',
            'doctrine.orm.metadata.xml_class' => 'Doctrine\\ORM\\Mapping\\Driver\\XmlDriver',
            'doctrine.orm.metadata.yml_class' => 'Doctrine\\ORM\\Mapping\\Driver\\YamlDriver',
            'doctrine.orm.metadata.php_class' => 'Doctrine\\ORM\\Mapping\\Driver\\PHPDriver',
            'doctrine.orm.metadata.staticphp_class' => 'Doctrine\\ORM\\Mapping\\Driver\\StaticPHPDriver',
            'doctrine.orm.class_metadata_factory_name' => 'Doctrine\\ORM\\Mapping\\ClassMetadataFactory',
            'security.user.provider.entity.class' => 'Symfony\\Bundle\\DoctrineBundle\\Security\\EntityUserProvider',
            'security.acl.collection_cache.class' => 'Symfony\\Bundle\\DoctrineBundle\\Security\\AclCollectionCache',
        );
    }
}
