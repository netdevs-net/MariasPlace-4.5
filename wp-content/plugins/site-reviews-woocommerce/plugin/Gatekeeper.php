<?php
/**
 * @version 2.5
 */

namespace GeminiLabs\SiteReviews\Addon\Woocommerce;

class Gatekeeper
{
    /**
     * @var string
     */
    public $addonName;

    /**
     * @var array
     */
    public $dependencies;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var Notice
     */
    protected $notice;

    /**
     * @var array
     */
    protected $noticeWhitelist;

    /**
     * @var bool
     */
    protected $useWhitelist;

    /**
     * @param string $file
     */
    public function __construct($file, array $dependencies = [], $useWhitelist = false)
    {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        $addon = get_file_data($file, [
            'id' => 'Text Domain',
            'name' => 'Plugin Name'
        ]);
        $this->addonName = $addon['name'];
        $this->dependencies = $this->parseDependencies($dependencies);
        $this->notice = new Notice();
        $this->noticeWhitelist = [
            'post_type=site-review' => filter_input(INPUT_SERVER, 'QUERY_STRING'),
            'wp-admin/plugins.php' => filter_input(INPUT_SERVER, 'PHP_SELF'),
        ];
        $this->useWhitelist = wp_validate_boolean($useWhitelist);
    }

    /**
     * @return void
     * @action current_screen
     */
    public function activatePlugin()
    {
        if ('activate' != filter_input(INPUT_GET, 'action')) {
            return;
        }
        $plugin = filter_input(INPUT_GET, 'plugin');
        check_admin_referer('activate-plugin_'.$plugin);
        $result = activate_plugin($plugin, '', is_network_admin(), true);
        if (is_wp_error($result)) {
            wp_die($result->get_error_message());
        }
        wp_safe_redirect(wp_get_referer());
        exit;
    }

