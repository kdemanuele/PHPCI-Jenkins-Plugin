<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Clounce.com
 * @license      https://github.com/kdemanuele/PHPCI-Jenkins-Plugin/blob/master/LICENSE
 * @link         https://www.clounce.com/
 */

namespace PHPCI\Plugin;

use PHPCI;
use PHPCI\Builder;
use PHPCI\Model\Build;

/**
* Jenkins trigger for Builds
*
* Extension assumes that Jenkins doesn't require authentication to trigger a build process
*
* @author       Karlston D'Emanuele (kd@clounce.com)
* @package      PHPCI
* @subpackage   Plugins
*/
class Jenkins implements PHPCI\Plugin
{
    /**
     * @var \PHPCI\Builder
     */
    protected $phpci;

    /**
     * @var String
     */
    protected $jenkinsUrl;

    /**
     * @var String
     */
    protected $jenkinsProject;

    /**
     * @var String
     */
    protected $jenkinsToken;

    /**
     * @param \Psr\Log\LoggerInterface $log
     * @param \PHPCI\Builder $phpci
     * @param \PHPCI\Model\Build $build
     * @param array $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = array())
    {
        $this->phpci = $phpci;
        $this->build = $build;
        $this->directory = $phpci->buildPath;
        $this->standard = 'PSR2';
        $this->jenkinsUrl = '';
        $this->jenkinsProject = '';
        $this->jenkinsToken = null;

        $this->setOptions($options);
    }

    /**
     * Handle this plugin's options.
     * @param $options
     */
    protected function setOptions($options)
    {
        foreach (array('url', 'project', 'token') as $key) {
            if (array_key_exists($key, $options)) {
                $prop = 'jenkins' . ucfirst($key);
                $this->{$prop} = $options[$key];
            }
        }
    }

    /**
    * Triggers Jenkins Build for the project
    */
    public function execute()
    {
        $success = true;
        if ($this->build->isSuccessful()) {
            // Builds the Jenkins trigger
            $jenkins = $this->jenkinsUrl . '/job/' . rawurlencode($this->jenkinsProject) . '/build?delay=0sec';
            if ($this->jenkinsToken && !empty($this->jenkinsToken)) {
                $jenkins .= '&token=%s';
            }
            $jenkins = sprintf($jenkins, rawurlencode($this->jenkinsProject), $this->jenkinsToken);

            $this->phpci->log($jenkins);
            $curlHandler = curl_init($jenkins);
	        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($curlHandler);
            $status = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
            $curlUrl = curl_getinfo($curlHandler, CURLINFO_EFFECTIVE_URL);
            curl_close($curlHandler);

            if ($status != '200') {
               $this->phpci->logFailure($curlUrl . ' return with status code ' . $status);
               return false;
            }
        } else {
            $this->phpci->log('Skipping due to failed Build');
        }

        return $success;
    }
}
