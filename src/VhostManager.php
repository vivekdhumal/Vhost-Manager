<?php

namespace Vhost;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class VhostManager
{
    /**
     * Create an instance of a filesystem.
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new config instance.
     *
     * @var Config;
     */
    protected $config;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem;

        $this->config = require __DIR__.'/config.php';
    }

    /**
     * Creates a virtual host.
     *
     * @param   string  $documentRoot
     * @param   string  $domain
     * @throws  \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return  bool
     */
    public static function createHost($documentRoot, $domain)
    {
        $static = new static;

        if ($static->domainExists($domain) !== false && $static->getVhostFromDomain($domain) !== false) {
            throw new \Exception("The domain {$domain} is already exists, please try different");
        }

        $documentRoot = str_replace("\\", "/", trim($static->config['workspace_path'], '/')) . '/'. $documentRoot;

        if ($static->filesystem->exists($documentRoot)) {
            $apacheHostEdited = $static->appendFileContent(
                $static->config['apache_host_path'],
                $static->getApacheVhostsTags($documentRoot, $domain)
            );

            $hostEdited = $static->appendFileContent(
                $static->config['windows_host_path'],
                "\n{$static->config['local_ip']}       {$domain}"
            );

            return ($apacheHostEdited && $hostEdited);
        } else {
            throw new FileNotFoundException("Directory does not exist at path {$documentRoot}");
        }
    }

    /**
     * Get the hosts.
     *
     * @return array
     */
    public static function getHosts()
    {
        $static = new static;

        $hosts = $static->generateHostArray(
            $static->filesystem->get($static->config['apache_host_path'])
        );

        return $hosts;
    }

    /**
     * Removes a host.
     *
     * @param   string  $domain
     * @throws  \Exception
     * @return bool
     */
    public static function removeHost($domain)
    {
        $static = new static;

        $virtualHost = $static->getVhostFromDomain($domain);

        if ($static->domainExists($domain) === false && $virtualHost === false) {
            throw new \Exception("The domain {$domain} does not exist.");
        }

        // Remove from windows host file.
        $removedFromWindowsHosts = $static->replaceFileContent(
            "{$static->config['local_ip']}       {$domain}",
            "",
            $static->config['windows_host_path']
        );

        // Remove from apache httpd-vhosts.conf
        $removedFromhttpdVhost = $static->replaceFileContent(
            trim($static->getApacheVhostsTags($virtualHost['document_root'], $virtualHost['server_name'])),
            "",
            $static->config['apache_host_path']
        );

        return $removedFromWindowsHosts && $removedFromhttpdVhost;
    }

    /**
     * Append the given file content.
     *
     * @param string $file
     * @param string $content
     * @return bool
     */
    protected function appendFileContent($file, $content)
    {
        return (bool) $this->filesystem->append($file, $content);
    }

    /**
     * Replace the given file content.
     *
     * @param   string  $search
     * @param   string  $replace
     * @param   string  $path
     * @return  bool
     */
    protected function replaceFileContent($search, $replace, $path)
    {
        return (bool) $this->filesystem->put(
            $path,
            str_replace(
                $search,
                $replace,
                $this->filesystem->get($path)
            )
        );
    }

    /**
     * Gets the virtual host from domain.
     *
     * @param   string $domain
     * @return  bool|Array
     */
    protected function getVhostFromDomain($domain)
    {
        $hosts = $this->getHosts();

        $virtualHost = current(
            array_filter(
                $hosts,
                function ($item) use ($domain) {
                    return (isset($item['server_name']) && $item['server_name'] === $domain);
                }
            )
        );

        return $virtualHost;
    }

    /**
     * Generate the host array.
     *
     * @param  string  $content
     * @return array
     */
    protected function generateHostArray($content)
    {
        $content = trim(preg_replace(["/(# .*)/", "/(##.*)/", "/(#)/"], "", $content));

        $contentArray = explode(' ', $content);

        $hostAttributes = [
            'documentroot' => 'document_root',
            'servername' => 'server_name'
        ];

        $hostArray = [];
        $arrayIterator = 0;

        foreach ($contentArray as $key => $value) {
            $attribute = trim(strtolower(strip_tags($value)));

            if (array_key_exists($attribute, $hostAttributes)) {
                $hostArray[$arrayIterator][$hostAttributes[$attribute]] = str_replace(
                    ['"'],
                    [''],
                    trim(strip_tags(isset($contentArray[$key+1]) ? $contentArray[$key+1] : ''))
                );

                if ($attribute === 'servername') {
                    $arrayIterator += 1;
                }
            }
        }

        return $hostArray;
    }

    /**
     * Gets the apache vhosts tags from the stub.
     *
     * @param   string  $documentRoot
     * @param   string  $domain
     * @return  string
     */
    protected function getApacheVhostsTags($documentRoot, $domain)
    {
        return str_replace(
            [
                '{{documentRoot}}',
                '{{domain}}',
            ],
            [
                $documentRoot,
                $domain
            ],
            $this->filesystem->get(__DIR__.'/stubs/httpd-vhosts.stub')
        );
    }

    /**
     * Check if the domain is already exists in the host file.
     *
     * @param   string  $domain
     * @return  int/bool
     */
    protected function domainExists($domain)
    {
        return strpos(
            $this->filesystem->get($this->config['windows_host_path']),
            $domain
        );
    }
}
