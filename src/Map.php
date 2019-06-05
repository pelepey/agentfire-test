<?php


namespace AgentFire\Plugin;


use AgentFire\Plugin\Test\Template;
use AgentFire\Plugin\Test\Traits\Singleton;
use AgentFire\Plugin\Utils\Options;

class Map
{
    use Singleton;

    public function addHooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('agentfire_test', [$this, 'renderShortcode']);
    }

    /**
     * @return void
     */
    public function enqueueScripts()
    {
        $base = plugin_dir_url(AGENTFIRE_TEST_FILE);
        $base = rtrim($base, '/');
        $baseBootstrap = $base . '/bower_components/bootstrap/dist';
        $mapBoxBase = $base . '/bower_components/mapbox.js/';

        wp_enqueue_style('bootstrap', $baseBootstrap . '/css/bootstrap.min.css', [], '');
        wp_enqueue_script('bootstrap', $baseBootstrap . '/js/bootstrap.min.js', ['jquery'], '', true);

        wp_enqueue_style('mapbox', $mapBoxBase . '/mapbox.css', [], '');
        wp_enqueue_script('mapbox', $mapBoxBase . '/mapbox.js', ['jquery'], '', true);

        wp_enqueue_script('maps-main', $base . '/assets/main.js', ['jquery'], '', true);

        wp_localize_script('maps-main', 'agentfire', [
            'mapsToken' => Options::getInstance()->getMapboxToken()
        ]);
    }

    /**
     * @return string
     */
    public function renderShortcode()
    {
        $content = '';

        try {
            $content = Template::getInstance()->render('map-shortcode.twig');
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $content;
    }
}