    /**
     * Must be called before "admin_init".
     * @return bool
     */
    public function allows()
    {
        if ($this->hasPendingDependencies()) {
            $this->setNotice();
            return false;
        }
        return true;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function createDependenciesNotice()
    {
        if ($errors = $this->getDependencyErrors()) {
            $message = _nx_noop('%s requires the latest version of', '%s requires the latest version of the following plugins:', 'admin-text', 'site-reviews-woocommerce');
            $message = sprintf(translate_nooped_plural($message, count($errors), 'site-reviews-woocommerce'), '<strong>'.$this->addonName.'</strong>');
            $this->notice->addWarning([
                $message.' '.$this->buildPluginLinks($errors),
                $this->buildPluginActions($errors),
            ]);
        }
    }

    /**
     * @return void
     * @action admin_init
     */
    public function createUnsupportedNotice()
    {
        if ($errors = $this->getUnsupportedErrors()) {
            $message = _nx_noop('%s needs an update to work with', '%s needs an update to work with the following plugins:', 'admin-text', 'site-reviews-woocommerce');
            $message = sprintf(translate_nooped_plural($message, count($errors), 'site-reviews-woocommerce'), '<strong>'.$this->addonName.'</strong>');
            $this->notice->addError([
                $message.' '.$this->buildPluginLinks($errors),
            ]);
        }
    }

    /**
     * @return void
     * @action admin_notices
     */
    public function displayNotice()
    {
        $this->notice->render();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return bool
     */
    public function hasPendingDependencies()
    {
        foreach ($this->dependencies as $plugin => $data) {
            if (!$this->isPluginInstalled($plugin)) {
                continue;
            }
            if (!$this->isPluginVersionSupported($plugin)) {
                continue;
            }
            if (!$this->isPluginVersionValid($plugin)) {
                continue;
            }
            $this->isPluginActive($plugin);
        }
        return $this->hasErrors();
    }

    /**
     * @return bool
     */
    public function isPluginActive($plugin)
    {
        $isActive = is_plugin_active($plugin) || array_key_exists($plugin, $this->getMustUsePlugins());
        return $this->catchError($plugin, 'inactive', $isActive);
    }

    /**
     * @return bool
     */
    public function isPluginInstalled($plugin)
    {
        $isInstalled = array_key_exists($plugin, $this->getPlugins());
        return $this->catchError($plugin, 'not_found', $isInstalled);
    }

    /**
     * @return bool
     */
    public function isPluginVersionSupported($plugin)
    {
        $unsupportedVersion = $this->getValue($this->dependencies, $plugin, 'UnsupportedVersion');
        $installedVersion = $this->getValue($this->getPlugins(), $plugin, 'Version');
        $isVersionValid = empty($unsupportedVersion) || version_compare($installedVersion, $unsupportedVersion, '<');
        return $this->catchError($plugin, 'unsupported_version', $isVersionValid);
    }

    /**
     * @return bool
     */
    public function isPluginVersionValid($plugin)
    {
        $requiredVersion = $this->getValue($this->dependencies, $plugin, 'Version');
        $installedVersion = $this->getValue($this->getPlugins(), $plugin, 'Version');
        $isVersionValid = version_compare($installedVersion, $requiredVersion, '>=');
        return $this->catchError($plugin, 'wrong_version', $isVersionValid);
    }

    /**
     * @param string $plugin
     * @return string
     */
    protected function buildActionForInactive($plugin)
    {
        if (!current_user_can('activate_plugins')) {
            return '';
        }
        $data = $this->getPluginData($plugin);
        $url = self_admin_url(sprintf('plugins.php?action=activate&plugin=%s&plugin_status=%s&paged=%s&s=%s',
            $data['plugin'],
            filter_input(INPUT_GET, 'plugin_status'),
            filter_input(INPUT_GET, 'paged'),
            filter_input(INPUT_GET, 's')
        ));
        $url = wp_nonce_url($url, 'activate-plugin_'.$data['plugin']);
        return $this->buildButton($url, __('Activate'), $data['name']);
    }

    /**
     * @param string $plugin
     * @return string
     */
    protected function buildActionForNotFound($plugin)
    {
        if (!current_user_can('install_plugins')) {
            return '';
        }
        $data = $this->getPluginData($plugin);
        $url = self_admin_url(sprintf('update.php?action=install-plugin&plugin=%s', $data['slug']));
        $url = wp_nonce_url($url, 'install-plugin_'.$data['slug']);
        return $this->buildButton($url, __('Install'), $data['name']);
    }

    /**
     * @param string $plugin
     * @return string
     */
    protected function buildActionForWrongVersion($plugin)
    {
        if (!current_user_can('update_plugins')) {
            return '';
        }
        $data = $this->getPluginData($plugin);
        $url = self_admin_url(sprintf('update.php?action=upgrade-plugin&plugin=%s', $data['plugin']));
        $url = wp_nonce_url($url, 'upgrade-plugin_'.$data['plugin']);
        return $this->buildButton($url, __('Update'), $data['name']);
    }

    /**
     * @param string $href
     * @param string $action
     * @param string $pluginName
     * @return string
     */
    protected function buildButton($href, $action, $pluginName)
    {
        return sprintf('<a href="%s" class="button button-small">%s %s</a>', $href, $action, $pluginName);
    }

    /**
     * @return string
     */
    protected function buildLink($plugin)
    {
        $data = $this->getPluginData($plugin);
        return sprintf('<span class="plugin-%s"><a href="%s">%s</a></span>',
            $data['slug'],
            $data['pluginuri'],
            $data['name']
        );
    }

    /**
     * @return string
     */
    protected function buildPluginActions(array $errors)
    {
        $actions = [];
        foreach ($errors as $plugin => $error) {
            $value = ucwords(str_replace(['-', '_'], ' ', $error));
            $value = str_replace(' ', '', $value);
            $method = 'buildActionFor'.$value;
            if (method_exists($this, $method)) {
                $actions[] = call_user_func([$this, $method], $plugin);
            }
        }
        return implode(' ', $actions);
    }

    /**
     * @return string
     */
    protected function buildPluginLinks(array $errors)
    {
        $plugins = array_keys($errors);
        array_walk($plugins, function (&$plugin) {
            $plugin = $this->buildLink($plugin);
        });
        return implode(', ', $plugins);
    }

    /**
     * @return bool
     */
    protected function canDisplayNotice()
    {
        if (!is_admin()) {
            return false;
        }
        if (!$this->useWhitelist) {
            return true;
        }
        foreach ($this->noticeWhitelist as $needle => $haystack) {
            if (false !== strpos($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $plugin
     * @param string $errorType
     * @param bool $isValidResult
     * @return bool
     */
    protected function catchError($plugin, $errorType, $isValidResult)
    {
        if (!$isValidResult) {
            $this->errors[$plugin] = $errorType;
        }
        return $isValidResult;
    }

    /**
     * @return array
     */
    protected function getDependencyErrors()
    {
        return array_filter($this->errors, function ($error) {
            return in_array($error, ['inactive', 'not_found', 'wrong_version']);
        });
    }

    /**
     * @return array
     */
    protected function getMustUsePlugins()
    {
        $plugins = get_mu_plugins();
        if (in_array('Bedrock Autoloader', array_column($plugins, 'Name'))) {
            $autoloadedPlugins = get_site_option('bedrock_autoloader');
            if (!empty($autoloadedPlugins['plugins'])) {
                return array_merge($plugins, $autoloadedPlugins['plugins']);
            }
        }
        return $plugins;
    }

    /**
     * @return array
     */
    protected function getPluginData($plugin)
    {
        $plugins = $this->isPluginInstalled($plugin)
            ? $this->getPlugins()
            : $this->dependencies;
        $data = $this->getValue($plugins, $plugin);
        if (!is_array($data)) {
            wp_die(sprintf('Plugin information not found for: %s', $plugin));
        }
        $data['plugin'] = $plugin;
        $data['slug'] = substr($plugin, 0, strrpos($plugin, '/'));
        return array_change_key_case($data);
    }

    /**
     * @return array
     */
    protected function getPlugins()
    {
        return array_merge(get_plugins(), $this->getMustUsePlugins());
    }

    /**
     * @return array
     */
    protected function getUnsupportedErrors()
    {
        return array_filter($this->errors, function ($error) {
            return 'unsupported_version' === $error;
        });
    }

    /**
     * @param string $slug
     * @param string $key
     * @return array|string
     */
    protected function getValue(array $data, $slug, $key = '')
    {
        $value = '';
        if (isset($data[$slug])) {
            $value = $data[$slug];
        }
        return !empty($key) && isset($value[$key])
            ? $value[$key]
            : $value;
    }

    /**
     * @return array
     */
    protected function parseDependencies(array $dependencies)
    {
        $keys = ['Name', 'Version', 'PluginURI', 'UnsupportedVersion'];
        $results = [];
        foreach ($dependencies as $plugin => $data) {
            $values = array_pad(explode('|', $data), 4, '');
            $results[$plugin] = array_combine($keys, $values);
        }
        return $results;
    }

    /**
     * @return void
     */
    protected function setNotice()
    {
        if (!$this->canDisplayNotice() || !$this->hasErrors()) {
            return;
        }
        add_action('current_screen', [$this, 'activatePlugin']);
        add_action('admin_init', [$this, 'createDependenciesNotice']);
        add_action('admin_init', [$this, 'createUnsupportedNotice']);
        add_action('admin_notices', [$this, 'displayNotice']);
    }
}
