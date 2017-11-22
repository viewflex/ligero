<?php

namespace Viewflex\Ligero\Utility;

/**
 * Methods for generating publisher display and UI elements using bootstrap.css.
 */
trait BootstrapUiTrait
{
    /**
     * Returns a keyword search form.
     *
     * @return string
     */
    public function keywordSearch()
    {
        $keyword_search = $this->publisher->getKeywordSearch();
        
        $ks['scope'] = $keyword_search['config']['scope'];
        $ks['persist_sort'] = $keyword_search['config']['persist_sort'];
        $ks['persist_view'] = $keyword_search['config']['persist_view'];
        $ks['persist_input'] = $keyword_search['config']['persist_input'];
        $ks['route'] = $keyword_search['route'];
        $ks['inputs'] = $keyword_search['base_parameters'];
        $ks['keyword'] = $keyword_search['keyword'];

        $ks['label'] = $ks['persist_input'] ? $ks['keyword'] : $keyword_search['label_search'];
        $ks['value'] = $ks['persist_input'] ? $ks['keyword'] : '';
        
        $html = "<form class=\"navbar-form navbar-left\" method=\"get\" action=\"".$ks['route']."\" name=\"fKeywordQuery\">";

        foreach($ks['inputs'] as $key => $value) {
            $html .= "\n        <input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }

        $html .= "\n       <input type=\"search\" class=\"form-control\" name=\"keyword\" placeholder=\""
            .$ks['label']."\" value=\"" .$ks['value']."\" onchange=\"submit\">";
        $html .= "\n   </form>";
        
        return $html;
    }

    /**
     * Returns a simple prev/next pager.
     *
     * @return string
     */
    public function pager()
    {
        $pager = $this->publisher->getPagination()['pager'];
        $html = "\n<nav>\n    <ul class=\"pager\">";

        $html .= "\n        <li class=\"previous".
            ($pager['pages']['prev'] !== null ? '' : ' disabled').
            "\"><a href=\"".($pager['pages']['prev'] ? : '#').
            "\"><span aria-hidden=\"true\">&larr;</span> ".
            $this->config->ls('ui.link_results_previous')."</a></li>";

        $html .= "\n        <li class=\"next".
            ($pager['pages']['next'] !== null ? '' : ' disabled').
            "\"><a href=\"".($pager['pages']['next'] ? : '#').
            "\">".$this->config->ls('ui.link_results_next').
            " <span aria-hidden=\"true\">&rarr;</span></a></li>";

        $html .= "\n    </ul>\n</nav>\n\n";

        return $html;
    }

    /**
     * Returns a page selector with prev and next buttons,
     * number of pages based on config and item count.
     *
     * @return string
     */
    public function pageNav()
    {
        $pager = $this->publisher->getPagination()['pager'];
        $html = "\n<nav>\n    <ul class=\"pagination\">";

        if ($pager['pages']['prev']) {
            $html .= "\n        <li><a href=\"".$pager['pages']['prev'].
                "\" aria-label=\"".$this->config->ls('ui.link_results_previous').
                "\"><span aria-hidden=\"true\">&laquo;</span></a><li>";
        } else
            $html .= "\n        <li class=\"disabled\"><span><span aria-hidden=\"true\">&laquo;</span></span></li>";

        $pages = $this->publisher->getPagination()['page_menu']['pages'];
        foreach($pages as $page_num => $data) {
            if ($data['url']) // not the current page
                $html .= "\n        <li><a href=\"".$data['url']."\">".$page_num."</a></li>";
            else
                $html .= "\n        <li class=\"active\"><span>".$page_num."<span class=\"sr-only\">(current)</span></span></li>";
        }

        if ($pager['pages']['next']) {
            $html .= "\n        <li><a href=\"".$pager['pages']['next'].
                "\" aria-label=\"".$this->config->ls('ui.link_results_next').
                "\"><span aria-hidden=\"true\">&raquo;</span></a><li>";
        } else
            $html .= "\n        <li class=\"disabled\"><span><span aria-hidden=\"true\">&raquo;</span></span></li>";

        $html .= "\n    </ul>\n</nav>\n\n";

        return $html;
    }

    /**
     * Returns a view mode change menu as Bootstrap dropdown.
     *
     * @return string
     */
    public function viewMenu()
    {
        $data = $this->publisher->getPagination()['view_menu'];
        $button_label = $data['label_view_as'].' '.$data['views'][$data['selected']]['display'];

        $html = "\n    <li class=\"dropdown\">";

        $html .= "\n        <a href=\"\" class=\"dropdown-toggle\" role=\"button\" id=\"viewMenu".
            "\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\">".
            $button_label." <span class=\"caret\"></span></button>";

        $html .= "\n        <ul class=\"dropdown-menu\" aria-labelledby=\"viewMenu\">";


        foreach ($data['views'] as $name => $view) {
            $html .= "\n            ".($name == $data['selected'] ? "<li class=\"disabled\">" : "<li>").
                "<a href=\"".($name == $data['selected'] ? "#" : $view['url'])."\">".$view['display'].' ('.$view['limit'].')'."</a></li>";
        }


        $html .= "\n        </ul>\n    </li>\n";

        return $html;
    }

}
