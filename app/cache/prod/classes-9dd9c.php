<?php
namespace Symfony\Component\Routing;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
interface RouterInterface extends UrlMatcherInterface, UrlGeneratorInterface
{
}
namespace Symfony\Component\Routing;
use Symfony\Component\Routing\Loader\LoaderInterface;
class Router implements RouterInterface
{
    protected $matcher;
    protected $generator;
    protected $options;
    protected $defaults;
    protected $context;
    protected $loader;
    protected $collection;
    protected $resource;
    public function __construct(LoaderInterface $loader, $resource, array $options = array(), array $context = array(), array $defaults = array())
    {
        $this->loader = $loader;
        $this->resource = $resource;
        $this->context = $context;
        $this->defaults = $defaults;
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'ProjectUrlGenerator',
            'matcher_class'          => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'ProjectUrlMatcher',
        );
                if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }
        $this->options = array_merge($this->options, $options);
    }
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->loader->load($this->resource);
        }
        return $this->collection;
    }
    public function setContext(array $context = array())
    {
        $this->context = $context;
    }
    public function setDefaults(array $defaults = array())
    {
        $this->defaults = $defaults;
    }
    public function generate($name, array $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }
    public function match($url)
    {
        return $this->getMatcher()->match($url);
    }
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }
        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }
        $class = $this->options['matcher_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getRouteCollection());
            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );
            $this->updateCache($class, $dumper->dump($options));
        }
        require_once $this->getCacheFile($class);
        return $this->matcher = new $class($this->context, $this->defaults);
    }
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }
        if (null === $this->options['cache_dir'] || null === $this->options['generator_cache_class']) {
            return $this->generator = new $this->options['generator_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }
        $class = $this->options['generator_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['generator_dumper_class']($this->getRouteCollection());
            $options = array(
                'class'      => $class,
                'base_class' => $this->options['generator_base_class'],
            );
            $this->updateCache($class, $dumper->dump($options));
        }
        require_once $this->getCacheFile($class);
        return $this->generator = new $class($this->context, $this->defaults);
    }
    protected function updateCache($class, $dump)
    {
        $this->writeCacheFile($this->getCacheFile($class), $dump);
        if ($this->options['debug']) {
            $this->writeCacheFile($this->getCacheFile($class, 'meta'), serialize($this->getRouteCollection()->getResources()));
        }
    }
    protected function needsReload($class)
    {
        $file = $this->getCacheFile($class);
        if (!file_exists($file)) {
            return true;
        }
        if (!$this->options['debug']) {
            return false;
        }
        $metadata = $this->getCacheFile($class, 'meta');
        if (!file_exists($metadata)) {
            return true;
        }
        $time = filemtime($file);
        $meta = unserialize(file_get_contents($metadata));
        foreach ($meta as $resource) {
            if (!$resource->isUptodate($time)) {
                return true;
            }
        }
        return false;
    }
    protected function getCacheFile($class, $extension = 'php')
    {
        return $this->options['cache_dir'].'/'.$class.'.'.$extension;
    }
    protected function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            chmod($file, 0644);
            return;
        }
        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }
}
namespace Symfony\Component\Routing\Matcher;
interface UrlMatcherInterface
{
    function match($url);
}
namespace Symfony\Component\Routing\Matcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
class UrlMatcher implements UrlMatcherInterface
{
    protected $routes;
    protected $defaults;
    protected $context;
    public function __construct(RouteCollection $routes, array $context = array(), array $defaults = array())
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->defaults = $defaults;
    }
    public function match($url)
    {
        $url = $this->normalizeUrl($url);
        foreach ($this->routes->all() as $name => $route) {
            $compiledRoute = $route->compile();
            if (isset($this->context['method']) && (($req = $route->getRequirement('_method')) && !preg_match(sprintf('#^(%s)$#xi', $req), $this->context['method']))) {
                continue;
            }
                        if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($url, $compiledRoute->getStaticPrefix())) {
                continue;
            }
            if (!preg_match($compiledRoute->getRegex(), $url, $matches)) {
                continue;
            }
            return array_merge($this->mergeDefaults($matches, $route->getDefaults()), array('_route' => $name));
        }
        return false;
    }
    protected function mergeDefaults($params, $defaults)
    {
        $parameters = array_merge($this->defaults, $defaults);
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $parameters[$key] = urldecode($value);
            }
        }
        return $parameters;
    }
    protected function normalizeUrl($url)
    {
                if ('/' !== substr($url, 0, 1)) {
            $url = '/'.$url;
        }
                if (false !== $pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }
                return preg_replace('#/+#', '/', $url);
    }
}
namespace Symfony\Component\Routing\Generator;
interface UrlGeneratorInterface
{
    function generate($name, array $parameters, $absolute = false);
}
namespace Symfony\Component\Routing\Generator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
class UrlGenerator implements UrlGeneratorInterface
{
    protected $routes;
    protected $defaults;
    protected $context;
    protected $cache;
    public function __construct(RouteCollection $routes, array $context = array(), array $defaults = array())
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->defaults = $defaults;
        $this->cache = array();
    }
    public function generate($name, array $parameters, $absolute = false)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not exist.', $name));
        }
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $route->compile();
        }
        return $this->doGenerate($this->cache[$name]->getVariables(), $route->getDefaults(), $route->getRequirements(), $this->cache[$name]->getTokens(), $parameters, $name, $absolute);
    }
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
        $defaults = array_merge($this->defaults, $defaults);
        $tparams = array_merge($defaults, $parameters);
                if ($diff = array_diff_key($variables, $tparams)) {
            throw new \InvalidArgumentException(sprintf('The "%s" route has some missing mandatory parameters (%s).', $name, implode(', ', $diff)));
        }
        $url = '';
        $optional = true;
        foreach ($tokens as $token) {
            if ('variable' === $token[0]) {
                if (false === $optional || !isset($defaults[$token[3]]) || (isset($parameters[$token[3]]) && $parameters[$token[3]] != $defaults[$token[3]])) {
                                        if (isset($requirements[$token[3]]) && !preg_match('#^'.$requirements[$token[3]].'$#', $tparams[$token[3]])) {
                        throw new \InvalidArgumentException(sprintf('Parameter "%s" for route "%s" must match "%s" ("%s" given).', $token[3], $name, $requirements[$token[3]], $tparams[$token[3]]));
                    }
                    $url = $token[1].urlencode($tparams[$token[3]]).$url;
                    $optional = false;
                }
            } elseif ('text' === $token[0]) {
                $url = $token[1].$token[2].$url;
                $optional = false;
            } else {
                                if ($segment = call_user_func_array(array($this, 'generateFor'.ucfirst(array_shift($token))), array_merge(array($optional, $tparams), $token))) {
                    $url = $segment.$url;
                    $optional = false;
                }
            }
        }
        if (!$url) {
            $url = '/';
        }
                if ($extra = array_diff_key($parameters, $variables, $defaults)) {
            $url .= '?'.http_build_query($extra);
        }
        $url = (isset($this->context['base_url']) ? $this->context['base_url'] : '').$url;
        if ($absolute && isset($this->context['host'])) {
            $url = 'http'.(isset($this->context['is_secure']) && $this->context['is_secure'] ? 's' : '').'://'.$this->context['host'].$url;
        }
        return $url;
    }
}
namespace Symfony\Component\Routing\Loader;
use Symfony\Component\Routing\Loader\LoaderResolver;
interface LoaderInterface
{
    function load($resource, $type = null);
    function supports($resource, $type = null);
    function getResolver();
    function setResolver(LoaderResolver $resolver);
}
namespace Symfony\Bundle\FrameworkBundle\Routing;
use Symfony\Component\Routing\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Loader\LoaderResolver as BaseLoaderResolver;
class LazyLoader implements LoaderInterface
{
    protected $container;
    protected $service;
    public function __construct(ContainerInterface $container, $service)
    {
        $this->container = $container;
        $this->service = $service;
    }
    public function load($resource, $type = null)
    {
        return $this->container->get($this->service)->load($resource, $type);
    }
    public function supports($resource, $type = null)
    {
        return $this->container->get($this->service)->supports($resource, $type);
    }
    public function getResolver()
    {
        return $this->container->get($this->service)->getResolver();
    }
    public function setResolver(BaseLoaderResolver $resolver)
    {
        $this->container->get($this->service)->setResolver($resolver);
    }
}
namespace Symfony\Component\Templating;
class DelegatingEngine implements EngineInterface
{
    protected $engines;
    public function __construct(array $engines = array())
    {
        $this->engines = array();
        foreach ($engines as $engine) {
            $this->addEngine($engine);
        }
    }
    public function render($name, array $parameters = array())
    {
        return $this->getEngine($name)->render($name, $parameters);
    }
    public function exists($name)
    {
        return $this->getEngine($name)->exists($name);
    }
    public function load($name)
    {
        return $this->getEngine($name)->load($name);
    }
    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }
    public function supports($name)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($name)) {
                return true;
            }
        }
        return false;
    }
    protected function getEngine($name)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($name)) {
                return $engine;
            }
        }
        throw new \RuntimeException(sprintf('No engine is able to work with the "%s" template.', $name));
    }
}
namespace Symfony\Bundle\FrameworkBundle\Templating;
use Symfony\Component\Templating\EngineInterface as BaseEngineInterface;
use Symfony\Component\HttpFoundation\Response;
interface EngineInterface extends BaseEngineInterface
{
    function renderResponse($view, array $parameters = array(), Response $response = null);
}
namespace Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
class Session implements \Serializable
{
    protected $storage;
    protected $attributes;
    protected $oldFlashes;
    protected $started;
    protected $options;
    public function __construct(SessionStorageInterface $storage, array $options = array())
    {
        $this->storage = $storage;
        $this->options = $options;
        $this->attributes = array('_flash' => array(), '_locale' => $this->getDefaultLocale());
        $this->started = false;
    }
    public function start()
    {
        if (true === $this->started) {
            return;
        }
        $this->storage->start();
        $this->attributes = $this->storage->read('_symfony2');
        if (!isset($this->attributes['_flash'])) {
            $this->attributes['_flash'] = array();
        }
        if (!isset($this->attributes['_locale'])) {
            $this->attributes['_locale'] = $this->getDefaultLocale();
        }
                $this->oldFlashes = array_flip(array_keys($this->attributes['_flash']));
        $this->started = true;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->attributes);
    }
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }
    public function set($name, $value)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes[$name] = $value;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes($attributes)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes = $attributes;
    }
    public function remove($name)
    {
        if (false === $this->started) {
            $this->start();
        }
        if (array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
        }
    }
    public function clear()
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes = array();
    }
    public function invalidate()
    {
        $this->clear();
        $this->storage->regenerate();
    }
    public function getId()
    {
        return $this->storage->getId();
    }
    public function getLocale()
    {
        return $this->attributes['_locale'];
    }
    public function setLocale($locale)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_locale'] = $locale;
    }
    public function getFlashes()
    {
        return $this->attributes['_flash'];
    }
    public function setFlashes($values)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_flash'] = $values;
    }
    public function getFlash($name, $default = null)
    {
        return array_key_exists($name, $this->attributes['_flash']) ? $this->attributes['_flash'][$name] : $default;
    }
    public function setFlash($name, $value)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_flash'][$name] = $value;
        unset($this->oldFlashes[$name]);
    }
    public function hasFlash($name)
    {
        return array_key_exists($name, $this->attributes['_flash']);
    }
    public function removeFlash($name)
    {
        unset($this->attributes['_flash'][$name]);
    }
    public function clearFlashes()
    {
        $this->attributes['_flash'] = array();
    }
    public function save()
    {
        if (true === $this->started) {
            if (isset($this->attributes['_flash'])) {
                $this->attributes['_flash'] = array_diff_key($this->attributes['_flash'], $this->oldFlashes);
            }
            $this->storage->write('_symfony2', $this->attributes);
        }
    }
    public function __destruct()
    {
        $this->save();
    }
    public function serialize()
    {
        return serialize(array($this->storage, $this->options));
    }
    public function unserialize($serialized)
    {
        list($this->storage, $this->options) = unserialize($serialized);
        $this->attributes = array();
        $this->started = false;
    }
    protected function getDefaultLocale()
    {
        return isset($this->options['default_locale']) ? $this->options['default_locale'] : 'en';
    }
}
namespace Symfony\Component\HttpFoundation\SessionStorage;
interface SessionStorageInterface
{
    function start();
    function getId();
    function read($key);
    function remove($key);
    function write($key, $data);
    function regenerate($destroy = false);
}
namespace Symfony\Component\HttpFoundation;
class ParameterBag
{
    protected $parameters;
    public function __construct(array $parameters = array())
    {
        $this->replace($parameters);
    }
    public function all()
    {
        return $this->parameters;
    }
    public function keys()
    {
        return array_keys($this->parameters);
    }
    public function replace(array $parameters = array())
    {
        $this->parameters = $parameters;
    }
    public function add(array $parameters = array())
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }
    public function getDigits($key, $default = '')
    {
        return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));
    }
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }
}
namespace Symfony\Component\HttpFoundation;
class HeaderBag
{
    protected $headers;
    protected $cacheControl;
    public function __construct(array $headers = array())
    {
        $this->cacheControl = array();
        $this->replace($headers);
    }
    public function all()
    {
        return $this->headers;
    }
    public function keys()
    {
        return array_keys($this->headers);
    }
    public function replace(array $headers = array())
    {
        $this->headers = array();
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return $first ? null : array();
            } else {
                return $first ? $default : array($default);
            }
        }
        if ($first) {
            return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
        } else {
            return $this->headers[$key];
        }
    }
    public function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        if (!is_array($values)) {
            $values = array($values);
        }
        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl($values[0]);
        }
    }
    public function has($key)
    {
        return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
    }
    public function contains($key, $value)
    {
        return in_array($value, $this->get($key, null, false));
    }
    public function remove($key)
    {
        $key = strtr(strtolower($key), '_', '-');
        unset($this->headers[$key]);
        if ('cache-control' === $key) {
            $this->cacheControl = array();
        }
    }
    public function setCookie($name, $value, $domain = null, $expires = null, $path = '/', $secure = false, $httponly = true)
    {
        $this->validateCookie($name, $value);
        return $this->set('Cookie', sprintf('%s=%s', $name, urlencode($value)));
    }
    public function getDate($key, \DateTime $default = null)
    {
        if (null === $value = $this->get($key)) {
            return $default;
        }
        if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
            throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
        }
        return $date;
    }
    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl);
    }
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }
    public function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    protected function getCacheControlHeader()
    {
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }
                $parts[] = "$key=$value";
            }
        }
        return implode(', ', $parts);
    }
    protected function parseCacheControl($header)
    {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[2]) && $match[2] ? $match[2] : (isset($match[3]) ? $match[3] : true);
        }
        return $cacheControl;
    }
    protected function validateCookie($name, $value)
    {
                if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
        if (preg_match("/[,; \t\r\n\013\014]/", $value)) {
            throw new \InvalidArgumentException(sprintf('The cookie value "%s" contains invalid characters.', $name));
        }
        if (!$name) {
            throw new \InvalidArgumentException('The cookie name cannot be empty');
        }
    }
}
namespace Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\SessionStorage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class Request
{
    public $attributes;
    public $request;
    public $query;
    public $server;
    public $files;
    public $cookies;
    public $headers;
    protected $content;
    protected $languages;
    protected $charsets;
    protected $acceptableContentTypes;
    protected $pathInfo;
    protected $requestUri;
    protected $baseUrl;
    protected $basePath;
    protected $method;
    protected $format;
    protected $session;
    static protected $formats;
    public function __construct(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server);
    }
    public function initialize(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        $this->request = new ParameterBag(null !== $request ? $request : $_POST);
        $this->query = new ParameterBag(null !== $query ? $query : $_GET);
        $this->attributes = new ParameterBag(null !== $attributes ? $attributes : array());
        $this->cookies = new ParameterBag(null !== $cookies ? $cookies : $_COOKIE);
        $this->files = new ParameterBag($this->convertFileInformation(null !== $files ? $files : $_FILES));
        $this->server = new ParameterBag(null !== $server ? $server : $_SERVER);
        $this->headers = new HeaderBag($this->initializeHeaders());
        $this->content = null;
        $this->languages = null;
        $this->charsets = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }
    static public function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array())
    {
        $defaults = array(
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => 80,
            'HTTP_HOST'            => 'localhost',
            'HTTP_USER_AGENT'      => 'Symfony/2.X',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR'          => '127.0.0.1',
            'SCRIPT_NAME'          => '',
            'SCRIPT_FILENAME'      => '',
        );
        $components = parse_url($uri);
        if (isset($components['host'])) {
            $defaults['SERVER_NAME'] = $components['host'];
            $defaults['HTTP_HOST'] = $components['host'];
        }
        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $defaults['HTTPS'] = 'on';
                $defaults['SERVER_PORT'] = 443;
            }
        }
        if (isset($components['port'])) {
            $defaults['SERVER_PORT'] = $components['port'];
            $defaults['HTTP_HOST'] = $defaults['HTTP_HOST'].':'.$components['port'];
        }
        if (in_array(strtoupper($method), array('POST', 'PUT', 'DELETE'))) {
            $request = $parameters;
            $query = array();
            $defaults['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        } else {
            $request = array();
            $query = $parameters;
            if (false !== $pos = strpos($uri, '?')) {
                $qs = substr($uri, $pos + 1);
                parse_str($qs, $params);
                $query = array_merge($params, $query);
            }
        }
        $queryString = isset($components['query']) ? html_entity_decode($components['query']) : '';
        parse_str($queryString, $qs);
        if (is_array($qs)) {
            $query = array_replace($qs, $query);
        }
        $uri = $components['path'] . ($queryString ? '?'.$queryString : '');
        $server = array_replace($defaults, $server, array(
            'REQUEST_METHOD'       => strtoupper($method),
            'PATH_INFO'            => '',
            'REQUEST_URI'          => $uri,
            'QUERY_STRING'         => $queryString,
        ));
        return new self($query, $request, array(), $cookies, $files, $server);
    }
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        $dup = clone $this;
        $dup->initialize(
            null !== $query ? $query : $this->query->all(),
            null !== $request ? $request : $this->request->all(),
            null !== $attributes ? $attributes : $this->attributes->all(),
            null !== $cookies ? $cookies : $this->cookies->all(),
            null !== $files ? $files : $this->files->all(),
            null !== $server ? $server : $this->server->all()
        );
        return $dup;
    }
    public function __clone()
    {
        $this->query      = clone $this->query;
        $this->request    = clone $this->request;
        $this->attributes = clone $this->attributes;
        $this->cookies    = clone $this->cookies;
        $this->files      = clone $this->files;
        $this->server     = clone $this->server;
        $this->headers    = clone $this->headers;
    }
    public function overrideGlobals()
    {
        $_GET = $this->query->all();
        $_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();
        foreach ($this->headers->all() as $key => $value) {
            $_SERVER['HTTP_'.strtoupper(str_replace('-', '_', $key))] = implode(', ', $value);
        }
                        $_REQUEST = array_merge($_GET, $_POST);
    }
                        public function get($key, $default = null)
    {
        return $this->query->get($key, $this->attributes->get($key, $this->request->get($key, $default)));
    }
    public function getSession()
    {
        return $this->session;
    }
    public function hasSession()
    {
        return $this->cookies->has(session_name());
    }
    public function setSession(Session $session)
    {
        $this->session = $session;
    }
    public function getClientIp($proxy = false)
    {
        if ($proxy) {
            if ($this->server->has('HTTP_CLIENT_IP')) {
                return $this->server->get('HTTP_CLIENT_IP');
            } elseif ($this->server->has('HTTP_X_FORWARDED_FOR')) {
                return $this->server->get('HTTP_X_FORWARDED_FOR');
            }
        }
        return $this->server->get('REMOTE_ADDR');
    }
    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }
        return $this->pathInfo;
    }
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }
        return $this->basePath;
    }
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }
        return $this->baseUrl;
    }
    public function getScheme()
    {
        return ($this->server->get('HTTPS') == 'on') ? 'https' : 'http';
    }
    public function getPort()
    {
        return $this->server->get('SERVER_PORT');
    }
    public function getHttpHost()
    {
        $host = $this->headers->get('HOST');
        if (!empty($host)) {
            return $host;
        }
        $scheme = $this->getScheme();
        $name   = $this->server->get('SERVER_NAME');
        $port   = $this->getPort();
        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $name;
        } else {
            return $name.':'.$port;
        }
    }
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }
        return $this->requestUri;
    }
    public function getUri()
    {
        $qs = $this->getQueryString();
        if (null !== $qs) {
            $qs = '?'.$qs;
        }
        return $this->getScheme().'://'.$this->getHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }
    public function getUriForPath($path)
    {
        return $this->getScheme().'://'.$this->getHttpHost().$this->getBaseUrl().$path;
    }
    public function getQueryString()
    {
        if (!$qs = $this->server->get('QUERY_STRING')) {
            return null;
        }
        $parts = array();
        $order = array();
        foreach (explode('&', $qs) as $segment) {
            if (false === strpos($segment, '=')) {
                $parts[] = $segment;
                $order[] = $segment;
            } else {
                $tmp = explode('=', urldecode($segment), 2);
                $parts[] = urlencode($tmp[0]).'='.urlencode($tmp[1]);
                $order[] = $tmp[0];
            }
        }
        array_multisort($order, SORT_ASC, $parts);
        return implode('&', $parts);
    }
    public function isSecure()
    {
        return (
            (strtolower($this->server->get('HTTPS')) == 'on' || $this->server->get('HTTPS') == 1)
            ||
            (strtolower($this->headers->get('SSL_HTTPS')) == 'on' || $this->headers->get('SSL_HTTPS') == 1)
            ||
            (strtolower($this->headers->get('X_FORWARDED_PROTO')) == 'https')
        );
    }
    public function getHost()
    {
        if ($host = $this->headers->get('X_FORWARDED_HOST')) {
            $elements = explode(',', $host);
            $host = trim($elements[count($elements) - 1]);
        } else {
            $host = $this->headers->get('HOST', $this->server->get('SERVER_NAME', $this->server->get('SERVER_ADDR', '')));
        }
                $elements = explode(':', $host);
        return trim($elements[0]);
    }
    public function setMethod($method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);
    }
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
            if ('POST' === $this->method) {
                $this->method = strtoupper($this->request->get('_method', 'POST'));
            }
        }
        return $this->method;
    }
    public function getMimeType($format)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }
        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }
    public function getFormat($mimeType)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }
        foreach (static::$formats as $format => $mimeTypes) {
            if (in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
        }
        return null;
    }
    public function setFormat($format, $mimeTypes)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }
        static::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
    }
    public function getRequestFormat()
    {
        if (null === $this->format) {
            $this->format = $this->get('_format', 'html');
        }
        return $this->format;
    }
    public function setRequestFormat($format)
    {
        $this->format = $format;
    }
    public function isMethodSafe()
    {
        return in_array($this->getMethod(), array('GET', 'HEAD'));
    }
    public function getContent($asResource = false)
    {
        if (false === $this->content || (true === $asResource && null !== $this->content)) {
            throw new \LogicException('getContent() can only be called once when using the resource return type.');
        }
        if (true === $asResource) {
            $this->content = false;
            return fopen('php://input', 'rb');
        }
        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }
        return $this->content;
    }
    public function getETags()
    {
        return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
    }
    public function isNoCache()
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }
    public function getPreferredLanguage(array $locales = null)
    {
        $preferredLanguages = $this->getLanguages();
        if (null === $locales) {
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
        }
        if (!$preferredLanguages) {
            return $locales[0];
        }
        $preferredLanguages = array_values(array_intersect($preferredLanguages, $locales));
        return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
    }
    public function getLanguages()
    {
        if (null !== $this->languages) {
            return $this->languages;
        }
        $languages = $this->splitHttpAcceptHeader($this->headers->get('Accept-Language'));
        foreach ($languages as $lang) {
            if (strstr($lang, '-')) {
                $codes = explode('-', $lang);
                if ($codes[0] == 'i') {
                                                                                if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = count($codes); $i < $max; $i++) {
                        if ($i == 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
            }
            $this->languages[] = $lang;
        }
        return $this->languages;
    }
    public function getCharsets()
    {
        if (null !== $this->charsets) {
            return $this->charsets;
        }
        return $this->charsets = $this->splitHttpAcceptHeader($this->headers->get('Accept-Charset'));
    }
    public function getAcceptableContentTypes()
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }
        return $this->acceptableContentTypes = $this->splitHttpAcceptHeader($this->headers->get('Accept'));
    }
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }
    public function splitHttpAcceptHeader($header)
    {
        if (!$header) {
            return array();
        }
        $values = array();
        foreach (array_filter(explode(',', $header)) as $value) {
                        if ($pos = strpos($value, ';')) {
                $q     = (float) trim(substr($value, strpos($value, '=') + 1));
                $value = trim(substr($value, 0, $pos));
            } else {
                $q = 1;
            }
            if (0 < $q) {
                $values[trim($value)] = $q;
            }
        }
        arsort($values);
        return array_keys($values);
    }
    protected function prepareRequestUri()
    {
        $requestUri = '';
        if ($this->headers->has('X_REWRITE_URL')) {
                        $requestUri = $this->headers->get('X_REWRITE_URL');
        } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
                        $requestUri = $this->server->get('UNENCODED_URL');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
                        $schemeAndHttpHost = $this->getScheme().'://'.$this->getHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
                        $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ($this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
        }
        return $requestUri;
    }
    protected function prepareBaseUrl()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');         } else {
                                    $path    = $this->server->get('PHP_SELF', '');
            $file    = $this->server->get('SCRIPT_FILENAME', '');
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }
                $requestUri = $this->getRequestUri();
        if ($baseUrl && 0 === strpos($requestUri, $baseUrl)) {
                        return $baseUrl;
        }
        if ($baseUrl && 0 === strpos($requestUri, dirname($baseUrl))) {
                        return rtrim(dirname($baseUrl), '/');
        }
        $truncatedRequestUri = $requestUri;
        if (($pos = strpos($requestUri, '?')) !== false) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                        return '';
        }
                                if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        return rtrim($baseUrl, '/');
    }
    protected function prepareBasePath()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }
        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }
        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }
        return rtrim($basePath, '/');
    }
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        if (null === ($requestUri = $this->getRequestUri())) {
            return '';
        }
        $pathInfo = '';
                if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
                        return '';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }
        return (string) $pathInfo;
    }
    protected function convertFileInformation(array $files)
    {
        $fixedFiles = array();
        foreach ($files as $key => $data) {
            $fixedFiles[$key] = $this->fixPhpFilesArray($data);
        }
        $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
        foreach ($fixedFiles as $key => $data) {
            if (is_array($data)) {
                $keys = array_keys($data);
                sort($keys);
                if ($keys == $fileKeys) {
                    $data['error'] = (int) $data['error'];
                }
                if ($keys != $fileKeys) {
                    $fixedFiles[$key] = $this->convertFileInformation($data);
                } else if ($data['error'] === UPLOAD_ERR_NO_FILE) {
                    $fixedFiles[$key] = null;
                } else {
                    $fixedFiles[$key] = new UploadedFile($data['tmp_name'], $data['name'], $data['type'], $data['size'], $data['error']);
                }
            }
        }
        return $fixedFiles;
    }
    protected function fixPhpFilesArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
        $keys = array_keys($data);
        sort($keys);
        if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }
        $files = $data;
        foreach ($fileKeys as $k) {
            unset($files[$k]);
        }
        foreach (array_keys($data['name']) as $key) {
            $files[$key] = $this->fixPhpFilesArray(array(
                'error'    => $data['error'][$key],
                'name'     => $data['name'][$key],
                'type'     => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size'     => $data['size'][$key],
            ));
        }
        return $files;
    }
    protected function initializeHeaders()
    {
        $headers = array();
        foreach ($this->server->all() as $key => $value) {
            if ('http_' === strtolower(substr($key, 0, 5))) {
                $headers[substr($key, 5)] = $value;
            }
        }
        return $headers;
    }
    static protected function initializeFormats()
    {
        static::$formats = array(
            'txt'  => 'text/plain',
            'js'   => array('application/javascript', 'application/x-javascript', 'text/javascript'),
            'css'  => 'text/css',
            'json' => array('application/json', 'application/x-json'),
            'xml'  => array('text/xml', 'application/xml', 'application/x-xml'),
            'rdf'  => 'application/rdf+xml',
            'atom' => 'application/atom+xml',
        );
    }
}
namespace Symfony\Component\HttpFoundation;
class Response
{
    public $headers;
    protected $content;
    protected $version;
    protected $statusCode;
    protected $statusText;
    protected $charset = 'UTF-8';
    static public $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
        $this->headers = new ResponseHeaderBag($headers);
    }
    public function __toString()
    {
        $content = '';
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset='.$this->charset);
        }
                $content .= sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText)."\n";
                foreach ($this->headers->all() as $name => $values) {
            foreach ($values as $value) {
                $content .= "$name: $value\n";
            }
        }
        $content .= "\n".$this->getContent();
        return $content;
    }
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }
    public function sendHeaders()
    {
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset='.$this->charset);
        }
                header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));
                foreach ($this->headers->all() as $name => $values) {
            foreach ($values as $value) {
                header($name.': '.$value);
            }
        }
    }
    public function sendContent()
    {
        echo $this->content;
    }
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }
    public function getProtocolVersion()
    {
        return $this->version;
    }
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = (int) $code;
        if ($this->statusCode < 100 || $this->statusCode > 599) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }
        $this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function getCharset()
    {
        return $this->charset;
    }
    public function isCacheable()
    {
        if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }
        if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
            return false;
        }
        return $this->isValidateable() || $this->isFresh();
    }
    public function isFresh()
    {
        return $this->getTtl() > 0;
    }
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }
    public function setPrivate()
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');
    }
    public function setPublic()
    {
        $this->headers->addCacheControlDirective('public');
        $this->headers->removeCacheControlDirective('private');
    }
    public function mustRevalidate()
    {
        return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('must-proxy-revalidate');
    }
    public function getDate()
    {
        if (null === $date = $this->headers->getDate('Date')) {
            $date = new \DateTime(null, new \DateTimeZone('UTC'));
            $this->headers->set('Date', $date->format('D, d M Y H:i:s').' GMT');
        }
        return $date;
    }
    public function getAge()
    {
        if ($age = $this->headers->get('Age')) {
            return $age;
        }
        return max(time() - $this->getDate()->format('U'), 0);
    }
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
        }
    }
    public function getExpires()
    {
        return $this->headers->getDate('Expires');
    }
    public function setExpires(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Expires');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Expires', $date->format('D, d M Y H:i:s').' GMT');
        }
    }
    public function getMaxAge()
    {
        if ($age = $this->headers->getCacheControlDirective('s-maxage')) {
            return $age;
        }
        if ($age = $this->headers->getCacheControlDirective('max-age')) {
            return $age;
        }
        if (null !== $this->getExpires()) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }
        return null;
    }
    public function setMaxAge($value)
    {
        $this->headers->addCacheControlDirective('max-age', $value);
    }
    public function setSharedMaxAge($value)
    {
        $this->headers->addCacheControlDirective('s-maxage', $value);
    }
    public function getTtl()
    {
        if ($maxAge = $this->getMaxAge()) {
            return $maxAge - $this->getAge();
        }
        return null;
    }
    public function setTtl($seconds)
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
    }
    public function setClientTtl($seconds)
    {
        $this->setMaxAge($this->getAge() + $seconds);
    }
    public function getLastModified()
    {
        return $this->headers->getDate('LastModified');
    }
    public function setLastModified(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Last-Modified');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');
        }
    }
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }
    public function setEtag($etag = null, $weak = false)
    {
        if (null === $etag) {
            $this->headers->remove('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"'.$etag.'"';
            }
            $this->headers->set('ETag', (true === $weak ? 'W/' : '').$etag);
        }
    }
    public function setCache(array $options)
    {
        if ($diff = array_diff_key($options, array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
            throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_keys($diff))));
        }
        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }
        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }
        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }
        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }
        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }
        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }
    }
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent(null);
                foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
            $this->headers->remove($header);
        }
    }
    public function setRedirect($url, $status = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        $this->setStatusCode($status);
        if (!$this->isRedirect()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
        $this->headers->set('Location', $url);
        $this->setContent(sprintf('<html><head><meta http-equiv="refresh" content="1;url=%s"/></head></html>', htmlspecialchars($url, ENT_QUOTES)));
    }
    public function hasVary()
    {
        return (Boolean) $this->headers->get('Vary');
    }
    public function getVary()
    {
        if (!$vary = $this->headers->get('Vary')) {
            return array();
        }
        return is_array($vary) ? $vary : preg_split('/[\s,]+/', $vary);
    }
    public function setVary($headers, $replace = true)
    {
        $this->headers->set('Vary', $headers, $replace);
    }
    public function isNotModified(Request $request)
    {
        $lastModified = $request->headers->get('If-Modified-Since');
        $notModified = false;
        if ($etags = $request->getEtags()) {
            $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
        } elseif ($lastModified) {
            $notModified = $lastModified == $this->headers->get('Last-Modified');
        }
        if ($notModified) {
            $this->setNotModified();
        }
        return $notModified;
    }
        public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
    public function isOk()
    {
        return 200 === $this->statusCode;
    }
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }
    public function isRedirect()
    {
        return in_array($this->statusCode, array(301, 302, 303, 307));
    }
    public function isEmpty()
    {
        return in_array($this->statusCode, array(201, 204, 304));
    }
    public function isRedirected($location)
    {
        return $this->isRedirect() && $location == $this->headers->get('Location');
    }
}
namespace Symfony\Component\HttpFoundation;
class ResponseHeaderBag extends HeaderBag
{
    protected $computedCacheControl = array();
    public function replace(array $headers = array())
    {
        parent::replace($headers);
        if (!isset($this->headers['cache-control'])) {
            $this->set('cache-control', '');
        }
    }
    public function set($key, $values, $replace = true)
    {
        parent::set($key, $values, $replace);
                if ('cache-control' === strtr(strtolower($key), '_', '-')) {
            $computed = $this->computeCacheControlValue();
            $this->headers['cache-control'] = array($computed);
            $this->computedCacheControl = $this->parseCacheControl($computed);
        }
    }
    public function remove($key)
    {
        parent::remove($key);
        if ('cache-control' === strtr(strtolower($key), '_', '-')) {
            $this->computedCacheControl = array();
        }
    }
    public function setCookie($name, $value, $domain = null, $expires = null, $path = '/', $secure = false, $httponly = true)
    {
        $this->validateCookie($name, $value);
        $cookie = sprintf('%s=%s', $name, urlencode($value));
        if (null !== $expires) {
            if (is_numeric($expires)) {
                $expires = (int) $expires;
            } elseif ($expires instanceof \DateTime) {
                $expires = $expires->getTimestamp();
            } else {
                $expires = strtotime($expires);
                if (false === $expires || -1 == $expires) {
                    throw new \InvalidArgumentException(sprintf('The "expires" cookie parameter is not valid.', $expires));
                }
            }
            $cookie .= '; expires='.substr(\DateTime::createFromFormat('U', $expires, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'), 0, -5);
        }
        if ($domain) {
            $cookie .= '; domain='.$domain;
        }
        $cookie .= '; path='.$path;
        if ($secure) {
            $cookie .= '; secure';
        }
        if ($httponly) {
            $cookie .= '; httponly';
        }
        $this->set('Set-Cookie', $cookie, false);
    }
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl);
    }
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
    }
    protected function computeCacheControlValue()
    {
        if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
            return 'no-cache';
        }
        if (!$this->cacheControl) {
                        return 'private, max-age=0, must-revalidate';
        }
        $header = $this->getCacheControlHeader();
        if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
            return $header;
        }
                if (!isset($this->cacheControl['s-maxage'])) {
            return $header.', private';
        }
        return $header;
    }
}
namespace Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class HttpKernel implements HttpKernelInterface
{
    protected $dispatcher;
    protected $resolver;
    protected $request;
    public function __construct(EventDispatcher $dispatcher, ControllerResolverInterface $resolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
                $previousRequest = $this->request;
        $this->request = $request;
        try {
            $response = $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if (false === $catch) {
                $this->request = $previousRequest;
                throw $e;
            }
                        $event = new Event($this, 'core.exception', array('request_type' => $type, 'request' => $request, 'exception' => $e));
            $this->dispatcher->notifyUntil($event);
            if (!$event->isProcessed()) {
                $this->request = $previousRequest;
                throw $e;
            }
            $response = $this->filterResponse($event->getReturnValue(), $request, 'A "core.exception" listener returned a non response object.', $type);
        }
                $this->request = $previousRequest;
        return $response;
    }
    public function getRequest()
    {
        return $this->request;
    }
    protected function handleRaw(Request $request, $type = self::MASTER_REQUEST)
    {
                $event = new Event($this, 'core.request', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->notifyUntil($event);
        if ($event->isProcessed()) {
            return $this->filterResponse($event->getReturnValue(), $request, 'A "core.request" listener returned a non response object.', $type);
        }
                if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException('Unable to find the controller.');
        }
        $event = new Event($this, 'core.controller', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->filter($event, $controller);
        $controller = $event->getReturnValue();
                if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s).', var_export($controller, true)));
        }
                $arguments = $this->resolver->getArguments($request, $controller);
                $retval = call_user_func_array($controller, $arguments);
                $event = new Event($this, 'core.view', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->filter($event, $retval);
        return $this->filterResponse($event->getReturnValue(), $request, sprintf('The controller must return a response (instead of %s).', is_object($event->getReturnValue()) ? 'an object of class '.get_class($event->getReturnValue()) : is_array($event->getReturnValue()) ? 'an array' : str_replace("\n", '', var_export($event->getReturnValue(), true))), $type);
    }
    protected function filterResponse($response, $request, $message, $type)
    {
        if (!$response instanceof Response) {
            throw new \RuntimeException($message);
        }
        $event = $this->dispatcher->filter(new Event($this, 'core.response', array('request_type' => $type, 'request' => $request)), $response);
        $response = $event->getReturnValue();
        if (!$response instanceof Response) {
            throw new \RuntimeException('A "core.response" listener returned a non response object.');
        }
        return $response;
    }
}
namespace Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
class ResponseListener
{
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.response', array($this, 'filter'), $priority);
    }
    public function filter(Event $event, Response $response)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->get('request_type') || $response->headers->has('Content-Type')) {
            return $response;
        }
        $request = $event->get('request');
        $format = $request->getRequestFormat();
        if ((null !== $format) && $mimeType = $request->getMimeType($format)) {
            $response->headers->set('Content-Type', $mimeType);
        }
        return $response;
    }
}
namespace Symfony\Component\HttpKernel\Controller;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
class ControllerResolver implements ControllerResolverInterface
{
    protected $logger;
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            if (null !== $this->logger) {
                $this->logger->err('Unable to look for the controller as the "_controller" parameter is missing');
            }
            return false;
        }
        if ($controller instanceof \Closure) {
            return $controller;
        }
        list($controller, $method) = $this->createController($controller);
        if (!method_exists($controller, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', get_class($controller), $method));
        }
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Using controller "%s::%s"', get_class($controller), $method));
        }
        return array($controller, $method);
    }
    public function getArguments(Request $request, $controller)
    {
        $attributes = $request->attributes->all();
        if (is_array($controller)) {
            list($controller, $method) = $controller;
            $m = new \ReflectionMethod($controller, $method);
            $parameters = $m->getParameters();
            $repr = sprintf('%s::%s()', get_class($controller), $method);
        } else {
            $f = new \ReflectionFunction($controller);
            $parameters = $f->getParameters();
            $repr = 'Closure';
        }
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->getName(), $attributes)) {
                $arguments[] = $attributes[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->getName()));
            }
        }
        return $arguments;
    }
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }
        list($class, $method) = explode('::', $controller);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        return array(new $class(), $method);
    }
}
namespace Symfony\Component\HttpKernel\Controller;
use Symfony\Component\HttpFoundation\Request;
interface ControllerResolverInterface
{
    function getController(Request $request);
    function getArguments(Request $request, $controller);
}
namespace Symfony\Bundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
class RequestListener
{
    protected $router;
    protected $logger;
    protected $container;
    public function __construct(ContainerInterface $container, RouterInterface $router, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.request', array($this, 'handle'), $priority);
    }
    public function handle(Event $event)
    {
        $request = $event->get('request');
        $master = HttpKernelInterface::MASTER_REQUEST === $event->get('request_type');
        $this->initializeSession($request, $master);
        $this->initializeRequestAttributes($request, $master);
    }
    protected function initializeSession(Request $request, $master)
    {
        if (!$master) {
            return;
        }
                if (null === $request->getSession()) {
            $request->setSession($this->container->get('session'));
        }
                if ($request->hasSession()) {
            $request->getSession()->start();
        }
    }
    protected function initializeRequestAttributes(Request $request, $master)
    {
        if ($master) {
                                    $this->router->setContext(array(
                'base_url'  => $request->getBaseUrl(),
                'method'    => $request->getMethod(),
                'host'      => $request->getHost(),
                'is_secure' => $request->isSecure(),
            ));
        }
        if ($request->attributes->has('_controller')) {
                        return;
        }
                if (false !== $parameters = $this->router->match($request->getPathInfo())) {
            if (null !== $this->logger) {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], str_replace("\n", '', var_export($parameters, true))));
            }
            $request->attributes->add($parameters);
            if ($locale = $request->attributes->get('_locale')) {
                $request->getSession()->setLocale($locale);
            }
        } elseif (null !== $this->logger) {
            $this->logger->err(sprintf('No route found for %s', $request->getPathInfo()));
        }
    }
}
namespace Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
class ControllerNameConverter
{
    protected $kernel;
    protected $logger;
    protected $namespaces;
    public function __construct(Kernel $kernel, LoggerInterface $logger = null)
    {
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->namespaces = array_keys($kernel->getBundleDirs());
    }
    public function toShortNotation($controller)
    {
        if (2 != count($parts = explode('::', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid class::method controller string.', $controller));
        }
        list($class, $method) = $parts;
        if ('Action' != substr($method, -6)) {
            throw new \InvalidArgumentException(sprintf('The "%s::%s" method does not look like a controller action (it does not end with Action)', $class, $method));
        }
        $action = substr($method, 0, -6);
        if (!preg_match('/Controller\\\(.*)Controller$/', $class, $match)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a controller class (it does not end with Controller)', $class));
        }
        $controller = $match[1];
        $bundle = null;
        $namespace = substr($class, 0, strrpos($class, '\\'));
        foreach ($this->namespaces as $prefix) {
            if (0 === $pos = strpos($namespace, $prefix)) {
                                $bundle = substr($namespace, strlen($prefix) + 1, -11);
            }
        }
        if (null === $bundle) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not belong to a known bundle namespace.', $class));
        }
        return $bundle.':'.$controller.':'.$action;
    }
    public function fromShortNotation($controller)
    {
        if (3 != count($parts = explode(':', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid a:b:c controller string.', $controller));
        }
        list($bundle, $controller, $action) = $parts;
        $bundle = strtr($bundle, array('/' => '\\'));
        $class = null;
        $logs = array();
        foreach ($this->namespaces as $namespace) {
            $try = $namespace.'\\'.$bundle.'\\Controller\\'.$controller.'Controller';
            if (!class_exists($try)) {
                if (null !== $this->logger) {
                    $logs[] = sprintf('Failed finding controller "%s:%s" from namespace "%s" (%s)', $bundle, $controller, $namespace, $try);
                }
            } else {
                if (!$this->kernel->isClassInActiveBundle($try)) {
                    throw new \LogicException(sprintf('To use the "%s" controller, you first need to enable the Bundle "%s" in your Kernel class.', $try, $namespace.'\\'.$bundle));
                }
                $class = $try;
                break;
            }
        }
        if (null === $class) {
            if (null !== $this->logger) {
                foreach ($logs as $log) {
                    $this->logger->info($log);
                }
            }
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s:%s".', $bundle, $controller));
        }
        return $class.'::'.$action.'Action';
    }
}
namespace Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
class ControllerResolver extends BaseControllerResolver
{
    protected $container;
    protected $converter;
    protected $esiSupport;
    public function __construct(ContainerInterface $container, ControllerNameConverter $converter, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->converter = $converter;
        parent::__construct($logger);
    }
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                                $controller = $this->converter->fromShortNotation($controller);
            } elseif (1 == $count) {
                                list($service, $method) = explode(':', $controller);
                return array($this->container->get($service), $method);
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }
        list($class, $method) = explode('::', $controller);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        $controller = new $class();
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }
        return array($controller, $method);
    }
    public function forward($controller, array $attributes = array(), array $query = array())
    {
        $attributes['_controller'] = $controller;
        $subRequest = $this->container->get('request')->duplicate($query, null, $attributes);
        return $this->container->get('kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    public function render($controller, array $options = array())
    {
        $options = array_merge(array(
            'attributes'    => array(),
            'query'         => array(),
            'ignore_errors' => !$this->container->getParameter('kernel.debug'),
            'alt'           => array(),
            'standalone'    => false,
            'comment'       => '',
        ), $options);
        if (!is_array($options['alt'])) {
            $options['alt'] = array($options['alt']);
        }
        if (null === $this->esiSupport) {
            $this->esiSupport = $this->container->has('esi') && $this->container->get('esi')->hasSurrogateEsiCapability($this->container->get('request'));
        }
        if ($this->esiSupport && $options['standalone']) {
            $uri = $this->generateInternalUri($controller, $options['attributes'], $options['query']);
            $alt = '';
            if ($options['alt']) {
                $alt = $this->generateInternalUri($options['alt'][0], isset($options['alt'][1]) ? $options['alt'][1] : array(), isset($options['alt'][2]) ? $options['alt'][2] : array());
            }
            return $this->container->get('esi')->renderIncludeTag($uri, $alt, $options['ignore_errors'], $options['comment']);
        }
        $request = $this->container->get('request');
                if (0 === strpos($controller, '/')) {
            $subRequest = Request::create($controller, 'get', array(), $request->cookies->all(), array(), $request->server->all());
            $subRequest->setSession($request->getSession());
        } else {
            $options['attributes']['_controller'] = $controller;
            $options['attributes']['_format'] = $request->getRequestFormat();
            $subRequest = $request->duplicate($options['query'], null, $options['attributes']);
        }
        try {
            $response = $this->container->get('kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true);
            if (200 != $response->getStatusCode()) {
                throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $request->getUri(), $response->getStatusCode()));
            }
            return $response->getContent();
        } catch (\Exception $e) {
            if ($options['alt']) {
                $alt = $options['alt'];
                unset($options['alt']);
                $options['attributes'] = isset($alt[1]) ? $alt[1] : array();
                $options['query'] = isset($alt[2]) ? $alt[2] : array();
                return $this->render($alt[0], $options);
            }
            if (!$options['ignore_errors']) {
                throw $e;
            }
        }
    }
    public function generateInternalUri($controller, array $attributes = array(), array $query = array())
    {
        if (0 === strpos($controller, '/')) {
            return $controller;
        }
        $uri = $this->container->get('router')->generate('_internal', array(
            'controller' => $controller,
            'path'       => $attributes ? http_build_query($attributes) : 'none',
            '_format'    => $this->container->get('request')->getRequestFormat(),
        ), true);
        if ($query) {
            $uri = $uri.'?'.http_build_query($query);
        }
        return $uri;
    }
}
namespace Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAware;
class Controller extends ContainerAware
{
    public function createResponse($content = '', $status = 200, array $headers = array())
    {
        $response = $this->container->get('response');
        $response->setContent($content);
        $response->setStatusCode($status);
        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }
        return $response;
    }
    public function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }
    public function forward($controller, array $path = array(), array $query = array())
    {
        return $this->container->get('controller_resolver')->forward($controller, $path, $query);
    }
    public function redirect($url, $status = 302)
    {
        $response = $this->container->get('response');
        $response->setRedirect($url, $status);
        return $response;
    }
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }
    public function render($view, array $parameters = array(), Response $response = null)
    {
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }
    public function has($id)
    {
        return $this->container->has($id);
    }
    public function get($id)
    {
        return $this->container->get($id);
    }
}
namespace Symfony\Component\EventDispatcher;
class Event
{
    protected $value = null;
    protected $processed = false;
    protected $subject;
    protected $name;
    protected $parameters;
    public function __construct($subject, $name, $parameters = array())
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->parameters = $parameters;
    }
    public function getSubject()
    {
        return $this->subject;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setReturnValue($value)
    {
        $this->value = $value;
    }
    public function getReturnValue()
    {
        return $this->value;
    }
    public function setProcessed($processed)
    {
        $this->processed = (boolean) $processed;
    }
    public function isProcessed()
    {
        return $this->processed;
    }
    public function all()
    {
        return $this->parameters;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }
    public function get($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
        }
        return $this->parameters[$name];
    }
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
namespace Symfony\Component\EventDispatcher;
class EventDispatcher
{
    protected $listeners = array();
    public function connect($name, $listener, $priority = 0)
    {
        if (!isset($this->listeners[$name][$priority])) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = array();
            }
            $this->listeners[$name][$priority] = array();
        }
        $this->listeners[$name][$priority][] = $listener;
    }
    public function disconnect($name, $listener = null)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }
        if (null === $listener) {
            unset($this->listeners[$name]);
            return;
        }
        foreach ($this->listeners[$name] as $priority => $callables) {
            foreach ($callables as $i => $callable) {
                if ($listener === $callable) {
                    unset($this->listeners[$name][$priority][$i]);
                }
            }
        }
    }
    public function notify(Event $event)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            call_user_func($listener, $event);
        }
        return $event;
    }
    public function notifyUntil(Event $event)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            if (call_user_func($listener, $event)) {
                $event->setProcessed(true);
                break;
            }
        }
        return $event;
    }
    public function filter(Event $event, $value)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            $value = call_user_func($listener, $event, $value);
        }
        $event->setReturnValue($value);
        return $event;
    }
    public function hasListeners($name)
    {
        return (Boolean) count($this->getListeners($name));
    }
    public function getListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            return array();
        }
        $listeners = array();
        $all = $this->listeners[$name];
        krsort($all);
        foreach ($all as $l) {
            $listeners = array_merge($listeners, $l);
        }
        return $listeners;
    }
}
namespace Symfony\Bundle\FrameworkBundle;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
class EventDispatcher extends BaseEventDispatcher
{
    public function registerKernelListeners(array $kernelListeners)
    {
        foreach ($kernelListeners as $priority => $listeners) {
            foreach ($listeners as $listener) {
                $listener->register($this, $priority);
            }
        }
    }
}
namespace Symfony\Component\Form;
class FormConfiguration
{
    protected static $defaultCsrfSecrets = array();
    protected static $defaultCsrfProtection = false;
    protected static $defaultCsrfFieldName = '_token';
    protected static $defaultLocale = null;
    static public function setDefaultLocale($defaultLocale)
    {
        self::$defaultLocale = $defaultLocale;
    }
    static public function getDefaultLocale()
    {
        return isset(self::$defaultLocale) ? self::$defaultLocale : \Locale::getDefault();
    }
    static public function enableDefaultCsrfProtection()
    {
        self::$defaultCsrfProtection = true;
    }
    static public function isDefaultCsrfProtectionEnabled()
    {
        return self::$defaultCsrfProtection;
    }
    static public function disableDefaultCsrfProtection()
    {
        self::$defaultCsrfProtection = false;
    }
    static public function setDefaultCsrfFieldName($name)
    {
        self::$defaultCsrfFieldName = $name;
    }
    static public function getDefaultCsrfFieldName()
    {
        return self::$defaultCsrfFieldName;
    }
    static public function setDefaultCsrfSecrets(array $secrets)
    {
        self::$defaultCsrfSecrets = $secrets;
    }
    static public function addDefaultCsrfSecret($secret)
    {
        self::$defaultCsrfSecrets[] = $secret;
    }
    static public function clearDefaultCsrfSecrets()
    {
        self::$defaultCsrfSecrets = array();
    }
    static public function getDefaultCsrfSecrets()
    {
        return self::$defaultCsrfSecrets;
    }
}