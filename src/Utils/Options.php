<?php


namespace AgentFire\Plugin\Utils;


use AgentFire\Plugin\Test\Traits\Singleton;

class Options
{
    use Singleton;

    /**
     * @return string
     */
    public function getMapPageSlug()
    {
        $slug = \get_field('at_map_page_slug', 'option');

        if (!$slug) {
            $slug = 'map';
        }

        return $slug;
    }

    /**
     * @return string
     */
    public function getPageTemplate()
    {
        $template = \get_field('at_map_page_template', 'option');

        return $template;
    }

    /**
     * @return array
     */
    public function getAvailableTags()
    {
        $tags = \get_field('at_available_tags', 'option');

        if ($tags) {
            $tags = explode(PHP_EOL, $tags);
        } else {
            $tags = [];
        }

        return $tags;
    }

    /**
     * @return string
     */
    public function getMapboxToken()
    {
        $token = \get_field('at_mapbox_token', 'option');

        return $token;
    }

    /**
     * @return string
     */
    public function getTplBaseUrl()
    {
        return plugin_dir_url(AGENTFIRE_TEST_FILE) . 'template/test/';
    }
}