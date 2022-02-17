<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Helper;

use Contao\System;
use Contao\Model;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\Template;
use Contao\Controller;
use Doctrine\DBAL\Connection;
use Contao\CoreBundle\Framework\ContaoFramework;

class Testimonial
{
    private ContaoFramework $framework;
    private Connection $connection;

    public function __construct(
        ContaoFramework $framework,
        Connection $connection
    ) {
        $this->framework = $framework;
        $this->connection = $connection;
    }

    public function getTestimonialById(int $id): ?array
    {
        $testimonial = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, addImage, singleSRC')
            ->from('tl_testimonials')
            ->where('id=:id')
            ->andWhere('published=:published')
            ->setParameter('id', $id)
            ->setParameter('published', 1)
            ->execute()
            ->fetch()
        ;

        if (false !== $testimonial) {
            return $testimonial;
        }

        return null;
    }

    public function getTestimonialsByArchive(int $pid, int $limit, bool $random = false): ?array
    {
        $testimonials = $this->connection
            ->createQueryBuilder()
            ->select('name, company, department, testimonial, addImage, singleSRC')
            ->from('tl_testimonials')
            ->where('pid=:pid')
            ->andWhere('published=:published')
            ->setParameter('pid', $pid)
            ->setParameter('published', 1)
            ;

        if (0 !== $limit) {
            $testimonials->setMaxResults($limit);
        }

        if (true === $random) {
            $testimonials->orderBy('RAND()');
        }

        $result = $testimonials->execute()->fetchAllAssociative();

        if (false !== $result) {
            return $result;
        }

        return null;
    }

    public function addImageToTemplate(Template $template, Model $model, string $singleSRC): void
    {
        $fileAdapter = $this->framework->getAdapter(FilesModel::class);
        $systemAdapter = $this->framework->getAdapter(System::class);
        $controllerAdapter = $this->framework->getAdapter(Controller::class);

        $objModel = $fileAdapter->findByUuid($singleSRC);

        if (null !== $objModel && is_file(
                $systemAdapter->getContainer()->getParameter('kernel.project_dir').'/'.$objModel->path)
        ) {
            $template->addImage = true;

            $arrData = [
                'singleSRC' => $objModel->path,
                'size' => $model->size,
                'floating' => $model->floating,
            ];

            $controllerAdapter->addImageToTemplate($template, $arrData, null, null, $objModel);
        }
    }
}