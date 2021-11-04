<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\EventListener\Contao\Dca;

class TlTestimonialsListener
{
    public function listTestimonials(array $arrRow): string
    {
        return '<div class="tl_content_left">'.$arrRow['name'].'</div>';
    }
}
