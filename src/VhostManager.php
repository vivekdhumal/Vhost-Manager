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

        $this->config = require 'src/config.php';
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

        if ($static->domainExists($domain) !== false) {
            throw new \Exception("The domain {$domain} is already exists, please try different");
        }

        $documentRoot = trim($static->config['workspace_path'], '/') . '/'. $documentRoot;

        if ($static->filesystem->exists($documentRoot)) {
            $apacheVhostsTags = $static->getApacheVhostsTags($documentRoot, $domain);

            $apacheHostEdited = (bool) $static->filesystem->append(
                $static->config['apache_host_path'],
                $apacheVhostsTags
            );

            $hostEdited = (bool) $static->filesystem->append(
                $static->config['windows_host_path'],
                "\n127.0.0.1       {$domain}"
            );

            return ($apacheHostEdited && $hostEdited);
        } else {
            throw new FileNotFoundException("File does not exist at path {$documentRoot}");
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
     * Generate the host array.
     *
     * @param  string  $content
     * @return array
     */
    protected function generateHostArray($content)
    {
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
                    ['"', '/'],
                    ['', '\\'],
                    trim(strtolower(strip_tags(isset($contentArray[$key+1]) ? $contentArray[$key+1] : '')))
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
