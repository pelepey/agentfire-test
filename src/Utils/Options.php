<?php


namespace AgentFire\Plugin\Utils;


use AgentFire\Plugin\Test\Traits\Singleton;

class Options
{
    use Singleton;

    public function getMapPageSlug()
    {
        $slug = \get_field('at_map_page_slug', 'option');

        if (!$slug) {
            $slug = 'map';
        }

        return $slug;
    }

    public function getPageTemplate()
    {
        $templates = \get_field('at_map_page_template', 'option');

        return $templates;
    }

    public function getAvailableTags()
    {
        $tags = \get_field('at_available_tags', 'option');

        if ($tags) {
            $tags = explode($tags, PHP_EOL);
        } else {
            $tags = [];
        }

        return $tags;
    }

    public function getMapboxToken()
    {
        $token = \get_field('at_mapbox_token', 'option');

        return $token;
    }
}