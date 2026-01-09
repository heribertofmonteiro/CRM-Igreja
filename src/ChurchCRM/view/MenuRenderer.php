<?php

namespace ChurchCRM\view;

use ChurchCRM\Config\Menu\MenuImproved;
use ChurchCRM\Config\Menu\MenuItem;

class MenuRendererImprovedImproved
{
    public static function renderMenu(): void
    {
        MenuImproved::init();
        echo '<nav class="mt-2">';
        echo '<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">';
        
        foreach (MenuImproved::getMenu() as $menuItem) {
            if ($menuItem->isVisible()) {
                if (!$menuItem->isMenu()) {
                    self::renderMenuItem($menuItem);
                } else {
                    self::renderSubMenuItem($menuItem);
                }
            }
        }
        
        echo '</ul>';
        echo '</nav>';
    }

    private static function renderMenuItem(MenuItem $menuItem): void
    {
        $isActive = $menuItem->isActive();
        $isExternal = $menuItem->isExternal();
        $uri = $menuItem->getURI();
        $name = htmlspecialchars($menuItem->getName());
        $icon = $menuItem->getIcon();
        
        ?>
        <li class="nav-item">
            <a href="<?= $uri ?>" <?= $isExternal ? "target='_blank' rel='noopener noreferrer'" : "" ?> 
               class="nav-link <?= $isActive ? "active" : "" ?>">
                <i class="<?= $icon ?> nav-icon"></i>
                <p>
                    <?= $name ?>
                    <span class="right">
                        <?php self::renderMenuCounters($menuItem) ?>
                    </span>
                </p>
            </a>
        </li>
        <?php
    }

    private static function renderSubMenuItem(MenuItem $menuItem): void
    {
        $isOpen = $menuItem->openMenu();
        $name = htmlspecialchars($menuItem->getName());
        $icon = $menuItem->getIcon();
        
        ?>
        <li class="nav-item <?= $isOpen ? "menu-open" : "" ?>">
            <a href="#" class="nav-link">
                <i class="<?= $icon ?> nav-icon"></i>
                <p>
                    <?= $name ?>
                    <span class="right">
                        <?php self::renderMenuCounters($menuItem) ?>
                        <i class="fas fa-angle-left"></i>
                    </span>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <?php 
                foreach ($menuItem->getSubItems() as $menuSubItem) {
                    if ($menuSubItem->isVisible()) {
                        if ($menuSubItem->isMenu()) {
                            self::renderSubMenuItem($menuSubItem);
                        } else {
                            self::renderMenuItem($menuSubItem);
                        }
                    }
                } 
                ?>
            </ul>
        </li>
        <?php
    }

    private static function renderMenuCounters(MenuItem $menuItem): void
    {
        if ($menuItem->hasCounters()) {
            foreach ($menuItem->getCounters() as $counter) {
                $cssClass = $counter->getCss();
                $name = $counter->getName();
                $title = $counter->getTitle() ? "title='{$counter->getTitle()}'" : "";
                $value = $counter->getInitValue();
                
                echo "<small class='badge {$cssClass}' id='{$name}' {$title}>{$value}</small>";
            }
        }
    }
}
