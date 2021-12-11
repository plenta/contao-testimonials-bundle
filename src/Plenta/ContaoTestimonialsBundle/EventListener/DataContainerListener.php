<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\EventListener;

use Codefog\TagsBundle\Manager\ManagerInterface;
use Codefog\TagsBundle\Tag;
use Contao\Backend;
use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use Doctrine\DBAL\Connection;
use Haste\Model\Model;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Terminal42\NodeBundle\Model\NodeModel;
use Terminal42\NodeBundle\PermissionChecker;
use Terminal42\NodeBundle\Widget\NodePickerWidget;

class DataContainerListener
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function onTestimonialsOptionsCallback(): array
    {
        $arr = [];
        $nodes = $this->db->fetchAllAssociative("SELECT t.id AS id, t.name AS name, a.title AS parent FROM tl_testimonials AS t LEFT JOIN tl_testimonials_archive AS a ON t.pid = a.id WHERE t.published=1 ORDER BY t.name ASC");

        if (is_array($nodes)) {
            foreach ($nodes as $node) {
                $arr[$node['parent']][$node['id']] = $node['name'];
            }
        }

        return $arr;
    }
}