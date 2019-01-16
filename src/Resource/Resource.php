<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/14/2019
 * Time: 9:52 AM
 */

namespace MkOrm\Resource;


class Resource
{
    protected function getName()
    {
        $name = substr(get_called_class(), 0, -8);
        return lcfirst(substr($name, strrpos($name, '\\') + 1));
    }

    protected function links($meta, $uri = '')
    {
        $link = $this->data[0]['type'];
        $first = "$uri/$link";

        $path = $first . 'page=' . $meta['current_page'];
        if ($meta['current_page'] == 1) {
            $path = $first;
        }

        $prev = $first . '?page=' . ($meta['current_page'] - 1);
        if (($meta['current_page'] - 1) < 1) {
            $prev = null;
        } elseif (($meta['current_page'] - 1) == 1) {
            $prev = $first;
        }

        $next = $first . '?page=' . ($meta['current_page'] + 1);
        if (($meta['current_page'] + 1) > $meta['last_page']) {
            $next = null;
        }

        $last = $first . '?page=' . $meta['last_page'];
        if ($meta['last_page'] == 1) {
            $last = $first;
        }

        $this->links = [
            "self"  => $path,
            'first' => $first,
            'last'  => $last,
            'prev'  => $prev,
            'next'  => $next,
        ];
    }

    protected function meta($meta, $uri = "")
    {
        $link = $this->data[0]['type'];
        $first = $uri . "/$link";
        $path = $first . 'page=' . $meta['current_page'];
        if ($meta['current_page'] == 1) {
            $path = $first;
        }

        $this->meta = [
            'current_page' => $meta['current_page'],
            'from'         => $meta['from'],
            'last_page'    => $meta['last_page'],
            'path'         => $path,
            "per_page"     => $meta['per_page'],
            "to"           => $meta['to'],
            "total"        => (int)$meta['total']
        ];
    }
}
