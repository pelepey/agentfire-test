<?php


namespace AgentFire\Plugin;


use AgentFire\Plugin\Test\Template;
use AgentFire\Plugin\Test\Traits\Singleton;
use AgentFire\Plugin\Utils\Options;

class MapPage
{
    use Singleton;

    public function addHooks()
    {
        add_filter('init', [$this, 'addRewriteRule'], 10, 1);
        add_filter('query_vars', [$this, 'addPublicQv'], 10, 1);
        add_filter('posts_pre_query', [$this, 'postsFilter'], 10, 2);
//        add_filter('posts_results', [$this, 'filterPostsResults'], 10, 2);
        add_filter('template_include', [$this, 'filterTemplate'], 10, 1);
        add_action('template_redirect', [$this, 'applyBuffering']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts()
    {
        if (!$this->isTheMapPage()) {
            return;
        }

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

    public function applyBuffering() {
        if ( !$this->isTheMapPage() ) {
            return;
        }

        add_action('get_header', [$this, 'startBuffering']);
        add_action('get_footer', [$this, 'injectContentInBuffer']);
    }

    public function startBuffering()
    {
        ob_start();
    }

    public function injectContentInBuffer()
    {
        $content = ob_get_clean();

        $mapContent = Template::getInstance()->render('main.twig', [
            'tags' => Options::getInstance()->getAvailableTags()
        ]);

        $content = $this->injectContentIntoMainTag($content, $mapContent);

        echo $content;
    }

    public function injectContentIntoMainTag($content, $injection)
    {
        $content = preg_replace('/(<main[^\/]*?>).*?(<\/main>)/s', '$1' . $injection . '$2', $content);

        return $content;
    }

    public function getQueryVariable()
    {
        return "at-map-page";
    }

    public function addPublicQv($vars)
    {
        array_push($vars, $this->getQueryVariable());

        return $vars;
    }

    public function addRewriteRule()
    {
        $regexp = $this->getPageRegexp();
        $qv = $this->getQueryVariable();
        $query_string = "index.php?$qv=$qv";

        add_rewrite_rule($regexp, $query_string, "top");
    }

    public function getPageRegexp()
    {
        $slug = Options::getInstance()->getMapPageSlug();

        $regexp = "^$slug/?$";

        return $regexp;
    }

    public function isTheMapPage()
    {
        global $wp_query;

        return $wp_query->is_main_query() && $this->isMapPageQuery($wp_query);
    }

    public function isMapPageQuery(\WP_Query $query)
    {
        return $query->get($this->getQueryVariable(), '') === $this->getQueryVariable();
    }

    /**
     * Hook that prevents query to the database if
     * the map page is queried
     *
     * @param $posts
     * @param $query
     *
     * @return array
     */
    public function postsFilter($posts, \WP_Query $query)
    {
        if ( $this->isMapPageQuery($query) ) {
            $posts = [];
        }

        return $posts;
    }

    public function filterTemplate($template) {
        if ( $this->isTheMapPage() ) {
            $templates = [];
            $templates[] = Options::getInstance()->getPageTemplate();
            $templates[] = 'index.php';

            $template = get_query_template( 'index', $templates );
        }

        return $template;
    }

    public function filterPostsResults($posts, $query)
    {
        if ( $this->isMapPageQuery($query) ) {
            $posts = [
                $this->getPostPlaceholder()
            ];
        }

        return $posts;
    }

    public function getTemplatePath()
    {
        return $this->getTmpBasePath() . 'tmp-map-' . Options::getInstance()->getPageTemplate();
    }

    protected function getPostPlaceholder()
    {
        $fakePost = new \stdClass();

        $fakePost->ID = 0;
        $fakePost->post_name = Options::getInstance()->getMapPageSlug();
        $fakePost->post_type = 'page';
        $fakePost->post_content = '';
        $fakePost->post_title = 'Map';
        $fakePost->post_parent = 0;

        $fakePost = new \WP_Post($fakePost);

        return $fakePost;
    }

    protected function getTmpBasePath() {
        $dir = '/tmp/';

        if ( is_dir($dir) ) {
            return $dir;
        }

        $dir = WP_CONTENT_DIR . "/tmp-agentfire-test/";

        if ( !is_dir($dir) ) {
            mkdir($dir);
        }

        return $dir;
    }
}